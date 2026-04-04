@extends('admin.master')

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">Edit Product</h1>

<form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('put')

    @include('admin.products._form')

    <button class="btn btn-info"><i class="fas fa-save"></i> Update</button>
</form>

@endsection

@section('title', 'Dashboard')

@section('js')
<script>
    function showImg(e) {
        const [file] = e.target.files
        if (file) {
            preview.src = URL.createObjectURL(file)
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
