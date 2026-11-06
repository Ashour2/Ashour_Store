@extends('admin.master')

@section('title', 'لوحة التحكم')

@section('content')
@php
$cards = [
    ['label' => 'الأدوية', 'value' => $stats['medicines'], 'icon' => 'fa-capsules', 'tone' => 'blue', 'route' => route('admin.products.index'), 'can' => 'products.view'],
    ['label' => 'التصنيفات', 'value' => $stats['categories'], 'icon' => 'fa-tags', 'tone' => 'purple', 'route' => route('admin.categories.index'), 'can' => 'categories.view'],
    ['label' => 'الطلبات الجديدة', 'value' => $stats['orders_new'], 'icon' => 'fa-bag-shopping', 'tone' => 'orange', 'route' => route('admin.orders.index', ['status' => 'new']), 'can' => 'orders.view'],
    ['label' => 'إجمالي الطلبات', 'value' => $stats['orders'], 'icon' => 'fa-receipt', 'tone' => 'green', 'route' => route('admin.orders.index'), 'can' => 'orders.view'],
    ['label' => 'الاستشارات الجديدة', 'value' => $stats['consultations_new'], 'icon' => 'fa-comments', 'tone' => 'red', 'route' => route('admin.consultations.index'), 'can' => 'consultations.view'],
    ['label' => 'الأدوية غير المتوفرة', 'value' => $stats['out_of_stock'], 'icon' => 'fa-box-open', 'tone' => 'slate', 'route' => route('admin.products.index'), 'can' => 'products.view'],
];
@endphp

<div class="dashboard-pro">
    <section class="dashboard-hero">
        <div>
            <p class="dashboard-kicker">لوحة التحكم</p>
            <h1>أهلًا {{ auth()->user()->name }} 👋</h1>
            <p>هنا تجد ملخصًا سريعًا لأهم عمليات المنصة اليوم.</p>
        </div>
        <div class="dashboard-hero-actions">
            @can('products.create')<a href="{{ route('admin.products.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> إضافة دواء</a>@endcan
            @can('orders.view')<a href="{{ route('admin.orders.index', ['status' => 'new']) }}" class="btn btn-outline-secondary"><i class="fas fa-receipt"></i> مراجعة الطلبات</a>@endcan
        </div>
    </section>

    @if (auth()->user()->isPharmacist() && auth()->user()->pharmacy_id)
        <div class="dashboard-pharmacy-note"><i class="fas fa-clinic-medical"></i> أنت تعمل الآن ضمن <strong>{{ auth()->user()->pharmacy->trans_name }}</strong>، وتظهر لك بيانات صيدليتك فقط.</div>
    @endif

    <section class="stat-grid">
        @foreach ($cards as $card)
            @can($card['can'])
                <a href="{{ $card['route'] }}" class="stat-card stat-{{ $card['tone'] }}">
                    <span class="stat-icon"><i class="fas {{ $card['icon'] }}"></i></span>
                    <span><strong>{{ $card['value'] }}</strong><small>{{ $card['label'] }}</small></span>
                    <i class="fas fa-arrow-left stat-arrow"></i>
                </a>
            @endcan
        @endforeach
    </section>

    <section class="dashboard-columns">
        @can('orders.view')
        <article class="dashboard-panel dashboard-panel-wide">
            <header><div><h2>أحدث الطلبات</h2><p>آخر الطلبات المسجلة في المنصة</p></div><a href="{{ route('admin.orders.index') }}">عرض الكل <i class="fas fa-arrow-left"></i></a></header>
            <div class="dashboard-table-wrap"><table class="dashboard-table"><thead><tr><th>رقم الطلب</th><th>العميل</th><th>الصيدلية</th><th>المبلغ</th><th>الحالة</th></tr></thead><tbody>
                @forelse ($latest_orders as $order)
                    <tr onclick="window.location='{{ route('admin.orders.show', $order) }}'" style="cursor:pointer"><td dir="ltr">{{ $order->order_number }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->pharmacy->trans_name ?? '—' }}</td><td>{{ number_format($order->total, 2) }}</td><td><span class="status-pill status-{{ $order->status_color }}">{{ __('admin.status_' . $order->status) }}</span></td></tr>
                @empty <tr><td colspan="5" class="dashboard-empty">لا توجد طلبات حتى الآن</td></tr>@endforelse
            </tbody></table></div>
        </article>
        @endcan

        @can('products.view')
        <article class="dashboard-panel">
            <header><div><h2>أحدث الأدوية</h2><p>آخر ما أضيف إلى الكتالوج</p></div><a href="{{ route('admin.products.index') }}">عرض الكل</a></header>
            <div class="dashboard-list">
                @forelse ($latest_medicines as $medicine)
                    <a href="{{ route('admin.products.edit', $medicine) }}"><img src="{{ $medicine->img_path }}" alt=""><span><strong>{{ $medicine->trans_name }}</strong><small>{{ $medicine->category->trans_name }}</small></span><i class="fas fa-chevron-left"></i></a>
                @empty <p class="dashboard-empty">لا توجد أدوية حتى الآن</p>@endforelse
            </div>
        </article>
        @endcan

        @can('consultations.view')
        <article class="dashboard-panel">
            <header><div><h2>آخر الاستشارات</h2><p>الطلبات الواردة من المرضى</p></div><a href="{{ route('admin.consultations.index') }}">عرض الكل</a></header>
            <div class="dashboard-list">
                @forelse ($latest_consultations as $consultation)
                    <a href="{{ route('admin.consultations.show', $consultation) }}"><span class="list-avatar"><i class="fas fa-user"></i></span><span><strong>{{ $consultation->name }}</strong><small dir="ltr">{{ $consultation->phone }}</small></span><span class="status-pill status-{{ $consultation->status === 'new' ? 'warning' : 'success' }}">{{ $consultation->status === 'new' ? 'جديد' : 'تم الرد' }}</span></a>
                @empty <p class="dashboard-empty">لا توجد استشارات حتى الآن</p>@endforelse
            </div>
        </article>
        @endcan
    </section>
</div>
@endsection