@extends('admin.master')
@section('title', 'تفاصيل طلب التوريد')
@section('content')
<h1 class="h3 mb-3">طلب التوريد {{ $order->number }}</h1>
@include('admin.warehouses.alerts')
<div class="card card-body mb-3"><p><strong>المستودع:</strong> {{ $order->warehouse->name }} · {{ $order->warehouse->phone }}</p><p><strong>الصيدلية:</strong> {{ $order->pharmacy_name }} · {{ $order->pharmacy_phone }}</p><p><strong>التسليم:</strong> {{ $order->delivery_address }}</p><p><strong>الحالة:</strong> {{ $order->status_label }}</p><p><strong>ملاحظات:</strong> {{ $order->notes ?: '—' }}</p><p class="text-muted mb-0">الدفع والتسوية خارج المنصة.</p></div>
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>الدواء</th><th>سعر الوحدة</th><th>الكمية</th><th>الإجمالي</th></tr></thead><tbody>@foreach($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ number_format($item->price, 2) }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->total, 2) }}</td></tr>@endforeach</tbody><tfoot><tr><th colspan="3">إجمالي الطلب</th><th>{{ number_format($order->total, 2) }}</th></tr></tfoot></table></div>
@php
    $next = ['new' => ['accepted', 'cancelled'], 'accepted' => ['preparing', 'cancelled'], 'preparing' => ['completed', 'cancelled']];
    $actions = auth()->user()->isWarehouse() ? ($next[$order->status] ?? []) : (auth()->user()->isPharmacist() && $order->status === 'new' ? ['cancelled'] : []);
@endphp
<div class="d-flex flex-wrap mb-3" style="gap:10px">@foreach($actions as $status)<form method="POST" action="{{ route('admin.wholesale.orders.status', $order) }}" @if($status === 'cancelled') onsubmit="return confirm('إلغاء الطلب وإرجاع الكميات لمخزون المستودع؟');" @endif>@csrf @method('PUT')<input type="hidden" name="status" value="{{ $status }}"><button class="btn {{ $status === 'cancelled' ? 'btn-outline-danger' : 'btn-primary' }}">{{ App\Models\WholesaleOrder::STATUSES[$status] }}</button></form>@endforeach</div>
<a href="{{ route('admin.wholesale.orders') }}" class="btn btn-link">الرجوع للطلبات</a>
@endsection
