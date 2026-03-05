<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Mostrar lista de posts
    public function index()
    {
        $posts = Post::latest()->get();
        return view('admin.posts.index', compact('posts'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('admin.posts.create');
    }

    // Guardar nuevo post
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $post = new Post();
        $post->title = $request->title;
        // Crear slug automático desde el título (Mi Primer Post -> mi-primer-post)
        $post->slug = Str::slug($request->title);
        $post->content = $request->content;
        $post->is_published = true; // Publicar directamente por ahora

        // Subir Imagen
        if ($request->hasFile('image')) {
            // Guardar en 'storage/app/public/posts'
            $path = $request->file('image')->store('posts', 'public');
            $post->image = $path;
        }

        $post->save();

        return redirect()->route('admin.posts.index')->with('success', '¡Artículo publicado con éxito!');
    }
}
