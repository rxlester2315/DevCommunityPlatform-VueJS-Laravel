<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class activityLogs extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email', 
        'description',
        'status_activity',
        'date_time'
    ];

       protected $table = 'activity_logs';



       public function user(){
        
        return $this->belongsTo(User::class);
       }

       //This is a query scope to easily get currently 
       // online users.
       //It assumes a user is online if:

      //status_activity = 'online'

     //and their last log was within the last 5 minutes.

    public function scopeOnline($query)
    {
        return $query->where('status_activity', 'online')
                    ->where('created_at', '>=', now()->subMinutes(5));
    }
    

    //This lets you fetch recent activity logs, 
    // defaulting to the last 15 minutes.
      public function scopeRecent($query, $minutes = 15)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
    

    // This is a quick helper that 
    // gives you the most recent activity of any user.
     public static function getLatestActivity($userId)
    {
        return self::where('user_id', $userId)
                  ->orderBy('created_at', 'desc')
                  ->first();
    }
}