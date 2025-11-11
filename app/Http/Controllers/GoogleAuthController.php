<?php

namespace App\Http\Controllers;
use App\Models\activityLogs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{


    public function redirect()
{
    try {
        //Generates a random string called $state — used to 
        // verify that the request came from your app.
        $state = Str::random(40);
        
        //Saves that $state in the database 
        // (oauth_states table) for later validation.
        DB::table('oauth_states')->insert([
            'state' => $state,
            'created_at' => now(),
        ]);

        $url = Socialite::driver('google')
            ->stateless()
            ->with(['state' => $state])
            ->redirect()
            ->getTargetUrl();

  
        $url = str_replace(
            'http://localhost:8000/auths/google/callback',
            'http://localhost:8000/auths/google/callback/web', 
            $url
        );

        return response()->json([
            'success' => true,
            'redirect_url' => $url,
        ]);

    } catch (\Exception $e) {
        \Log::error('Google redirect error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to initialize Google login'
        ], 500);
    }
}




// after that yung google mag send you back or mag send back to app
public function callback(Request $request)
{
    try {
        $state = $request->input('state');
        $code = $request->input('code');
        //Google sends you back a state and a code.

        //You check if the state exists in your DB 
        // and is still valid (not expired).
        $storedState = DB::table('oauth_states')
            ->where('state', $state)
            ->where('created_at', '>', now()->subMinutes(10))
            ->first();


        // If it’s invalid → reject it (“Invalid state parameter”).
        if (!$storedState) {
            return redirect()->route('google.callback.web', [
                'success' => false,
                'message' => 'Invalid state parameter'
            ]);
        }
        // If valid → delete that state record (for security).
        DB::table('oauth_states')->where('state', $state)->delete();


       // Redirect to another route: google.callback.web, with the code and state.
        return redirect()->route('google.callback.web', [
            'state' => $state,
            'code' => $code,
            'success' => true
        ]);

    } catch (\Exception $e) {
        \Log::error('Google API callback error: ' . $e->getMessage());
        return redirect()->route('google.callback.web', [
            'success' => false,
            'message' => 'Authentication failed'
        ]);
    }
}



public function callbackWeb(Request $request)
{
    try {
        // Checks if success is true and state/code exist.
        if (!$request->get('success', true)) {
            return view('google-callback', [
                'success' => false,
                'message' => $request->get('message', 'Authentication failed')
            ]);
        }

        $state = $request->input('state');
        $code = $request->input('code');

        if (!$state || !$code) {
            return view('google-callback', [
                'success' => false,
                'message' => 'Missing authentication parameters'
            ]);
        }


       // Calls Google again (Socialite::driver('google')->stateless()->user()) 
       // to get the user’s data (name, email, Google ID, avatar, etc.).
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();
      
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

          // Finds the user in your database:
       // If found → updates Google tokens.
    // If not found → creates a new user in your users table.
        if (!$user) {
            // Creates a new API token for that user ($user->createToken()).
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'username' => User::generateUsername($googleUser->getEmail()),
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => Carbon::now(),
            ]);
        } else {
            $user->update([
                'google_id' => $googleUser->getId(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

               Auth::guard('web')->login($user, true);
        
             session()->regenerate();
           // same with custom login wherein we create token for the user and pass it to the browser
         $token = $user->createToken('google-auth-token')->plainTextToken;

        // Logs the activity in your activity_logs table.
        $dt = Carbon::now('Asia/Manila');
        ActivityLogs::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'description' => 'Has Login via Google',
            'status_activity' => 'Online',
            'date_time' => $dt,
          
        ]);

        return view('google-callback', [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'is_online' => true, 
                'last_seen' => now()->toDateTimeString(),
            ],
            'token' => $token
        ]);

    } catch (\Exception $e) {
        \Log::error('Google OAuth Web Error: ' . $e->getMessage());
        return view('google-callback', [
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


}