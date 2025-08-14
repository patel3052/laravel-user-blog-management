@extends('layouts.app')

@section('content')
    <h2>Create Blog</h2>
    <form action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('blogs._form')
        <button class="btn btn-primary mb-5">Save</button>
        <a href="{{ route('blogs.index') }}" class="btn btn-secondary mb-5">Cancel</a>
    </form>
@endsection
