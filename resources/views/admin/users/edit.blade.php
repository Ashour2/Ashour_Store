@extends('admin.master')

@section('title', 'Edit user')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.edit_user') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @method('put')
            @include('admin.users._form')
        </form>
    </div>
</div>
@endsection
