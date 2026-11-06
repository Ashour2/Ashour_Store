@extends('admin.master')

@section('content')
<style>
    .sticky-save-bar { position: sticky; top: 8px; z-index: 1020; background: #fff; padding: 10px 0; border-bottom: 1px solid #e9ecef; box-shadow: 0 4px 10px rgba(15,23,42,.08); }
</style>
<h1 class="h3 mb-3 text-gray-800">تعديل التصنيف</h1>

@if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<form id="categoryForm" class="category-edit-form" action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="sticky-save-bar mb-4"><button class="btn btn-info px-4"><i class="fas fa-save"></i> حفظ التعديلات</button></div>

    @include('admin.categories._form')

    <button class="btn btn-info"><i class="fas fa-save"></i> حفظ التعديلات</button>
</form>

@endsection

@section('title', 'Dashboard')

@section('js')
<script>
    function showImg(e) {
        const [file] = e.target.files
        if (file) {
            preview.src = URL.createObjectURL(file)
            preview.classList.remove('d-none')
        }
    }

    setupCategoryUnsavedWarning();
</script>
@endsection
