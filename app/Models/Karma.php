<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Karma extends Model
{


    protected $table = 'karma';

    protected $fillable = ['user_id', 'type', 'karmaable_id', 'karmaable_type'];

    const UPVOTE = 'up';
    const DOWNVOTE = 'down';

    /**
     * The user who cast this karma (vote).
     * Relationship: a karma entry belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic owner (post or comment) this karma entry is attached to.
     * Use morphTo() so multiple models can be 'karmaable'.
     */
    public function karmaable()
    {
        return $this->morphTo();
    }
    
}