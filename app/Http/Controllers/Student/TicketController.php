<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        $tickets = Ticket::where('user_id', $student->id)
            ->withCount('replies')
            ->latest()
            ->paginate(10);

        return view('student.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('student.tickets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical,academic,financial,other',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return redirect()->route('student.tickets.show', $ticket)
            ->with('success', 'تم إنشاء التذكرة بنجاح. رقم التذكرة: #' . $ticket->id);
    }

    public function show(Ticket $ticket)
    {
        // Ensure student can only view their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies' => function($q) {
            $q->where('is_internal', false)->with('user');
        }]);

        return view('student.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Reopen ticket if it was resolved/closed
        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'تم إضافة ردك بنجاح');
    }
}
