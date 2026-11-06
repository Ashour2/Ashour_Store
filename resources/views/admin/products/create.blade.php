@extends('admin.master')

@section('content')
<style>.sticky-save-bar{position:sticky;top:8px;z-index:1020;background:#fff;padding:10px 0;border-bottom:1px solid #e9ecef;box-shadow:0 4px 10px rgba(15,23,42,.08)}</style>
<!-- Page Heading -->
<h1 class="h3 mb-3 text-gray-800">إضافة دواء جديد</h1>

@if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
<form id="productForm" class="product-edit-form" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="sticky-save-bar mb-4"><button class="btn btn-success px-4"><i class="fas fa-save"></i> حفظ الدواء</button></div>

    @include('admin.products._form')

    <button class="btn btn-success"><i class="fas fa-save"></i> حفظ الدواء</button>
</form>

@unless (auth()->user()->isPharmacist())
    @include('admin.products._add-pharmacy-modal')
@endunless

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
</script>
@endsection
