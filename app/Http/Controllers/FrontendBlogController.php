<?php

namespace App\Http\Controllers;

use App\Models\Blog;

class FrontendBlogController extends Controller
{
    public function show($slug)
    {
        $blog = Blog::with(['user:id,name', 'tags:id,name', 'images', 'links'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('frontend.blog-show', compact('blog'));
    }
}
