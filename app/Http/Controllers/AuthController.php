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
use App\Models\activityLogs;


// Note : This Controller responsible for 
// Creating Account / Register Account
// Logout Function/
// Getting User

class AuthController extends Controller
{


// for this function the purpose of this 
// is mag create and mag store ng data sa db user, 
// so purpose niya is mag create ng user
public function store(Request $request)
{

    // proper way ng pag validate ng data
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);
   // using create a query function para mag create ng data
    $user = User::create([
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    
   // then after that we will transform the data into json type so that 
   // we can transfer or throw this in front end,
   //  yung front end mag rerecieve neto
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

// here is we are validating or checking if tama ba yung information 
// so main function neto is when the user trying to login
// we are verifying  if tama and si user ba talaga ang login
public function authenticate(Request $request)
{    
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        ActivityLogs::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'description' => 'User logged in',
            'status_activity' => 'Online',
            'date_time' => now('Asia/Manila'),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_online' => true, 
                'last_seen' => now()->toDateTimeString(),
            ],
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    return response()->json([
        'success' => false,
        'message' => 'Invalid credentials'
    ], 401);
}

    // Return the currently authenticated user's public profile.
    // - Uses Auth::user() to retrieve the user tied to the current session/token.
    // Security note: avoid returning sensitive attributes (password hashes, tokens).
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


// this function is for logout of user
public function logout(Request $request)
{  
    // here we use try catch para if ever may ma 
    // encounter tayo error pag nag logout  may display tayo
    try {
        // here we are getting the auth user
        //  and then yung time ng pag logout niya
        $user = Auth::user();
        $dt = Carbon::now('Asia/Manila');

        \Log::info('Logout process started for user: ' . ($user ? $user->email : 'No user'));

       // then after that mag sesend tayo or mag insert tayo ng data ulit kay 
       // activity_logs this time data naman ng pag logout niya same lang din 
       // kay pag login na function or authenticate diff lang is yung pag logout
        if ($user) {
      ActivityLogs::create([
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'description' => 'User logged out',
        'status_activity' => 'Offline',
        'date_time' => now('Asia/Manila'),
    ]);
            
            \Log::info('Logout activity recorded for: ' . $user->email);
        }

        
       // then we call this auth function where in we will logout the current login user
        Auth::guard('web')->logout();
        
       

        \Log::info('Session destroyed successfully');
        // after successfully or no error we will send this data 
        // to front-end saying na success yung pag logout
        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
        
     // incase may error tayo ma ecnounter this will catch
    } catch (\Exception $e) {
        \Log::error('Logout error: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Logout failed: ' . $e->getMessage()
        ], 500);
    }
}

    
}