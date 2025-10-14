<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
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
        $posts = Post::with(['user' => function($query){
            $query->select('id','name','email');
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

}