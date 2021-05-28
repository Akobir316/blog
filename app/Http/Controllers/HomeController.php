<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::with('category')->orderBy('id','desc')->paginate(2);

        return view('front.index', compact('posts'));
    }

    public function showPost($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        Post::setRecentlyViewed($post->id);
        $post->views += 1;
        $post->update();
      return view('front.post', compact('post'));
    }
}
