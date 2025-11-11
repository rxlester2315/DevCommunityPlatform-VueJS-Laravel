<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Chat;

class MessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn(): array
    {
        // Remove the extra "private-" - Laravel automatically adds it
        return [
            new PrivateChannel('user.' . $this->chat->sender_id),
            new PrivateChannel('user.' . $this->chat->receiver_id)
        ];
    }

    public function broadcastAs()
    {
        return 'new.chat.message';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'message' => $this->chat->message,
            'sender_id' => $this->chat->sender_id,
            'receiver_id' => $this->chat->receiver_id,
            'created_at' => $this->chat->created_at->toISOString(),
            'sender_name' => $this->chat->sender->name,
            'receiver_name' => $this->chat->receiver->name,
        ];
    }
}