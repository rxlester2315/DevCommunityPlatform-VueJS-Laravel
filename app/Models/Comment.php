<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'image'
    ];

    protected $table = 'comments';

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A comment belongs to ONE post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    
}