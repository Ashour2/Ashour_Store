@extends('admin.master')
@section('title', 'المستودعات الدوائية')
@section('content')
<div class="d-flex justify-content-between mb-3"><h1 class="h3">المستودعات الدوائية</h1><a class="btn btn-outline-primary" href="{{ route('admin.wholesale.orders') }}">طلبات التوريد الخاصة بصيدليتي</a></div>
<p class="text-muted">اطلب احتياجات صيدليتك من المستودع مباشرة. الدفع والتسوية خارج المنصة.</p>
<div class="row">@forelse($warehouses as $warehouse)<div class="col-md-6 col-xl-4 mb-3"><article class="card card-body h-100"><h2 class="h5">{{ $warehouse->name }}</h2><p>{{ $warehouse->address }}</p><p dir="ltr">{{ $warehouse->phone }}</p><p>{{ $warehouse->stocks_count }} دواء متوفر</p><a href="{{ route('admin.wholesale.warehouse', $warehouse) }}" class="btn btn-primary">عرض الأدوية والأسعار</a></article></div>@empty<div class="col-12"><div class="card card-body text-center">لا توجد مستودعات متاحة حالياً.</div></div>@endforelse</div>
{{ $warehouses->links() }}
@endsection
