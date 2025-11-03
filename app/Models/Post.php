<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
   protected $fillable = [
    'user_id',
    'title_post', 
    'category_post',
    'text_content',
    'image',
    'likes_count',
    'comments_count',
    'is_active',
    'published_at'
];
          // Explicit table name for this model. Overrides Laravel's
          // pluralization when the DB table name differs (e.g. 'post').
          protected $table = 'post';


   
   /**
     * Convenience accessor: returns the author's Profile via the post's user.
     * Useful in templates to access profile without an extra query when already eager-loaded.
     */
    public function getProfileAttribute()
    {
        return $this->user->profile;
    }

    /**
     * The user who created the post.
     * Relationship: a post belongs to a single User (author).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

/**
     * Comments attached to this post.
     * Relationship: a post has many comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');   
    }

    /**
     * Polymorphic karma entries (upvotes/downvotes) for this post.
     * Using morphMany so posts and comments can share the Karma model.
     */
    public function karma()
    {
        return $this->morphMany(Karma::class, 'karmaable');
    }


    // Convenience scopes to filter karma by type.
    public function upvotes()
    {
        return $this->karma()->where('type', Karma::UPVOTE);
    }
    public function downvotes()
    {
        return $this->karma()->where('type', Karma::DOWNVOTE);
    }

    /**
     * Compute karma/engagement score.
     * Note: current implementation returns upvote count; adjust if you need net score.
     */
    public function karmaScore()
    {
     return $this->upvotes()->count();
    }

      public function userVote(User $user = null)
    {
        if (!$user) return null;
        
        $vote = $this->karma()->where('user_id', $user->id)->first();
        return $vote ? $vote->type : null;
    }

     public function upvote(User $user)
    {
        $this->updateVote($user, Karma::UPVOTE);
    }

     public function downvote(User $user)
    {
        $this->updateVote($user, Karma::DOWNVOTE);
    }

    public function removeVote(User $user)
    {
        $this->karma()->where('user_id', $user->id)->delete();
    }

    protected function updateVote(User $user, $type)
    {
        $this->karma()->updateOrCreate(
            ['user_id' => $user->id],
            ['type' => $type]
        );
    }


   



}