@extends('admin.master')

@section('title', 'Add user')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.add_user') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @include('admin.users._form')
        </form>
    </div>
</div>
@endsection
