@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>{{ $blog->title }}</h2>
        <a href="{{ route('blogs.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="mb-2 text-muted">
        By <strong>{{ $blog->user->name }}</strong> · {{ $blog->created_at->format('M d, Y') }}
    </div>

    @if ($blog->tags->count())
        <div class="mb-3">
            @foreach ($blog->tags as $t)
                <span class="badge bg-primary">{{ $t->name }}</span>
            @endforeach
        </div>
    @endif

    <div class="mb-4">
        {!! nl2br(e($blog->description)) !!}
    </div>

    @if ($blog->images->count())
        <div class="mb-4 d-flex gap-2 flex-wrap">
            @foreach ($blog->images as $img)
                <img src="{{ asset('storage/' . $img->path) }}" width="180" class="border rounded">
            @endforeach
        </div>
    @endif

    @if ($blog->links->count())
        <div class="mb-4">
            @foreach ($blog->links as $lnk)
                <a href="{{ $lnk->url }}" target="_blank" class="btn btn-outline-info me-2 mb-2">
                    {{ $lnk->title }}
                </a>
            @endforeach
        </div>
    @endif
@endsection
