<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
    }

    public function send(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // In production: Mail::to('contact@vibe.com')->send(new ContactMail($request->all()));
        // For now, we just flash success

        return back()->with('success', 'Thank you for your message! Our team will respond within 2 hours.');
    }
}
