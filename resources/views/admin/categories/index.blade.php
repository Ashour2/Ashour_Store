@extends('admin.master')

@section('content')
<!-- Page Heading -->
<div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
    <h1 class="h3 mb-0 text-gray-800">كل التصنيفات</h1>
    <div class="mt-2 mt-md-0">
        @can('categories.create')
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> إضافة جديد</a>
            <a href="{{ route('admin.categories.import.form') }}" class="btn btn-outline-success"><i class="fas fa-file-import"></i> استيراد من Excel</a>
        @endcan
        @can('categories.view')
            <a href="{{ route('admin.categories.export.excel') }}" class="btn btn-outline-primary"><i class="fas fa-file-excel"></i> تصدير Excel</a>
            <a href="{{ route('admin.categories.export.pdf') }}" target="_blank" class="btn btn-outline-danger"><i class="fas fa-file-pdf"></i> تصدير PDF</a>
        @endcan
    </div>
</div>
@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif
<table class="table table-bordered table-hover" >
    <tr class="bg-dark text-white">
        <th>#</th>
        <th>الصورة</th>
        <th>Name</th>
        <th>Products Count</th>
        <th>ترتيب واجهات الموقع</th>
        <th>Actions</th>
    </tr>
    @forelse ($categories as $category)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td class="text-center">
            <img src="{{ $category->img_path }}" alt="{{ $category->trans_name }}" style="width:52px;height:42px;object-fit:cover;border-radius:5px;">
        </td>
        <td>{{ $category->trans_name }}</td>
        {{-- <td>{{ $category->name }}</td> --}}
        {{-- <td>{{json_decode($category->name,true)[app()->getLocale()] ?? ""}}</td> --}}
        <td>{{ $category->products_count }}</td>
        <td><span class="badge badge-info">{{ $category->home_display_order ?: 999 }}</span></td>
        <td>
            @can('categories.edit')
            <a class="btn btn-sm btn-primary" href="{{ route('admin.categories.edit', $category->id) }}"><i class="fas fa-edit"></i></a>
            @endcan
            @can('categories.delete')
            <form class="d-inline" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                @csrf
                @method('delete')
                <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
            @endcan
        </td>
    </tr>
    @empty
        <tr>
        <td colspan="6" class="text-center">No Data Found</td>
    </tr>
    @endforelse

</table>
{{ $categories->links() }}
@endsection

@section('title', 'Dashboard')
