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
          protected $table = 'post';


   
   public function getProfileAttribute()
    {
        return $this->user->profile;
    }

    public function user(): BelongsTo
{
    return $this->belongsTo(User::class, 'user_id');
}

public function comments()
{
    return $this->hasMany(Comment::class, 'post_id');   

    
}

 public function karma()
    {
        return $this->morphMany(Karma::class, 'karmaable');
    }


   public function upvotes()
    {
        return $this->karma()->where('type', Karma::UPVOTE);
    }
     public function downvotes()
    {
        return $this->karma()->where('type', Karma::DOWNVOTE);
    }

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