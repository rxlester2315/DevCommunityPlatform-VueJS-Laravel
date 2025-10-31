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

      public function karma()
    {
        return $this->hasMany(Karma::class);
    }


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




// User Model
    public function profile(): HasOne
{
    return $this->hasOne(Profile::class, 'user_id');
}

public function posts()
{
    return $this->hasMany(Post::class, 'user_id');
}
 public function user()
    {
        return $this->belongsTo(User::class);
    }

public function comments()
{
    return $this->hasMany(Comment::class, 'user_id');
}

}