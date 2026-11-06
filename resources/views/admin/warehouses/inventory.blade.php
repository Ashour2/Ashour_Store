@extends('admin.master')
@section('title', 'مخزون المستودع')
@section('content')
<h1 class="h3">أدوية وأسعار {{ $warehouse->name }}</h1><p class="text-muted">حدّد سعر الجملة والكمية المتاحة للصيدليات. الكمية المعروضة لا تشمل الكميات المحجوزة للطلبات القائمة.</p>
@unless($warehouse->is_active)<div class="alert alert-warning">المستودع متوقف عن استقبال طلبات جديدة بقرار الإدارة.</div>@endunless
@include('admin.warehouses.alerts')
<form method="GET" class="input-group mb-3" style="max-width:500px"><input class="form-control" name="q" value="{{ $q }}" placeholder="ابحث عن دواء أو باركود" aria-label="البحث عن دواء"><div class="input-group-append"><button class="btn btn-primary">بحث</button></div></form>
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>الدواء</th><th>التصنيف</th><th>السعر والكمية المتاحة</th></tr></thead><tbody>
@forelse($products as $product)
@php $stock = $stocks->get($product->id); @endphp
<tr><td>{{ $product->trans_name }}</td><td>{{ $product->category->trans_name }}</td><td>
<form method="POST" action="{{ route('admin.warehouse.inventory.save', $product) }}" class="d-flex flex-wrap align-items-end" style="gap:10px">@csrf
<label>سعر الجملة<input type="number" name="price" min="0" max="999999.99" step="0.01" required value="{{ $stock?->price ?? $product->price }}" class="form-control" style="width:130px"></label>
<label>الكمية المتاحة<input type="number" name="quantity" min="0" max="1000000" step="1" required value="{{ $stock?->quantity ?? 0 }}" class="form-control" style="width:120px"></label>
<label>التوفر<select name="is_available" class="form-control"><option value="1" @selected($stock?->is_available ?? true)>متوفر</option><option value="0" @selected($stock && !$stock->is_available)>غير متوفر مؤقتاً</option></select></label>
<button class="btn btn-primary mb-2">{{ $stock ? 'حفظ' : 'إضافة لمخزوني' }}</button></form></td></tr>
@empty<tr><td colspan="3" class="text-center">لا توجد أدوية مطابقة.</td></tr>@endforelse
</tbody></table></div>{{ $products->links() }}
@endsection
