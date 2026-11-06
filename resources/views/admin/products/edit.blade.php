@extends('admin.master')

@section('content')
<style>.sticky-save-bar{position:sticky;top:8px;z-index:1020;background:#fff;padding:10px 0;border-bottom:1px solid #e9ecef;box-shadow:0 4px 10px rgba(15,23,42,.08)}</style>
<!-- Page Heading -->
<h1 class="h3 mb-3 text-gray-800">تعديل الدواء</h1>

<form id="productForm" class="product-edit-form" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')

    <div class="sticky-save-bar mb-4"><button class="btn btn-info px-4"><i class="fas fa-save"></i> حفظ التعديلات</button></div>

    @include('admin.products._form')

    <button class="btn btn-info"><i class="fas fa-save"></i> حفظ التعديلات</button>
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

    function delImg(e, id) {
        // console.log(e.target.parentElement);
        // console.log(e.target.closest('div'));
        $.ajax({
            type: 'get',
            url: '{{ route("admin.delete_img") }}/'+id,
            success: (res) => {
                if(res) {
                    e.target.parentElement.remove();
                }
            },
            error: (err) => {
                console.log(err);
            }
        })
    }
</script>
@endsection
