@extends('admin.master')

@section('title', 'Edit pharmacy')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.edit_pharmacy') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pharmacies.update', $pharmacy->id) }}" method="POST">
            @method('put')
            @include('admin.pharmacies._form')
        </form>
    </div>
</div>
@endsection
