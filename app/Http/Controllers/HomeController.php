<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use App\Models\Karma;

use Illuminate\Support\Facades\Auth;
use App\Events\UserNotification;
use App\Events\PostDownVoted;
use App\Events\PostUpvoted;






class HomeController extends Controller
{

// for this naman we are creating and store a post from user 
// so if yung user mag create or mag post eto yung fucntion
// nag mag send sa db and mag store 
 public function createPost(Request $request)
    {  

        // we just validating the data and make sure the 
        // sinusunod nya yung proper way na pag post
        $validated = $request->validate([   
            'content' => 'required|string|max:5000',
            'title_post' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'category_post' => 'required|string|max:255',

        ]);
      // here we add variable $imagePath na mag handle ng image function natin
      // for post
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }


      // after that validation and creation of variable image
      // we will proceed to create we use function Create
      // to send those data from  validated to database
      // so in short yung data nasa validated we will send 
      // and we will create na 
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
       
        // here we are just doing relationship with user
        // so its like we are matching make sure this post
        // belong who user?
        // relationship between POST MODEL & USER MODEL
        $posts->load('user');


        // After that process we will ready to send this to front-end
        return response()->json([
            'message' => 'Post created successfully',
            'posts' => $posts
        ], 201);

        // 201 meaning success
    }

   // Here yung function nato is we are just displaying yung lahat 
   // ng post na ginawa ng mga user 
   // so we just getting all post include Auth or 
   // user/ profile/photo/commments/karma score
   public function getPost(){

    // so the relationship between is
    // From User to Post ,Profile Models
    $posts = Post::with([
        'user' => function($query){
            $query->select('id','name','email')
            ->with(['profile:id,user_id,photo_profile']);
        }
    ])
    ->withCount('comments') 
    //  ADD KARMA COUNTS
    ->withCount(['karma as upvotes_count' => function($query) {
        $query->where('type', 'up');
    }])
    ->withCount(['karma as downvotes_count' => function($query) {
        $query->where('type', 'down');
    }])
    //  ADD USER'S CURRENT VOTE
    ->with(['karma' => function($query) {
        $query->where('user_id', auth()->id());
    }])
    ->orderBy('created_at','desc')
    ->get()
    // CALCULATE KARMA SCORE AND USER VOTE
    ->map(function($post) {
        $post->karma_score = $post->upvotes_count; 

        $post->user_vote = $post->karma->first()?->type;
        return $post;
    });

    return response()->json([
        'posts' => $posts
    ]);
}

  // so here function neto is if ever si user gustu 
  // niya delete yung post niya
  // for this function may parameter na $id 
  // meaning we need an id of the post before tayo mag proceed sa delete
  // so if we found the id or we already select it will delete easily
    public function deletePost($id){

        // we have try catch if ever we encounter an error
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

// and also meron din tayong function 
// pwde natin e edit yung current post natin or ni user
// there a parameter na id and also we need to implement the validation check
// which is Request
public function update(Request $request, $id)
{  
     // find the id
    $post = Post::findOrFail($id);
    // check those data if still acceptable padin ba
    $validated = $request->validate([
        'editPostContent' => 'required|string|max:5000',
        'title_post' => 'required|string|max:255',
        'editCategPost' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);
   
   // then we put in the variable called updateData 
   // its like array set of data
    $updateData = [
        'text_content' => $validated['editPostContent'], 
        'category_post' => $validated['editCategPost'], 
        'title_post' => $validated['title_post'],
        'updated_at' => now(),
    ];

    // here we checking if may image ba, we making sure na image siya
    if ($request->hasFile('image')) {
        if ($post->image_path) {
            Storage::delete($post->image_path);

            // if meron delete natin
        }
        
        // and then update natin ng new image
        $imagePath = $request->file('image')->store('posts', 'public');
        $updateData['image_path'] = $imagePath;
    }

    // after that we will use update function and inside 
    // of it is the array data or UpdatedData

    $post->update($updateData);

    return response()->json([
        'success' => true,
        'message' => 'Post Updated Successfully',
        'post' => $post
    ], 200);
}



// here we have function wherein we check if yung user nayun is may profile
// the purpose of this some pages or route require profile
// like commenting,doing vote, or checking the profile

public function checkProfile()
    {  
        // here we are checking if user is authenticated 
        $user = auth()->user();
        
        // here we are checking if the current user is authenticated
        // have match in the user_id profile table 
        // so ex. in table Profile user_id = 1 then in table user may id=1
        // then meaning may profile
        $hasProfile = Profile::where('user_id', $user->id)->exists();

        return response()->json([
            'has_profile' => $hasProfile, // true or false return if may profile or wala
            'user_id' => $user->id, // return yung id if meron
            'message' => $hasProfile ? 'Profile exists' : 'No profile found' // condition statement whether p-exist or n-exist
        ]);
    }



// here naman for sure na walang profile 
public function setupProfile(Request $request){
   
   // we make sure na naka login si user or authenitcated siya 
    $user = auth()->user();

   // lets check if wala ba talaga profile if meron then we will
   // notified in front-end
    if($user->profile){

        return response()->json([
            'message' => 'Profile Has already exist',
            'profile' => $user->profile
        ] , 409);
    }

  
 // we are starting to create a validation
 // we make sure na acceptable yung mga data
 // na input ni user
   $validated = $request->validate([
    'bio' => 'required|string|max:255',
    'photo_profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    'location' => 'required|string|max:100',
    'website'=> 'nullable|url|max:255',
    'github_url' => 'nullable|url|max:255',


  ]);

  try {

  // we are initalizing the imageProfilePath is null 
  // and also in our db we set in nullable
  // so we can still create profile even no 
  // profile
     $imageProfilePath = null;
 
 // we request or we add profile then we just set the imageProfilePath
 if($request->hasFile('photo_profile')){

    $imageProfilePath = $request->file('photo_profile')->store('profile' , 'public');
 }

// after that process now we are ready to pass & store those data in profile
// table
  $profile = Profile::create([
    'user_id' => $user->id,
    'bio' => $validated['bio'],
    'location' => $validated['location'],
    'website' => $validated['website'] ?? null,
    'github_url' => $validated['github_url'] ?? null,
    'photo_profile' => $imageProfilePath 

   
  ]);


// since  are still using user_id so it have
// relationship between user and profile\
// purpose of that so that we can know who owner of that profile
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

// the purpose is this  is when we visit in the profile page
// we can display those data that we created in function SetupProfile
// now we can see the profile
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
    try {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // checking if may profile yung user 
        $photoProfile = $user->profile ? $user->profile->photo_profile : null;

        return response()->json([
            'success' => true,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'photo_profile' => $photoProfile, // This can be null
            'has_profile' => !is_null($user->profile) // Useful for frontend
        ]);
        
    } catch (\Exception $e) {
        \Log::error('getCurrentUser error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Server error: ' . $e->getMessage()
        ], 500);
    }
}





// here we just fetching or getting those post 
// na belong sa current user and then we will display
// sa profile niya so when he visit to the profile andun yung 
// mga post niya ginawa
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




// so for this function is when we view the post
// or na click natin mapupunta tayo dun sa post nayun 
// wherein we can do comment but this is not the function
// nag mag submit tayo ng comment only view
public function viewPost($id){

    // so behind to this query is when i click the post i will obtain the the id of the post and also the user who created the post
    $post = Post::with(['user.profile'])->findOrFail($id);

    return response()->json([

        'message' => 'Post Found Successfully',
        'post' => $post,
    ] , 200);
}


// here is in the home or yung newsfeed yung mga post 
// ng mga user is may mga image or profile nila
public function getUserProfilePhoto()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    $profile = $user->profile;

    if (!$profile || !$profile->photo_profile) {
        return response()->json([
            'success' => false,
            'message' => 'Profile photo not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'photo_profile' => $profile->photo_profile
    ]);


}


// here is the part wherein we do or make a comment
// and then our comment will store in db Comment but
// there a relationship between post since
// this post related with comment 
public function makeComment(Request $request) 
{  
    $validated = $request->validate([
        'post_id' => 'required|exists:post,id',
        'content' => 'required|string|max:2000',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    // Check if post exists
    $post = Post::with('user')->find($validated['post_id']); 
    if (!$post) {
        return response()->json([
            'message' => 'Post not found'
        ], 404);
    }

    $user = auth()->user();
    
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('comments', 'public');
    }
   // now we are ready to this comment data to db Comment
    $comment = Comment::create([
        'post_id' => $validated['post_id'], 
        'user_id' => $user->id,
        'content' => $validated['content'],
        'image' => $imagePath,
    ]);


    // after that we will load our  
    //  profile and who is user commented it
  //  there a relationship betwee User to Post,Comment
    $comment->load('user.profile');
    // after that comment we will fire a even notifcation galing kay pusher
    //  then ayan yung real time notification
    event(new UserNotification($comment, $post, $user)); 


    return response()->json([
        'message' => 'Comment created successfully',
        'comment' => $comment
    ], 201);
}



 // after that we will display na yung mga comments 
 // andun yung name ,profile, user who comment
 // and also naka OrderList na yung comment
public function getComments()
{
    $postId = request()->input('post_id');

    $comments = Comment::with(['user.profile'])
                ->where('post_id', $postId)
                ->orderBy('created_at', 'desc')
                ->get();

    return response()->json([
        'comments' => $comments
    ], 200);


}



// here when we are in the home or newsfeed we can 
// display the total comment in post, ex.Post 1 = 3 Comment
public function totalComments($postId)
{
    $totalComments = Comment::where('post_id', $postId)->count();

    return response()->json([
        'total_comments' => $totalComments
    ], 200);

}

// for this function is yung karma Voting
// this function for Upvote mean you like it
public function upvote(Post $post)
{    
    // make sure na naka login is user
    $user = Auth::user();

    // if hinde return to login
    if (!$user) {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Please login to vote'
        ], 401);
    }

    $currentVote = $post->userVote($user);
    $action = 'upvoted'; 

    if ($currentVote === Karma::UPVOTE) {
        $post->removeVote($user);
        $action = 'removed';
        $newVote = null;
    } else {
        $post->upvote($user);
        $action = 'upvoted';
        $newVote = 'up';
        event(new PostUpvoted($post, $user, $post->user));
    }

        $finalScore = $post->upvotes()->count();


    return response()->json([
        'karma_score' =>$finalScore ,
        'user_vote' => $newVote, 
        'action' => $action
    ]);
}

// same lang din kay upvote same function lang 
public function downvote(Post $post)
{
    // make sure na naka login is user
    $user = Auth::user();
    // if hinde return to login
    if (!$user) {
        return response()->json([
            'error' => 'Unauthorized',
            'message' => 'Please login to vote'
        ], 401);
    }
    // nag initial na tayo ng variable na current vote we use userVote function sa post inside of it the user vote
    $currentVote = $post->userVote($user);
    //current action natin is down vote if this function run
    $action = 'downvoted'; 
    // if click ulit yung down vote it will make the action from downvoted to removed and then set new vote to null
    if ($currentVote === Karma::DOWNVOTE) {
        $post->removeVote($user);
        $action = 'removed';
        $newVote = null;
        // else if hinde naman remain lang yun
    } else {
        $post->downvote($user);
        $action = 'downvoted';
        $newVote = 'down';
        event(new PostDownvoted($post, $user, $post->user));
    }
   // upvotes total 
        $finalScore = $post->upvotes()->count();


// same logic lang to sa upvote
    return response()->json([
        'karma_score' => $finalScore,
        'user_vote' => $newVote,
        'action' => $action
    ]);
}

 public function show(Post $post)
    {
        return response()->json([
            'karma_score' => $post->karmaScore(),
            'upvotes' => $post->upvotes()->count(),
            'downvotes' => $post->downvotes()->count(),
            'user_vote' => $post->userVote(auth()->user())
        ]);
    }

    

}