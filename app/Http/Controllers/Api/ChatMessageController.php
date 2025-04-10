<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatMessage;
use App\Models\User;
use App\Events\MessageSent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatMessageController extends Controller
{
    public function fetchMessages(): JsonResponse
    {
        Log::info('Fetching messages');
        $messages = ChatMessage::with('user')
            ->latest()
            ->take(50)
            ->get();
            
        Log::info('Messages fetched', ['count' => count($messages)]);
        return response()->json($messages);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        Log::info('Received message request', ['data' => $request->all()]);
        
        $request->validate([
            'message' => 'required|string|max:255',
        ]);
        
        $user = auth()->user() ?? User::first();
        Log::info('User sending message', ['user_id' => $user->id]);

        $message = ChatMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);

        $message->load('user');
        Log::info('Message created', ['message_id' => $message->id]);

        broadcast(new MessageSent($user, $message))->toOthers();
        Log::info('Message broadcasted');
        
        return response()->json($message);
    }
}
