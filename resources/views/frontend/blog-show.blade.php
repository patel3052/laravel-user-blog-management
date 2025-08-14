@extends('layouts.app')

@section('content')
    <div class="container my-5">
        <div class="card shadow-sm border-0">

            @if ($blog->images->count())
                <div class="text-center">
                    <img src="{{ asset('storage/' . $blog->images->first()->path) }}" alt="{{ $blog->title }}"
                        class="img-fluid blog-main-image w-100">
                </div>
            @endif

            <div class="card-body">
                <h1 class="card-title fw-bold">{{ $blog->title }}</h1>
                <p class="text-muted mb-2">
                    By <strong>{{ $blog->user->name }}</strong> •
                    {{ $blog->created_at->format('F d, Y') }}
                </p>

                <div class="mb-3">
                    @foreach ($blog->tags as $tag)
                        <span class="badge bg-primary">{{ $tag->name }}</span>
                    @endforeach
                </div>

                <p class="card-text fs-5">{!! nl2br(e($blog->description)) !!}</p>

                @if ($blog->links->count())
                    <div class="mt-4">
                        <h5>Related Links</h5>
                        @foreach ($blog->links as $link)
                            <a href="{{ $link->url }}" target="_blank" class="btn btn-outline-primary me-2 mb-2">
                                {{ $link->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if ($blog->images->count() > 1)
            <div class="mt-4">
                <h4>More Images</h4>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($blog->images->skip(1) as $img)
                        <a href="{{ asset('storage/' . $img->path) }}" data-lightbox="blog-gallery">
                            <img src="{{ asset('storage/' . $img->path) }}" class="rounded border"
                                style="width: 150px; height: 100px; object-fit: contain;">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>


@endsection
