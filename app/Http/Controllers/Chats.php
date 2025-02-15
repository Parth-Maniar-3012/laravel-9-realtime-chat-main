<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\WebsocketDemoEvent;

class Chats extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::where('id', '!=', Auth()->user()->id)->get();
        $messages = null;
        return view('chats', compact('users', 'messages'));
    }

    public function getMessages(Request $request)
    {
        $users = User::where('id', '!=', Auth()->user()->id)->get();
        $receiver_user = User::find($request->id);
        $receiver_id = $request->id;
        $messages = Message::with('user', 'receiver')->where('sender_id', Auth()->user()->id)->Where('receiver_id', $receiver_id)->orwhere('sender_id', $receiver_id)->Where('receiver_id', Auth()->user()->id)->get();
        return view('chats', compact('users', 'messages', 'receiver_id', 'receiver_user'));
    }

    public function sendMessages(Request $request)
    {
        $sender_user = User::find(Auth()->user()->id);

        $message = new Message();
        $message->sender_id = Auth()->user()->id;
        $message->receiver_id = $request->receiver_id;
        $message->message = $request->message;
        $message->created_at = now();
        $message->updated_at = now();
        $message->save();

        $users = User::where('id', '!=', Auth()->user()->id)->get();
        $receiver_user = User::find($request->receiver_id);
        $receiver_id = $request->receiver_id;
        $messages = Message::with('user', 'receiver')->where('sender_id', Auth()->user()->id)->orWhere('receiver_id', Auth()->user()->id)->where('sender_id', $receiver_id)->orWhere('receiver_id', $receiver_id)->get();

        broadcast(new WebsocketDemoEvent($sender_user->id, $receiver_id, $request->message, $sender_user->name));
        return view('chats', compact('users', 'messages', 'receiver_id', 'receiver_user'));
    }
}
