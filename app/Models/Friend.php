<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{

        protected $fillable = [
        'user_id',
        'friend_id',
        'friends_since'
    ];

      protected $casts = [
        'friends_since' => 'datetime'
    ];
       

       // yung nag add
       public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

  // your friend
      public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id')->with('profile');
    }

     //This is a bidirectional lookup.
     // user A → user B is in the friends table OR
     // user B → user A is in the friends table.
     // kahit saan dito they considered friends
     // This is the key logic that ensures friendship is two-way and symmetric.
     public static function areFriends($userId1, $userId2): bool
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('user_id', $userId1)
                  ->where('friend_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('user_id', $userId2)
                  ->where('friend_id', $userId1);
        })->exists();
    }


    
}