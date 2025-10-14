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


  public function user(): BelongsTo{

      return $this->belongsTo(User::class);
  }

    
}