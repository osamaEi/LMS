<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'assignedTo'])
            ->withCount('replies');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $tickets = $query->latest()->paginate(15);

        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'in_progress' => Ticket::where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('status', 'resolved')->count(),
            'avg_response_time' => $this->calculateAverageResponseTime(),
        ];

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedTo', 'replies.user']);

        $staff = User::whereIn('role', ['admin', 'super_admin'])->get();

        return view('admin.tickets.show', compact('ticket', 'staff'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal_note_note' => 'boolean',
        ]);

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'is_internal_note_note' => $validated['is_internal_note_note'] ?? false,
        ]);

        // Update ticket status if it was open
        if ($ticket->status === 'open') {
            $ticket->update([
                'status' => 'in_progress',
                'first_response_at' => $ticket->first_response_at ?? now(),
            ]);
        }

        return back()->with('success', 'تم إضافة الرد بنجاح');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        if ($validated['status'] === 'closed') {
            $updateData['closed_at'] = now();
        }

        $ticket->update($updateData);

        return back()->with('success', 'تم تحديث حالة التذكرة');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $ticket->update(['assigned_to' => $validated['assigned_to']]);

        return back()->with('success', 'تم تعيين التذكرة بنجاح');
    }

    public function updatePriority(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket->update(['priority' => $validated['priority']]);

        return back()->with('success', 'تم تحديث الأولوية');
    }

    private function calculateAverageResponseTime()
    {
        $tickets = Ticket::whereNotNull('first_response_at')->get();

        if ($tickets->isEmpty()) {
            return 0;
        }

        $totalMinutes = 0;
        foreach ($tickets as $ticket) {
            $totalMinutes += $ticket->created_at->diffInMinutes($ticket->first_response_at);
        }

        return round($totalMinutes / $tickets->count());
    }
}
