<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\FarmSetting;

class ContactSupportController extends Controller
{
    public function show()
    {
        $farm = [
            'name'    => FarmSetting::get('farm_name', "Escalona's Farm"),
            'address' => FarmSetting::get('farm_address', 'Pagkakaisa, Naujan, Oriental Mindoro'),
            'phone'   => FarmSetting::get('farm_phone', '+63 912 345 6789'),
            'email'   => FarmSetting::get('farm_email', 'support@escalonafarm.com'),
        ];
        return view('contact-support', compact('farm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Save to database inbox
        $contactMessage = ContactMessage::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        // Send email notification to admin
        try {
            Mail::to(env('ADMIN_EMAIL', 'marananalysa029@gmail.com'))
                ->send(new ContactMessageReceived($contactMessage));
        } catch (\Exception $e) {
            Log::warning('Contact form email failed: ' . $e->getMessage());
            // Still succeed — message is saved in DB inbox even if email fails
        }

        return back()->with('success', 'Your message has been sent successfully!');
    }

    public function inbox()
    {
        if (!auth()->check()) return redirect('/login');
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();
        $unreadCount = ContactMessage::where('is_read', false)->count();
        return view('contact-inbox', compact('messages', 'unreadCount'));
    }

    public function markRead(ContactMessage $message)
    {
        if (!auth()->check()) return redirect('/login');
        $message->update(['is_read' => true]);
        return back();
    }

    public function markAllRead()
    {
        if (!auth()->check()) return redirect('/login');
        ContactMessage::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All messages marked as read.');
    }

    public function destroy(ContactMessage $message)
    {
        if (!auth()->check()) return redirect('/login');
        $message->delete();
        return back()->with('success', 'Message deleted.');
    }
}
