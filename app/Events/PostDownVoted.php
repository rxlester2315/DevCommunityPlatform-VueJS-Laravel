<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;

class PostDownVoted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $voter;
    public $postAuthor;
    public $voteType;


    
    public function __construct(Post $post, User $voter, User $postAuthor)
    {
        $this->post = $post;
        $this->voter = $voter;
        $this->postAuthor = $postAuthor;
        $this->voteType = 'downvote';
    }

    
    public function broadcastOn(): array
    {
        return [
        new PrivateChannel('user.' . $this->postAuthor->id)
    ];

    }

     public function broadcastAs()
    {
        return 'new.vote';
    }

    public function broadcastWith()
    {
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title_post,
            'voter_name' => $this->voter->name,
            'voter_username' => $this->voter->username,
            'vote_type' => $this->voteType,
            'created_at' => now()->diffForHumans(),
            'message' => $this->voter->name . ' downvoted your post'
        ];
    }
}