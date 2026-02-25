<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContactController extends Controller
{
    public function index()
    {
        return view('front.contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'category' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:20480',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('contacts', 'public');
        }

        $contact = Contact::create($validated);

        // Notify all superadmins about the new contact message
        $superAdmins = User::where('role', 'super_admin')->get();
        foreach ($superAdmins as $superAdmin) {
            $superAdmin->notify(new CustomNotification(
                title: 'رسالة تواصل جديدة',
                body: "أرسل {$contact->first_name} {$contact->last_name} رسالة جديدة بخصوص: " . ($contact->subject ?? 'بدون موضوع'),
                actionUrl: route('admin.contacts.show', $contact),
                senderName: $contact->first_name . ' ' . $contact->last_name,
            ));
        }

        return redirect()->route('contact')->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك في أقرب وقت ممكن.');
    }
}
