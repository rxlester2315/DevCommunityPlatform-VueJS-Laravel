<?php

namespace App\Http\Controllers;

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
        $state = Str::random(40);
        
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

public function callback(Request $request)
{
    try {
        $state = $request->input('state');
        $code = $request->input('code');
        
        $storedState = DB::table('oauth_states')
            ->where('state', $state)
            ->where('created_at', '>', now()->subMinutes(10))
            ->first();

        if (!$storedState) {
            return redirect()->route('google.callback.web', [
                'success' => false,
                'message' => 'Invalid state parameter'
            ]);
        }

        DB::table('oauth_states')->where('state', $state)->delete();

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
        // Check if we have success parameter from API callback
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

        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if (!$user) {
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

        // Log the user in (session-based)
        Auth::login($user);
        $request->session()->regenerate();

        // Record login activity
        $dt = Carbon::now('Asia/Manila');
        DB::table('activity_logs')->insert([
            'name' => $user->name,
            'email' => $user->email,
            'description' => 'Has Login via Google',
            'status_activity' => 'Online',
            'date_time' => $dt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Return view with user data
        return view('google-callback', [
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'username' => $user->username,
                'avatar' => $user->avatar,
                'last_login' => $dt->toDateTimeString(),
            ]
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