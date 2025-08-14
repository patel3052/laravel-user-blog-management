@extends('layouts.app')

@section('content')
<h2>Edit User</h2>

<form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('users.form', ['user' => $user])
    <button type="submit" class="btn btn-primary mb-5">Update</button>
    <a href="{{ route('users.index') }}" class="btn btn-secondary mb-5">Cancel</a>
</form>
@endsection
