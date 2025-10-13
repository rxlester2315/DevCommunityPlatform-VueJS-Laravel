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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'text_content' => $validated['content'],
            'image' => $imagePath,
            'likes_count' => 0,
            'comments_count' => 0,
            'published_at' => now(),
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
    


}