<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chat;


use App\Events\MessageEvent;

class ChatController extends Controller
{

    public function index($userId){

        $currentUserId = Auth::id();

        $messages = Chat::where(function($query) use ($currentUserId, $userId){

            $query->where('sender_id',$currentUserId)
            ->where('receiver_id' , $userId);
        })->orWhere(function($query) use ($currentUserId,$userId){
            $query->where('sender_id', $userId)
            ->where('receiver_id' , $currentUserId);
        })->with(['sender','receiver'])->get();

        return response()->json($messages);

    }
     
    public function sendMessage(Request $request){


        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string'
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
             'read' => false


      

        ]);

            $chat->load(['sender', 'receiver']);
            broadcast(new MessageEvent($chat))->toOthers();
            return response()->json($chat);




    }
}