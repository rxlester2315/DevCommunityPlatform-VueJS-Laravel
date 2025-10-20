<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;


class HomeController extends Controller
{
    


 public function createPost(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'title_post' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_post' => 'required|string|max:255',

        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $posts = Post::create([
            'user_id' => auth()->id(),
            'text_content' => $validated['content'],
            'title_post' => $validated['title_post'],
            'category_post'=>$validated['category_post'],
            'image' => $imagePath,
            'likes_count' => 0,
            'comments_count' => 0,
            'published_at' => now(),
        ]);

        $posts->load('user');

        return response()->json([
            'message' => 'Post created successfully',
            'posts' => $posts
        ], 201);
    }


    public function getPost(){
        $posts = Post::with([
            'user' => function($query){
            $query->select('id','name','email')
            ->with(['profile:id,user_id,photo_profile']);
        }])
        ->orderBy('created_at','desc')
        ->get();


        return response()->json([
            'posts' => $posts
        ]);
    }


    public function deletePost($id){

        try{
            $post = Post::findOrFail($id);

                $post->delete();

                if($post->image){
                    
                    Storage::disk('public')->delete($post->image);

                }

            return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
            'post_id' => $id
        ], 200); // 




          
        }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
            
            return response()->json([
            'success' => false,
            'message' => 'Post not found'
        ], 404);

        }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting post: ' . $e->getMessage()
        ], 500);
    }
       
    }

   public function edit($id){

    $post = Post::findOrFail($id);

    return response()->json([

        'message' => 'Id Successfuly Find',
        'post' => $post,
    ] , 200);


   }


public function update(Request $request, $id)
{
    $post = Post::findOrFail($id);
    
    // Validate with the exact field names from your frontend form
    $validated = $request->validate([
        'editPostContent' => 'required|string|max:5000',
        'title_post' => 'required|string|max:255',
        'editCategPost' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    $updateData = [
        'text_content' => $validated['editPostContent'], 
        'category_post' => $validated['editCategPost'], 
        'title_post' => $validated['title_post'],
        'updated_at' => now(),
    ];

    if ($request->hasFile('image')) {
        if ($post->image_path) {
            Storage::delete($post->image_path);
        }
        
        $imagePath = $request->file('image')->store('posts', 'public');
        $updateData['image_path'] = $imagePath;
    }

    $post->update($updateData);

    return response()->json([
        'success' => true,
        'message' => 'Post Updated Successfully',
        'post' => $post
    ], 200);
}


public function checkProfile()
    {
        $user = auth()->user();
        
        $hasProfile = Profile::where('user_id', $user->id)->exists();

        return response()->json([
            'has_profile' => $hasProfile, // true or false return if may profile or wala
            'user_id' => $user->id, // return yung id if meron
            'message' => $hasProfile ? 'Profile exists' : 'No profile found' // condition statement whether p-exist or n-exist
        ]);
    }


public function setupProfile(Request $request){

    $user = auth()->user();


    if($user->profile){

        return response()->json([
            'message' => 'Profile Has already exist',
            'profile' => $user->profile
        ] , 409);
    }

  

   $validated = $request->validate([
    'bio' => 'required|string|max:255',
    'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    'location' => 'required|string|max:100',
    'website'=> 'nullable|url|max:255',
    'github_url' => 'nullable|url|max:255',


  ]);

  try {


     $imageProfilePath = null;

 if($request->hasFile('photo_profile')){

    $imageProfilePath = $request->file('photo_profile')->store('profile' , 'public');
 }

  $profile = Profile::create([
    'user_id' => $user->id,
    'bio' => $validated['bio'],
    'location' => $validated['location'],
    'website' => $validated['website'] ?? null,
    'github_url' => $validated['github_url'] ?? null,
    'photo_profile' => $imageProfilePath 

   
  ]);

  $profile->load('user');

       return response()->json([
                'message' => 'Profile setup successfully',
                'profile' => $profile
            ], 201);





  }catch (\Exception $e) {
            \Log::error('Profile setup failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to setup profile',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }


 




}


public function getProfile(){

    $user = Auth()->user();

    $user->load('profile');

    $profile = Profile::with('user')->where('user_id', $user->id)->first();


    if(!$profile){

        return response()->json([
            'message' => 'Profile not found',

        ],404);
    }

    return response()->json([
        'profile'=> $profile
    ],200);
}

public function getCurrentUser()
{
    $user = auth()->user()->load('profile');
    
    return response()->json([
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'username' => $user->username,
        'photo_profile' => $user->profile->photo_profile,
    ]);
}







    public function historyPost()
{
    $user = auth()->user();
    
    $posts = Post::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
    
    if($posts->isEmpty()){
        return response()->json([
            'message' => 'No posts found',
        ], 404);
    }

    return response()->json([
        'posts' => $posts
    ], 200);
}



}