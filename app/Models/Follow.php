<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


// this is pivot table
// The follows table is the pivot table — it connects two users together.
class Follow extends Model
{

        public $timestamps = false; // This is important!


      protected $fillable = [
        'follower_id',
        'followed_id',
        'followed_at'
    ];

        protected $casts = [
        'followed_at' => 'datetime'
    ];


     public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id')->with('profile');
    }

      // The user who is being followed (with profile)
    public function followed()
    {
        return $this->belongsTo(User::class, 'followed_id')->with('profile');
    }
    
}