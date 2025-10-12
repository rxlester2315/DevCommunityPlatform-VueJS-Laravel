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


    public function authenticate(Request $request){

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);



        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials)) {
           
            $user = Auth::user();
            $dt = Carbon::now('Asia/Manila');

            if($user->status !== 'Active'){
                Auth::logout();

                return response()->json([
                    'success' => false,
                    'message' => 'Your acount is not active'
                ] , 401);
            }

            $request->session()->regenerate();


              DB::table('activity_logs')->insert([
            'name'        => $user->name,
            'email'       => $user->email,
            'description' => 'Has Login',
            'status_activity' => 'Online',
            'date_time'   => $dt,
            'created_at' => now(),
            'updated_at' => now(),




        ]);

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_login' => $dt->toDateTimeString(),
            ],
            'message' => 'Login successful'
        ]);
 

}
  return response()->json([
        'success' => false,
        'message' => 'Invalid credentials or account not active'
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


    public function logout(Request $request){

        $user = Auth::user();
        $dt = Carbon::now('Asia/Manila');


        if($user){
            DB::table('activity_logs')->insert([
                'name' => $user->name,
                'email'=> $user->email,
                'description'=> 'Has Logout',
                'status_activity' => 'Offline',
                'date_time' => $dt,
                'created_at' => now(),
                'updated_at' => now(),

            ]);
        }

        Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Logout successful'
    ]);

    }





    public function getUserActivity(Request $request){
    
    $user = Auth::user();

    $activities = DB::table('activity_logs')
    ->where('email',$user->email)
    ->orderBy('date_time' ,'desc')
    ->get(['description','status_activity','date_time']);


    return response()->json([
        'success'=> true,
        'activities' => $activities
    ]);




    }


    public function getLastLogin(Request $request)
{
    $user = Auth::user();
    
    $lastLogin = DB::table('activity_logs')
        ->where('email', $user->email)
        ->where('description', 'Has login')
        ->orderBy('date_time', 'desc')
        ->first();

    return response()->json([
        'success' => true,
        'last_login' => $lastLogin ? $lastLogin->date_time : null
    ]);
}


    
}