@extends('admin.master')
@section('title', 'تعديل دواء صيدليتي')
@section('content')
<h1 class="h3 mb-3">تعديل {{ $product->trans_name }} في صيدليتي</h1>
@if (session('msg'))<div class="alert alert-success">{{ session('msg') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@unless ($product->is_active)<div class="alert alert-info">الدواء بانتظار تفعيل المدير للنشر في الموقع. يمكنك تحديث مخزون صيدليتك الآن.</div>@endunless
<form method="POST" action="{{ route('admin.products.update', $product) }}" class="card card-body" style="max-width:640px">
    @csrf @method('PUT')
    <label for="inventory-price">السعر عندي</label>
    <input id="inventory-price" type="number" name="price" min="0" max="999999.99" step="0.01" class="form-control mb-3" value="{{ old('price', $inventory->price ?? $product->price) }}">
    <label for="inventory-quantity">الكمية عندي</label>
    <input id="inventory-quantity" type="number" name="quantity" min="0" max="1000000" step="1" required class="form-control mb-3" value="{{ old('quantity', $inventory->quantity) }}">
    <label for="inventory-available">حالة التوفر في صيدليتي</label>
    <select id="inventory-available" name="is_available" class="form-control mb-3">
        <option value="1" @selected(old('is_available', $inventory->is_available))>متوفر عند وجود كمية</option>
        <option value="0" @selected(!old('is_available', $inventory->is_available))>غير متوفر مؤقتاً</option>
    </select>
    <label class="mb-3"><input type="hidden" name="show_price" value="0"><input type="checkbox" name="show_price" value="1" @checked(old('show_price', $inventory->show_price))> إظهار السعر</label>
    <label for="inventory-notes">ملاحظات خاصة بصيدليتي</label>
    <textarea id="inventory-notes" name="pharmacy_notes" maxlength="2000" class="form-control mb-3">{{ old('pharmacy_notes', $inventory->notes) }}</textarea>
    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    <a href="{{ route('admin.my-pharmacy.inventory') }}" class="btn btn-link mt-2">الرجوع إلى أدويتي</a>
</form>
@endsection
