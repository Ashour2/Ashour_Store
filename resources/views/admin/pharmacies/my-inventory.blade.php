@extends('admin.master')

@section('title', 'أدويتي')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h3 mb-1 text-gray-800">أدويتي في {{ $pharmacy->trans_name }}</h1><p class="text-muted mb-0">أضف الأدوية إلى مخزون صيدليتك فقط، ثم حدّد كمية كل دواء.</p></div>
    <form method="POST" action="{{ route('admin.my-pharmacy.attach-all') }}" onsubmit="return confirm('هل تريد إضافة جميع الأدوية إلى صيدليتك بكمية صفر؟');">
        @csrf <button class="btn btn-success"><i class="fas fa-plus-double"></i> إضافة جميع الأدوية إلى صيدليتي</button>
    </form>
</div>
<div class="mb-3 d-flex flex-wrap" style="gap:.5rem">
    <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus-circle"></i> إضافة دواء يدوي</a>
    <a href="{{ route('admin.products.import.form') }}" class="btn btn-outline-success"><i class="fas fa-file-excel"></i> إضافة أدوية من Excel</a>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-outline-secondary"><i class="fas fa-tags"></i> إضافة تصنيف</a>
</div>
@if (session('msg'))<div class="alert alert-{{ session('type', 'info') }}">{{ session('msg') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card mb-3 border-0 shadow-sm"><div class="card-body"><details><summary class="text-primary" style="cursor:pointer"><i class="fas fa-lightbulb"></i> اقترح ميزة أو تصنيفاً جديداً لمدير المنصة</summary><form method="POST" action="{{ route('admin.my-pharmacy.suggestions.store') }}" class="mt-3">@csrf <input name="title" class="form-control mb-2" placeholder="عنوان الاقتراح" required><textarea name="message" class="form-control mb-2" rows="3" placeholder="اشرح اقتراحك أو التصنيف المطلوب" required></textarea><button class="btn btn-outline-primary btn-sm">إرسال الاقتراح</button></form></details></div></div>
<form method="GET" action="{{ route('admin.my-pharmacy.inventory') }}" class="mb-3" role="search">
    <div class="input-group" style="max-width: 480px;">
        <input type="search" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="ابحث باسم الدواء أو المادة الفعالة أو الباركود">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary" title="بحث عن الأدوية" aria-label="بحث عن الأدوية"><i class="fas fa-search"></i></button>
        </div>
        @if ($q ?? false)
            <a href="{{ route('admin.my-pharmacy.inventory') }}" class="btn btn-outline-secondary">مسح</a>
        @endif
    </div>
</form>
<table class="table table-bordered table-hover bg-white">
    <tr class="bg-dark text-white"><th>#</th><th>الصورة</th><th>الدواء</th><th>التصنيف</th><th>المخزون عندي</th><th>السعر عندي</th><th>إظهار السعر</th><th>إجراء</th></tr>
    @foreach ($products as $product)
        <tr><td>{{ $loop->iteration }}</td><td><img src="{{ $product->img_path }}" width="48" height="48" style="object-fit:cover"></td><td>{{ $product->trans_name }}</td><td>{{ $product->category->trans_name }}</td>
            <td>@if($held->has($product->id))<span class="badge {{ ($held[$product->id] > 0 && ($availability[$product->id] ?? true)) ? 'badge-success' : 'badge-secondary' }}">{{ $held[$product->id] }}</span><small class="d-block">{{ ($held[$product->id] > 0 && ($availability[$product->id] ?? true)) ? 'متوفر في صيدليتي' : 'غير متوفر في صيدليتي' }}</small>@else<span class="text-muted">غير مضاف</span>@endif
            @unless($product->is_active)<small class="d-block text-muted">بانتظار تفعيل المدير للنشر</small>@endunless</td>
            <td colspan="3">
                <div class="d-flex align-items-center flex-wrap" style="gap:.4rem">
                    <form method="POST" action="{{ route('admin.my-pharmacy.attach', $product) }}" class="form-inline">@csrf
                        <input type="number" name="quantity" min="0" value="{{ $held[$product->id] ?? 0 }}" class="form-control form-control-sm mr-2" style="width:70px" title="الكمية">
                        <input type="number" step="0.01" min="0" name="price" value="{{ $prices[$product->id] ?? $product->price }}" class="form-control form-control-sm mr-2" style="width:80px" title="سعرك الخاص">
                        <select name="is_available" class="form-control form-control-sm mr-2" title="حالة التوفر"><option value="1" @selected($availability[$product->id] ?? true)>متوفر عند وجود كمية</option><option value="0" @selected(!($availability[$product->id] ?? true))>غير متوفر مؤقتاً</option></select>
                        <label class="small mr-2 mb-0"><input type="hidden" name="show_price" value="0"><input type="checkbox" name="show_price" value="1" @checked(($priceVisibility[$product->id] ?? $product->show_price))> إظهار السعر</label>
                        <button class="btn btn-sm {{ $held->has($product->id) ? 'btn-outline-primary' : 'btn-success' }}">{{ $held->has($product->id) ? 'حفظ' : 'إضافة إلى أدويتي' }}</button>
                    </form>
                    @if ($held->has($product->id))
                        <form method="POST" action="{{ route('admin.my-pharmacy.detach', $product) }}" onsubmit="return confirm('هل تريد إزالة هذا الدواء من أدويتي فقط؟');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="إزالة من أدويتي"><i class="fas fa-trash-alt"></i></button></form>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
</table>
{{ $products->links() }}
@endsection
