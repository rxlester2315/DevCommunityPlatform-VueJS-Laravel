<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Profile;

use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable 
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasApiTokens ;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username', 
        'email',
        'password',
        'google_id',
        'google_token',
        'google_refresh_token',
        'avatar',
        'status'
    ];
    protected $attributes = [
    'status' => 'Active'
];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_token',
        'google_refresh_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Karma (votes) cast by this user.
     * Relationship: a user can have many Karma records (one per vote).
     */
    public function karma()
    {
        return $this->hasMany(Karma::class);
    }


    // Generate a clean, unique username from an email address.
    // Strips non-alphanumeric characters and appends a counter if needed.
    public static function generateUsername($email)
    {
        $username = strstr($email, '@', true);
        $username = preg_replace('/[^a-zA-Z0-9_]/', '', $username);
        
        // Ensure username is unique
        $originalUsername = $username;
        $counter = 1;
        
        while (static::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }




    /**
     * The user's public profile.
     * Relationship: a user has one Profile (1:1).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    /**
     * Posts authored by this user.
     * Relationship: a user has many Posts.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'user_id');
    }
    /**
     * Self-referential relation placeholder.
     * (Present in the file but may be unused — typically used for parent/owner references.)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Comments written by the user.
     * Relationship: a user has many Comments.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }


   public function following()
{
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')
                ->withPivot('followed_at') 
                ->with('profile');
}

public function followers()
{
    return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')
                ->withPivot('followed_at') 
                ->with('profile');
}


    public function toggleFollow(User $user): bool
    {
        if ($this->follows($user)) {
            return $this->unfollow($user);
        } else {
            return $this->follow($user);
        }
    }

        public function unfollow(User $user): bool
    {
        if ($this->follows($user)) {
            $this->following()->detach($user->id);
            return true;
        }

        return false;
    }

        public function follow(User $user): bool
    {
        // Prevent self-follow
        if ($this->id === $user->id) {
            return false;
        }

        // Only follow if not already following
        if (!$this->follows($user)) {
            $this->following()->attach($user->id, [
                'followed_at' => now()
            ]);
            return true;
        }

        return false;
    }

      public function follows(User $user): bool
    {
        return $this->following()
            ->where('followed_id', $user->id)
            ->exists();
    }

}