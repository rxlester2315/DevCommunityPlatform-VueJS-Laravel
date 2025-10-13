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
    


}