<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{


    protected $fillable = [
        'user_id',
        'bio',
        'location',
        'website',
        'github_url',
        'total_posts',
        'followers_count',
        'following_count',
        'total_karma',
        'job_title',
        'photo_profile'


    ];

    // Explicit table name for this model. Use this when the table name
    // does not follow Laravel's default pluralization (e.g. 'profiles').
    // Keeps intent clear and prevents accidental mismatches with DB names.
    protected $table = 'profile';

   /**
     * The owner of this profile.
     * Relationship: a profile belongs to a single User (author/owner).
     * Returns a BelongsTo relation to the User model.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    


}