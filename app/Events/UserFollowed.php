<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;



class UserFollowed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

 

   public $follower;
   public $followed;
   public $message;

    public function __construct(User $follower, User $followed )
    {
        $this->follower = $follower;
        $this->followed = $followed;
        $this->message = "{$follower->name} started following you";
        
    }



  
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->followed->id);

    }


      public function broadcastAs()
    {
        return 'user.followed';
    }


    public function broadcastWith()
    {
        return [
            'follower' => [
                'id' => $this->follower->id,
                'name' => $this->follower->name,
                'username' => $this->follower->username,
                'avatar' => $this->follower->profile->photo_profile ?? null,
            ],
            'message' => $this->message,
            'followed_at' => now()->toISOString(),
        ];
    }
}