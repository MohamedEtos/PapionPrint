<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Webklex\IMAP\Facades\Client;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\GeneralMail;

class MailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $client = Client::account('default');
            $client->connect();
            
            $folder = $client->getFolder('INBOX');
            $messages = $folder->messages()->all()->limit(20)->get();

            return view('emails.index', compact('messages'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Could not connect to mail server: ' . $e->getMessage());
        }
    }

    public function sent()
    {
        try {
            $client = Client::account('default');
            $client->connect();
            
            // Usually 'Sent' or 'Sent Items'
            $folder = $client->getFolder('Sent'); 
            if (!$folder) {
                 $folder = $client->getFolder('Sent Items');
            }
            
            $messages = $folder ? $folder->messages()->all()->limit(20)->get() : [];

            return view('emails.index', compact('messages'))->with('box', 'sent');
        } catch (\Exception $e) {
             Log::error($e->getMessage());
             return back()->with('error', 'Error fetching sent items: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $client = Client::account('default');
            $client->connect();
            
            // Try Inbox first
            $inbox = $client->getFolder('INBOX');
            $message = $inbox->query()->where('uid', $id)->get()->first();
            
            if (!$message) {
                // Try Sent
                $sent = $client->getFolder('Sent');
                if (!$sent) $sent = $client->getFolder('Sent Items');
                
                if ($sent) {
                     $message = $sent->query()->where('uid', $id)->get()->first();
                }
            }
            
            if (!$message) {
                return back()->with('error', 'Message not found.');
            }

            return view('emails.show', compact('message'));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Could not fetch message: ' . $e->getMessage());
        }
    }

    public function create()
    {
        return view('emails.compose');
    }

    public function store(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        try {
            Mail::to($request->to)->send(new GeneralMail($request->subject, $request->body));
            
            return redirect()->route('mail.index')->with('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }


}
