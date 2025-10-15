<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'job_title'


    ];

    protected $table = 'profile';


}