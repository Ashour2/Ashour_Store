@extends('admin.master')
@section('title', 'إدارة المستودعات')
@section('content')
<h1 class="h3 mb-3">إدارة المستودعات الدوائية</h1>
@include('admin.warehouses.alerts')
<details class="card card-body mb-4" @if($errors->any()) open @endif><summary class="text-primary">إضافة مستودع وحساب مسؤول</summary>
<form method="POST" action="{{ route('admin.warehouses.store') }}" class="mt-3">@csrf
<div class="row">
@foreach (['name' => 'اسم المستودع', 'phone' => 'الهاتف', 'address' => 'العنوان', 'manager_name' => 'اسم المسؤول', 'email' => 'البريد الإلكتروني للمسؤول'] as $field => $label)
<div class="col-md-6 mb-3"><label for="new-{{ $field }}">{{ $label }}</label><input id="new-{{ $field }}" name="{{ $field }}" type="{{ $field === 'email' ? 'email' : 'text' }}" class="form-control" value="{{ old($field) }}" @required(in_array($field, ['name', 'manager_name', 'email']))></div>
@endforeach
<div class="col-md-6 mb-3"><label for="new-password">كلمة المرور</label><input id="new-password" type="password" name="password" minlength="8" required class="form-control" autocomplete="new-password"></div>
<div class="col-md-6 mb-3"><label for="new-password-confirm">تأكيد كلمة المرور</label><input id="new-password-confirm" type="password" name="password_confirmation" minlength="8" required class="form-control" autocomplete="new-password"></div>
</div><button class="btn btn-primary">إنشاء المستودع</button></form></details>
@forelse ($warehouses as $warehouse)
<article class="card card-body mb-3"><h2 class="h5">{{ $warehouse->name }}</h2><p class="text-muted">{{ $warehouse->stocks_count }} دواء · {{ $warehouse->orders_count }} طلب</p>
@foreach($warehouse->users as $manager)<p>المسؤول: {{ $manager->name }} — {{ $manager->email }}</p>@endforeach
<form method="POST" action="{{ route('admin.warehouses.update', $warehouse) }}">@csrf @method('PUT')
<div class="row"><div class="col-md-3"><label for="name-{{ $warehouse->id }}">الاسم</label><input id="name-{{ $warehouse->id }}" class="form-control mb-2" name="name" value="{{ $warehouse->name }}" required></div>
<div class="col-md-3"><label for="phone-{{ $warehouse->id }}">الهاتف</label><input id="phone-{{ $warehouse->id }}" class="form-control mb-2" name="phone" value="{{ $warehouse->phone }}"></div>
<div class="col-md-4"><label for="address-{{ $warehouse->id }}">العنوان</label><input id="address-{{ $warehouse->id }}" class="form-control mb-2" name="address" value="{{ $warehouse->address }}"></div>
<div class="col-md-2"><label for="active-{{ $warehouse->id }}">الحالة</label><select id="active-{{ $warehouse->id }}" name="is_active" class="form-control mb-2"><option value="1" @selected($warehouse->is_active)>فعّال</option><option value="0" @selected(!$warehouse->is_active)>متوقف</option></select></div></div>
<button class="btn btn-outline-primary">حفظ بيانات المستودع</button></form></article>
@empty<div class="card card-body text-center">لا توجد مستودعات بعد. أضف أول مستودع من الأعلى.</div>@endforelse
{{ $warehouses->links() }}
@endsection
