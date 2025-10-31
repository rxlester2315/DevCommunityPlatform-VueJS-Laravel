<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Karma extends Model
{


    protected $table = 'karma';

    protected $fillable = ['user_id', 'type', 'karmaable_id', 'karmaable_type'];

     
    const UPVOTE = 'up';
    const DOWNVOTE = 'down';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
   
    public function karmaable()
    {
        return $this->morphTo();
    }
    
}