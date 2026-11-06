@extends('admin.master')
@section('title', 'طلب من المستودع')
@section('content')
<h1 class="h3">طلب من {{ $warehouse->name }}</h1><p>{{ $warehouse->address }} · {{ $warehouse->phone }}</p>
@include('admin.warehouses.alerts')
<a href="{{ route('admin.wholesale.catalog') }}" class="btn btn-link mb-3">الرجوع للمستودعات</a>
<form method="GET" class="input-group mb-3" style="max-width:500px"><input class="form-control" name="q" value="{{ $q }}" placeholder="ابحث عن دواء" aria-label="البحث عن دواء"><div class="input-group-append"><button class="btn btn-primary">بحث</button></div></form>
<form method="POST" action="{{ route('admin.wholesale.store', $warehouse) }}">@csrf
<input type="hidden" name="request_token" value="{{ old('request_token', (string) Illuminate\Support\Str::uuid()) }}">
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>الدواء</th><th>سعر الوحدة بالجملة</th><th>المتاح</th><th>الكمية المطلوبة</th></tr></thead><tbody>
@forelse($stocks as $stock)<tr><td>{{ $stock->product->trans_name }}</td><td>{{ number_format($stock->price, 2) }}</td><td>{{ $stock->quantity }}</td><td><input type="number" name="quantities[{{ $stock->id }}]" data-wholesale-price="{{ $stock->price }}" min="0" max="{{ $stock->quantity }}" step="1" value="{{ old('quantities.'.$stock->id, 0) }}" class="form-control" style="max-width:140px" aria-label="الكمية المطلوبة من {{ $stock->product->trans_name }}"></td></tr>
@empty<tr><td colspan="4" class="text-center">لا توجد أدوية متوفرة مطابقة.</td></tr>@endforelse
</tbody></table></div>
@if($stocks->isNotEmpty())
<div class="card card-body mb-3"><p class="text-muted">اختر كميات الأدوية من هذه الصفحة وأرسل الطلب. تُعتمد الأسعار النهائية عند إرسال الطلب، والدفع خارج المنصة.</p>
<p class="h5 mb-3">الإجمالي التقديري: <output id="wholesale-total" aria-live="polite">0.00</output></p>
<label for="delivery-address">عنوان تسليم الطلب</label><input id="delivery-address" name="delivery_address" required maxlength="255" class="form-control mb-3" value="{{ old('delivery_address', auth()->user()->pharmacy->address) }}">
<label for="wholesale-notes">ملاحظات للمستودع</label><textarea id="wholesale-notes" name="notes" maxlength="2000" class="form-control mb-3">{{ old('notes') }}</textarea>
<button class="btn btn-primary">إرسال الطلب للمستودع</button></div>
@endif
</form>{{ $stocks->links() }}
@endsection
@section('scripts')
<script>
    const wholesaleQuantities = document.querySelectorAll('[data-wholesale-price]');
    const wholesaleTotal = document.getElementById('wholesale-total');
    function updateWholesaleTotal() {
        if (!wholesaleTotal) return;
        let total = 0;
        wholesaleQuantities.forEach(input => total += Math.max(0, Number(input.value) || 0) * Number(input.dataset.wholesalePrice));
        wholesaleTotal.value = total.toFixed(2);
    }
    wholesaleQuantities.forEach(input => input.addEventListener('input', updateWholesaleTotal));
    updateWholesaleTotal();
</script>
@endsection
