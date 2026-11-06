@extends('admin.master')

@section('title', 'Orders')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ __('admin.orders') }}
    @if (!empty($counts['new']))
        <span class="badge badge-danger">{{ $counts['new'] }} {{ __('admin.status_new') }}</span>
    @endif
</h1>

@if (session()->has('msg'))
<div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
    {{ session('msg') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
@endif

{{-- Status filter + search --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="btn-group mb-3">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-{{ $status ? 'outline-dark' : 'dark' }}">
                {{ __('admin.all_orders') }} <span class="badge badge-light">{{ $counts['all'] }}</span>
            </a>
            @foreach (\App\Models\Order::STATUSES as $s)
                <a href="{{ route('admin.orders.index', ['status' => $s]) }}"
                   class="btn btn-sm btn-{{ $status === $s ? 'primary' : 'outline-primary' }}">
                    {{ __('admin.status_' . $s) }} <span class="badge badge-light">{{ $counts[$s] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('admin.orders.index') }}" method="GET" class="form-inline">
            @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <input type="text" name="q" value="{{ $q }}" class="form-control mr-2" style="min-width:280px"
                   placeholder="{{ __('admin.search_orders') }}">
            <button class="btn btn-primary mr-2"><i class="fas fa-search"></i> {{ __('admin.search') }}</button>
            @if ($q)
                <a href="{{ route('admin.orders.index', ['status' => $status]) }}" class="btn btn-secondary">{{ __('admin.reset') }}</a>
            @endif
        </form>
    </div>
</div>

<table class="table table-bordered table-hover bg-white">
    <tr class="bg-dark text-white">
        <th>{{ __('admin.order_number') }}</th>
        <th>الصيدلية</th>
        <th>{{ __('admin.customer') }}</th>
        <th>{{ __('admin.phone') }}</th>
        <th>{{ __('admin.items_count') }}</th>
        <th>{{ __('admin.total') }}</th>
        <th>{{ __('admin.status') }}</th>
        <th>{{ __('admin.date') }}</th>
        <th>{{ __('admin.actions') }}</th>
    </tr>
    @forelse ($orders as $order)
    <tr>
        <td><a href="{{ route('admin.orders.show', $order->id) }}"><span dir="ltr">{{ $order->order_number }}</span></a></td>
        <td>{{ $order->pharmacy->trans_name ?: '—' }}</td>
        <td>{{ $order->customer_name }}</td>
        <td><span dir="ltr">{{ $order->customer_phone }}</span></td>
        <td>{{ $order->order_details_count }}</td>
        <td>{{ number_format($order->total, 2) }}</td>
        <td><span class="badge badge-{{ $order->status_color }}">{{ __('admin.status_' . $order->status) }}</span></td>
        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <a class="btn btn-sm btn-info" href="{{ route('admin.orders.show', $order->id) }}" title="{{ __('admin.view') }}">
                <i class="fas fa-eye"></i>
            </a>
            <a class="btn btn-sm btn-success" target="_blank" href="https://wa.me/{{ $order->wa_phone }}" title="{{ __('admin.contact_customer') }}">
                <i class="fab fa-whatsapp"></i>
            </a>
            <form class="d-inline" action="{{ route('admin.orders.destroy', $order->id) }}" method="POST">
                @csrf
                @method('delete')
                <button onclick="return confirm('Are you sure?!')" class="btn btn-sm btn-danger" title="{{ __('admin.delete') }}">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @empty
    <tr><td colspan="9" class="text-center">{{ __('admin.no_data') }}</td></tr>
    @endforelse
</table>
{{ $orders->links() }}
@endsection
