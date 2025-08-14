@extends('layouts.app')

@section('content')
<h2>Create User</h2>

<form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('users.form')
    <button type="submit" class="btn btn-primary mb-5">Save</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mb-5">Cancel</a>
</form>
@endsection
