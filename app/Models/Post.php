<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    
      protected $fillable = [
        'user_id',
        'text_content', 
        'likes_count',
        'comments_count',
        'image',
        'is_active',
        'published_at',
        'title_post',
        'category_post'


    ];
          protected $table = 'post';


  public function user(): BelongsTo{

      return $this->belongsTo(User::class);
  }

    
}