@extends('admin.master')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.add_new') }} — {{ __('admin.articles') }}</h1>

<form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.articles._form')
    <button class="btn btn-success"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
</form>
@endsection

@section('title', 'New Article')
