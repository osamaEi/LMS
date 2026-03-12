<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\TicketReplyResource;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * GET /api/v1/student/tickets
     * List student's tickets with stats summary.
     *
     * Query params: ?status=open|in_progress|resolved  ?category=technical|academic|financial|other  ?per_page=10
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = Ticket::where('user_id', $student->id)->withCount('replies');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        $tickets = $query->latest()->paginate($request->integer('per_page', 10));

        $stats = Ticket::where('user_id', $student->id)
            ->selectRaw("
                COUNT(*) as total,
                SUM(status = 'open') as open,
                SUM(status IN ('in_progress','waiting_response')) as in_progress,
                SUM(status IN ('resolved','closed')) as resolved
            ")
            ->first();

        return response()->json([
            'success' => true,
            'data'    => TicketResource::collection($tickets),
            'meta'    => [
                'current_page' => $tickets->currentPage(),
                'last_page'    => $tickets->lastPage(),
                'per_page'     => $tickets->perPage(),
                'total'        => $tickets->total(),
            ],
            'stats' => [
                'total'       => (int) $stats->total,
                'open'        => (int) $stats->open,
                'in_progress' => (int) $stats->in_progress,
                'resolved'    => (int) $stats->resolved,
            ],
        ]);
    }

    /**
     * POST /api/v1/student/tickets
     * Create a new support ticket (supports attachment upload).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|in:technical,academic,financial,account,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'description' => 'required|string',
            'attachment'  => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments', 'public');
        }

        $ticket = Ticket::create([
            'user_id'     => auth()->id(),
            'subject'     => $validated['subject'],
            'category'    => $validated['category'],
            'priority'    => $validated['priority'],
            'description' => $validated['description'],
            'attachment'  => $attachmentPath,
            'status'      => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء التذكرة بنجاح. رقم التذكرة: ' . $ticket->ticket_number,
            'data'    => new TicketResource($ticket),
        ], 201);
    }

    /**
     * GET /api/v1/student/tickets/{id}
     * Show ticket details with all replies.
     */
    public function show($id)
    {
        $ticket = Ticket::where('user_id', auth()->id())
            ->withCount('replies')
            ->findOrFail($id);

        $ticket->load(['replies' => function ($q) {
            $q->where('is_internal_note_note', false)
              ->with('user:id,name,role,profile_photo')
              ->orderBy('created_at', 'asc');
        }]);

        return response()->json([
            'success' => true,
            'data'    => new TicketResource($ticket),
        ]);
    }

    /**
     * POST /api/v1/student/tickets/{id}/reply
     * Add a reply to a ticket (supports attachment upload).
     */
    public function reply(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        if ($ticket->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن الرد على تذكرة مغلقة',
            ], 422);
        }

        $validated = $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('ticket-attachments', 'public');
        }

        $reply = TicketReply::create([
            'ticket_id'             => $ticket->id,
            'user_id'               => auth()->id(),
            'message'               => $validated['message'],
            'attachment'            => $attachmentPath,
            'is_internal_note_note' => false,
        ]);

        // Reopen ticket if resolved/closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        $reply->load('user:id,name,role,profile_photo');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة ردك بنجاح',
            'data'    => new TicketReplyResource($reply),
        ]);
    }

    /**
     * POST /api/v1/student/tickets/{id}/close
     * Student closes a resolved ticket.
     */
    public function close($id)
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        if ($ticket->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'التذكرة مغلقة بالفعل',
            ], 422);
        }

        $ticket->markAsClosed();

        return response()->json([
            'success' => true,
            'message' => 'تم إغلاق التذكرة بنجاح',
            'data'    => new TicketResource($ticket->fresh()),
        ]);
    }

    /**
     * POST /api/v1/student/tickets/{id}/rate
     * Student rates satisfaction after resolution (1-5 stars).
     */
    public function rate(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', auth()->id())->findOrFail($id);

        if (!$ticket->isResolved() && !$ticket->isClosed()) {
            return response()->json([
                'success' => false,
                'message' => 'يمكن التقييم فقط بعد حل التذكرة',
            ], 422);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $ticket->update(['satisfaction_rating' => $request->integer('rating')]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً على تقييمك',
            'data'    => ['satisfaction_rating' => $request->integer('rating')],
        ]);
    }
}
