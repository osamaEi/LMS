<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        $tickets = Ticket::where('user_id', $teacher->id)
            ->withCount('replies')
            ->latest()
            ->paginate(10);

        return view('teacher.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('teacher.tickets.create');
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

        return redirect()->route('teacher.tickets.show', $ticket)
            ->with('success', 'تم إنشاء التذكرة بنجاح. رقم التذكرة: #' . $ticket->id);
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['replies' => function($q) {
            $q->where('is_internal', false)->with('user');
        }]);

        return view('teacher.tickets.show', compact('ticket'));
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

        if (in_array($ticket->status, ['resolved', 'closed'])) {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'تم إضافة ردك بنجاح');
    }
}
