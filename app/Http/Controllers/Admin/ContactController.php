<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->paginate(20);
        $counts = [
            'all'      => Contact::count(),
            'new'      => Contact::where('status', 'new')->count(),
            'read'     => Contact::where('status', 'read')->count(),
            'replied'  => Contact::where('status', 'replied')->count(),
            'archived' => Contact::where('status', 'archived')->count(),
        ];

        return view('admin.contacts.index', compact('contacts', 'counts'));
    }

    public function show(Contact $contact)
    {
        if ($contact->status === 'new') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }

    public function updateStatus(Request $request, Contact $contact)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $contact->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الرسالة');
    }

    public function reply(Request $request, Contact $contact)
    {
        $request->validate([
            'reply_message' => 'required|string|min:10',
        ]);

        $replyText = $request->input('reply_message');
        $adminName = auth()->user()->name ?? 'إدارة المنصة';

        // Send reply email to the contact
        try {
            Mail::raw($replyText, function ($message) use ($contact, $adminName) {
                $message->to($contact->email, $contact->first_name . ' ' . $contact->last_name)
                    ->subject('رد على رسالتك: ' . ($contact->subject ?? 'تواصل معنا'))
                    ->from(config('mail.from.address'), config('mail.from.name', $adminName));
            });
        } catch (\Exception $e) {
            // Log but don't block — still update status
        }

        // Update contact status to replied
        $contact->update(['status' => 'replied']);

        // Notify all superadmins
        $superAdmins = User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new CustomNotification(
                title: 'تم الرد على رسالة تواصل',
                body: "رد {$adminName} على رسالة {$contact->first_name} {$contact->last_name} بخصوص: " . ($contact->subject ?? 'بدون موضوع'),
                actionUrl: route('admin.contacts.show', $contact),
                senderName: $adminName,
            ));
        }

        return back()->with('success', 'تم إرسال الرد بنجاح وإشعار المدير العام.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.contacts.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }
}
