@extends('admin.master')
@section('title', 'طلبات التوريد')
@section('content')
<h1 class="h3 mb-3">طلبات التوريد</h1>
@include('admin.warehouses.alerts')
<div class="table-responsive"><table class="table table-bordered"><thead><tr><th>رقم الطلب</th><th>المستودع</th><th>الصيدلية</th><th>المبلغ</th><th>الحالة</th><th>التاريخ</th><th>التفاصيل</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td>{{ $order->number }}</td><td>{{ $order->warehouse->name }}</td><td>{{ $order->pharmacy_name }}</td><td>{{ number_format($order->total, 2) }}</td><td>{{ $order->status_label }}</td><td>{{ $order->created_at->format('Y-m-d H:i') }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.wholesale.orders.show', $order) }}">عرض</a></td></tr>
@empty<tr><td colspan="7" class="text-center">لا توجد طلبات توريد بعد.</td></tr>@endforelse
</tbody></table></div>{{ $orders->links() }}
@endsection
