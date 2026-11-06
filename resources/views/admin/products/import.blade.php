@extends('admin.master')

@section('title', 'استيراد الأدوية')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">استيراد الأدوية من Excel</h1>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">رجوع للأدوية</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p>ارفع ملف <strong>Excel أو CSV</strong> يحتوي حتى 10,000 دواء. الملف يتم فحصه كاملًا أولًا؛ إذا وُجد خطأ فلن تُضاف أي بيانات.</p>
            <p>الأعمدة الإلزامية هي: <code>name_en</code> و<code>price</code> و<code>quantity</code>. عمود <code>category</code> اختياري، ويقبل اسم التصنيف بالعربي/الإنجليزي أو رقمه في النظام.</p>
            <a href="{{ route('admin.products.import.template') }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-download"></i> تنزيل قالب Excel
            </a>

            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.products.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="file">ملف الأدوية</label>
                    <input id="file" name="file" type="file" class="form-control-file" accept=".xlsx,.xls,.csv" required>
                    <small class="form-text text-muted">الحد الأقصى للحجم: 20 MB. لا تُدرج الصور في هذا الاستيراد.</small>
                </div>
                <button class="btn btn-success" type="submit"><i class="fas fa-upload"></i> ابدأ الاستيراد</button>
            </form>
        </div>
    </div>
@endsection
