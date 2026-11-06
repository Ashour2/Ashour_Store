@extends('admin.master')

@section('title', 'استيراد التصنيفات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">استيراد التصنيفات من Excel</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">رجوع للتصنيفات</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p>نزّل القالب، أضف التصنيفات ثم ارفعه. الأعمدة المطلوبة: <code>name_en</code> و<code>name_ar</code>.</p>
            <p class="text-muted">الصور لا تُرفع من ملف Excel؛ بعد الاستيراد افتح التصنيف من زر التعديل وأضف صورته.</p>
            <a href="{{ route('admin.categories.import.template') }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-download"></i> تنزيل قالب Excel
            </a>
            @if ($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
            <form method="POST" action="{{ route('admin.categories.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">ملف التصنيفات</label>
                    <input id="file" name="file" type="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                </div>
                <button class="btn btn-success"><i class="fas fa-upload"></i> ابدأ الاستيراد</button>
            </form>
        </div>
    </div>
@endsection
