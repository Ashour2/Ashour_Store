@extends('admin.master')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ __('admin.edit') }} — {{ __('admin.articles') }}</h1>

<form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')
    @include('admin.articles._form')
    <button class="btn btn-success"><i class="fas fa-save"></i> {{ __('admin.save') }}</button>
</form>
@endsection

@section('title', 'Edit Article')
