<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use App\Models\BlogImage;
use App\Models\BlogLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    public function index()
    {
        $isAdmin = Auth::id() === 1;
        $users   = $isAdmin ? User::get(['id', 'name']) : collect();
        $tags    = Tag::orderBy('name')->get(['id', 'name']);

        return view('blogs.index', compact('isAdmin', 'users', 'tags'));
    }

    public function data(Request $request)
    {
        $query = Blog::with(['user:id,name', 'tags:id,name'])
            ->select(['id', 'user_id', 'title', 'slug', 'description', 'created_at']);

        if (Auth::id() !== 1) {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tags')) {
            $tagIds = is_array($request->tags) ? $request->tags : [$request->tags];
            $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            });
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('user_name', fn($b) => e($b->user?->name ?? '-'))
            ->addColumn('tags_list', fn($b) => e($b->tags->pluck('name')->join(', ')))
            ->addColumn('images', function ($b) {
                if ($b->images->count()) {
                    $html = '';
                    foreach ($b->images as $index => $img) {
                        $url = asset('storage/' . $img->path);
                        if ($index === 0) {
                            $html .= '<div class="image-hover-wrapper">
                                        <a href="' . $url . '" data-lightbox="blog-' . $b->id . '" data-title="' . e($b->title) . '">
                                            <img src="' . $url . '" class="blog-thumb">
                                        </a>
                                        <div class="image-hover-popup">';
                            foreach ($b->images as $imgs) {
                                $url = asset('storage/' . $imgs->path);
                                $html .= '<img src="' . $url . '" class="blog-hover-thumb m-1">';
                            }
                            $html .= '</div></div>';
                        } else {
                            $html .= '<a href="' . $url . '" data-lightbox="blog-' . $b->id . '" data-title="' . e($b->title) . '" style="display:none;"></a>';
                        }
                    }
                    return $html;
                }
                return '';
            })
            ->addColumn('action', function ($b) {
                $publicUrl = route('frontend.blog.show', $b->slug);
                return '
                    <button type="button" class="btn btn-success btn-sm copy-link" data-link="' . $publicUrl . '">Copy Link</button>
                    <a href="' . route('blogs.show', $b->id) . '" class="btn btn-info btn-sm">View</a>
                    <a href="' . route('blogs.edit', $b->id) . '" class="btn btn-warning btn-sm">Edit</a>
                    <form action="' . route('blogs.destroy', $b->id) . '" method="POST" style="display:inline">'
                    . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure want to delete?\')">Delete</button>
                    </form>';
            })
            ->rawColumns(['images', 'action'])
            ->make(true);
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('blogs.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'         => ['required', 'string', 'max:255', 'unique:blogs,title'],
            'description'   => ['required', 'string', function ($attr, $val, $fail) {
                if (str_word_count(strip_tags($val)) < 10) $fail('Description must be at least 10 words.');
            }],
            'images.*'      => ['nullable', 'image', 'max:4096'],
            'tags'          => ['nullable', 'array'],
            'tags_input'    => ['nullable', 'string', 'max:255'],
            'link_titles'   => ['nullable', 'array'],
            'link_titles.*' => ['nullable', 'string', 'max:255'],
            'link_urls'     => ['nullable', 'array'],
            'link_urls.*'   => ['nullable', 'url', 'max:255'],
        ]);

        $titles = $request->input('link_titles', []);
        $urls   = $request->input('link_urls', []);
        foreach ($titles as $i => $t) {
            $t = trim((string)$t);
            $u = trim((string)($urls[$i] ?? ''));
            if (($t && !$u) || ($u && !$t)) {
                return back()->withInput()->withErrors(['links' => 'Each link must have both Title and URL.']);
            }
        }

        $blog = Blog::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        $rawTags = json_decode($request->tags_input, true) ?? [];
        $tagIds = [];
        foreach ($rawTags as $tagItem) {
            $tagName = trim($tagItem['value'] ?? '');
            if ($tagName !== '') {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }
        $blog->tags()->sync($tagIds);

        // Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext       = $file->getClientOriginalExtension();
                $newName   = $filename . '_' . time() . '_' . uniqid() . '.' . $ext;
                $path      = $file->storeAs('blogs', $newName, 'public');

                $blog->images()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Links
        foreach ($titles as $i => $t) {
            $t = trim((string)$t);
            $u = trim((string)($urls[$i] ?? ''));
            if ($t && $u) {
                $blog->links()->create(['title' => $t, 'url' => $u]);
            }
        }

        return redirect()->route('blogs.index')->with('success', 'Blog created successfully.');
    }

    public function show(Blog $blog)
    {
        $this->authorizeViewOrOwn($blog);
        $blog->load(['user', 'tags', 'images', 'links']);
        return view('blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $this->authorizeViewOrOwn($blog);
        $blog->load(['tags', 'images', 'links']);
        $tags = Tag::orderBy('name')->get();
        return view('blogs.edit', compact('blog', 'tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $this->authorizeViewOrOwn($blog);

        $request->validate([
            'title'         => ['required', 'string', 'max:255', Rule::unique('blogs', 'title')->ignore($blog->id)],
            'description'   => ['required', 'string', function ($attr, $val, $fail) {
                if (str_word_count(strip_tags($val)) < 10) $fail('Description must be at least 10 words.');
            }],
            'images.*'      => ['nullable', 'image', 'max:4096'],
            'tags'          => ['nullable', 'array'],
            'tags_input'    => ['nullable', 'string', 'max:255'],
            'link_titles'   => ['nullable', 'array'],
            'link_titles.*' => ['nullable', 'string', 'max:255'],
            'link_urls'     => ['nullable', 'array'],
            'link_urls.*'   => ['nullable', 'url', 'max:255'],
        ]);

        // pair check
        $titles = $request->input('link_titles', []);
        $urls   = $request->input('link_urls', []);
        foreach ($titles as $i => $t) {
            $t = trim((string)$t);
            $u = trim((string)($urls[$i] ?? ''));
            if (($t && !$u) || ($u && !$t)) {
                return back()->withInput()->withErrors(['links' => 'Each link must have both Title and URL.']);
            }
        }

        $blog->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        $rawTags = json_decode($request->tags_input, true) ?? [];
        $tagIds = [];
        foreach ($rawTags as $tagItem) {
            $tagName = trim($tagItem['value'] ?? '');
            if ($tagName !== '') {
                $tag = \App\Models\Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
        }
        $blog->tags()->sync($tagIds);

        // New images (existing are kept; you can delete individually via AJAX)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $ext       = $file->getClientOriginalExtension();
                $newName   = $filename . '_' . time() . '_' . uniqid() . '.' . $ext;
                $path      = $file->storeAs('blogs', $newName, 'public');
                $blog->images()->create([
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        // Replace links
        $blog->links()->delete();
        foreach ($titles as $i => $t) {
            $t = trim((string)$t);
            $u = trim((string)($urls[$i] ?? ''));
            if ($t && $u) {
                $blog->links()->create(['title' => $t, 'url' => $u]);
            }
        }

        return redirect()->route('blogs.index')->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog)
    {
        $this->authorizeViewOrOwn($blog);

        foreach ($blog->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        $blog->delete();

        return redirect()->route('blogs.index')->with('success', 'Blog deleted successfully.');
    }

    public function destroyImage(BlogImage $image)
    {
        $blog = $image->blog;
        $this->authorizeViewOrOwn($blog);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['status' => true]);
    }

    protected function authorizeViewOrOwn(Blog $blog)
    {
        if (Auth::id() === 1) return;
        abort_unless($blog->user_id === Auth::id(), 403);
    }
}
