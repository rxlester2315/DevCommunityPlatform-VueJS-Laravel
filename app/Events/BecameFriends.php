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

class BecameFriends implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user1;
    public $user2;
    public $message;
    public $currentUserId; 


    
    public function __construct( User $user1, User $user2 , $currentUserId = null)
    {
        $this->user1 = $user1;// other user
        $this->user2 = $user2;// current user
        $this->message = "You and {$user1->name} are now friends!";


    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        //both of them will notified

         return [
            new PrivateChannel('user.' . $this->user1->id),
            new PrivateChannel('user.' . $this->user2->id)
        ];
        
    }

     public function broadcastAs()
    {
        return 'became.friends';
    }

     public function broadcastWith()
    {

       
        
        return [
            'friend' => [
                'id' => $this->user1->id,
                'name' => $this->user1->name,
                'username' => $this->user1->username,
                'avatar' => $this->user1->profile->photo_profile ?? null,
            ],
            'message' => $this->message,
            'friends_since' => now()->toISOString(),
        ];
    }
}