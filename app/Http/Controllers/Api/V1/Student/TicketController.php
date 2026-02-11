<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * GET /api/v1/student/tickets
     * List student's tickets
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = Ticket::where('user_id', $student->id)
            ->withCount('replies');

        // Optional status filter
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        // Optional category filter
        if ($request->has('category')) {
            $query->where('category', $request->query('category'));
        }

        $tickets = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }

    /**
     * POST /api/v1/student/tickets
     * Create a new support ticket
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical,academic,financial,other',
            'priority' => 'required|in:low,medium,high',
            'description' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'description' => $validated['description'],
            'status' => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التذكرة بنجاح. رقم التذكرة: #' . $ticket->id,
            'data' => $ticket,
        ], 201);
    }

    /**
     * GET /api/v1/student/tickets/{id}
     * Show ticket details with replies
     */
    public function show($id)
    {
        $student = auth()->user();

        $ticket = Ticket::where('user_id', $student->id)->findOrFail($id);

        $ticket->load(['replies' => function ($q) {
            $q->where('is_internal_note_note', false)->with('user:id,name,role,profile_photo');
        }]);

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }

    /**
     * POST /api/v1/student/tickets/{id}/reply
     * Reply to a ticket
     */
    public function reply(Request $request, $id)
    {
        $student = auth()->user();

        $ticket = Ticket::where('user_id', $student->id)->findOrFail($id);

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal_note_note' => false,
        ]);

        // Reopen ticket if it was resolved/closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        $reply->load('user:id,name,role,profile_photo');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة ردك بنجاح',
            'data' => $reply,
        ]);
    }
}
