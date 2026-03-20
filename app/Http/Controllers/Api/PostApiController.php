<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function pending()
    {
        return response()->json(
            Post::pending()
                ->orderBy('created_at', 'desc')
                ->get(['id', 'title', 'excerpt', 'category', 'word_count', 'dominant_subreddit', 'created_at'])
        );
    }

    public function approve($id)
    {
        $post = Post::findOrFail($id);
        $post->approve();
        return response()->json(['success' => true, 'status' => 'approved']);
    }

    public function publish($id)
    {
        $post = Post::findOrFail($id);
        $post->publish();
        return response()->json(['success' => true, 'status' => 'published']);
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $post = Post::findOrFail($id);
        $post->reject($request->reason);
        return response()->json(['success' => true, 'status' => 'rejected']);
    }
}
