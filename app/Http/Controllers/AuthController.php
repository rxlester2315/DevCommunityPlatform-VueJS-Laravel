<?php

namespace App\Http\Controllers;
use Hash;
use Auth;
use DB;
use Carbon\Carbon;
use Session;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'message' => 'Account Created Successfully',
        'success' => true,
        'user' => [
            'name' => $user->name, 
            'username' => $user->username,
            'email' => $user->email,
        ]
    ], 201); 
}


public function authenticate(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $dt = Carbon::now('Asia/Manila');

        // Record login activity
        DB::table('activity_logs')->insert([
            'name' => $user->name,
            'email' => $user->email,
            'description' => 'Has Login',
            'status_activity' => 'Online',
            'date_time' => $dt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // since we are using JWT token  we need to create token that will store in localstorage and this token use for every request of the user
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_login' => $dt->toDateTimeString(),
            ],
            'token' => $token, // the token to be stored in localstorage
            'message' => 'Login successful'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}


public function getUser(Request $request){

    $user = Auth::user();

    if($user){
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name'=> $user->name,
                'email'=> $user->email,
            ]

        ]);
    }

    return response()->json([
        'succcess' => false,
        'message' => 'Not Authenticated'
     ] , 401);

}


public function logout(Request $request)
{
    try {
        $user = Auth::user();
        $dt = Carbon::now('Asia/Manila');

        \Log::info('Logout process started for user: ' . ($user ? $user->email : 'No user'));

        if ($user) {
            DB::table('activity_logs')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'description' => 'Has Logout',
                'status_activity' => 'Offline',
                'date_time' => $dt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            \Log::info('Logout activity recorded for: ' . $user->email);
        }

        Auth::guard('web')->logout();
        
       

        \Log::info('Session destroyed successfully');

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);

    } catch (\Exception $e) {
        \Log::error('Logout error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Logout failed: ' . $e->getMessage()
        ], 500);
    }
}

    
}