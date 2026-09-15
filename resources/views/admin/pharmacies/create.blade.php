@extends('admin.master')

@section('title', 'Add pharmacy')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.add_pharmacy') }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pharmacies.store') }}" method="POST">
            @include('admin.pharmacies._form')
        </form>
    </div>
</div>
@endsection
