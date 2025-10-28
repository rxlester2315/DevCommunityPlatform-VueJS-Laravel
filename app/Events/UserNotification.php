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

class UserNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    
    public $comment;
    public $post;
    public $commentAuthor;
    public $postAuthorId;
    
    public function __construct(Comment $comment, Post $post, User $commentAuthor)
    {
        $this->comment = $comment;
        $this->post = $post;
        $this->commentAuthor = $commentAuthor;
        $this->postAuthorId = $post->user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
   public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->postAuthorId);
    }

       public function broadcastAs()
    {
        return 'new.comment';
    }

     public function broadcastWith()
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title_post,
            'comment_content' => substr($this->comment->content, 0, 100) . (strlen($this->comment->content) > 100 ? '...' : ''),
            'comment_author_name' => $this->commentAuthor->name,
            'comment_author_username' => $this->commentAuthor->username,
            'created_at' => $this->comment->created_at->diffForHumans(),
            'message' => $this->commentAuthor->name . ' commented on your post'
        ];
    }
}