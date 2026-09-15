@extends('admin.master')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('admin.articles') }}</h1>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> {{ __('admin.add_new') }}</a>
</div>

@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
</div>
@endif

<table class="table table-bordered table-hover">
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>{{ __('admin.image') }}</th>
        <th>{{ __('admin.title') }}</th>
        <th>{{ __('admin.status') }}</th>
        <th>{{ __('admin.date') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($articles as $article)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td><img width="80" style="border-radius:6px" src="{{ $article->img_path }}" alt=""></td>
        <td>{{ $article->trans_name }}</td>
        <td>
            @if ($article->is_published)
                <span class="badge badge-success">{{ __('admin.published') }}</span>
            @else
                <span class="badge badge-secondary">{{ __('admin.draft') }}</span>
            @endif
        </td>
        <td>{{ $article->created_at->format('Y-m-d') }}</td>
        <td>
            <a class="btn btn-sm btn-info" target="_blank" href="{{ route('articles.show', $article->id) }}"><i class="fas fa-eye"></i></a>
            <a class="btn btn-sm btn-primary" href="{{ route('admin.articles.edit', $article->id) }}"><i class="fas fa-edit"></i></a>
            <form class="d-inline" action="{{ route('admin.articles.destroy', $article->id) }}" method="POST">
                @csrf @method('delete')
                <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="text-center">{{ __('admin.no_data') }}</td></tr>
    @endforelse
</table>
{{ $articles->links() }}
@endsection

@section('title', 'Articles')
