@extends('layouts.app')

@section('content')
    <h2>Edit Blog</h2>
    <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('blogs._form', ['blog' => $blog])
        <button class="btn btn-primary mb-5">Update</button>
        <a href="{{ route('blogs.index') }}" class="btn btn-secondary mb-5">Cancel</a>
    </form>
@endsection
