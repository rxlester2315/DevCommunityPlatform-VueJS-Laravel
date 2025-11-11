<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;
use App\Models\Post;

class BroadcastServiceProvider extends ServiceProvider
{
    
  
    public function boot(): void
    {
    Broadcast::routes(['middleware' => ['web', 'auth:sanctum']]); // Add 'web'
           // here we are checking if the current authenticated user is same owner of the post  which is the userId this id is for identifying who the owner of the post 
            Broadcast::channel('user.{userId}', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        

    Broadcast::channel('post.{postId}', function ($user, $postId) {
    $post = Post::find($postId);

    $hasCommented = $post->comments()->where('user_id', $user->id)->exists();

    return $post && ($user->id === $post->user_id || $hasCommented);
});





        

    }
}