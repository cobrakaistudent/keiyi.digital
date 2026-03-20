<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::published()
            ->get(['id', 'title', 'slug', 'excerpt', 'category', 'word_count', 'dominant_subreddit', 'published_at']);

        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
