@extends('admin.master')

@section('title', 'Order')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ __('admin.order_details') }}
    <small class="text-muted" dir="ltr">{{ $order->order_number }}</small>
    <span class="badge badge-{{ $order->status_color }}">{{ __('admin.status_' . $order->status) }}</span>
</h1>

@if (session()->has('msg'))
    <div class="alert alert-{{ session('type') }}">{{ session('msg') }}</div>
@endif

<div class="row">
    {{-- Ordered medicines --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><strong>{{ __('admin.items') }}</strong></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tr class="bg-light">
                        <th>#</th>
                        <th>{{ __('admin.medicine_name') }}</th>
                        <th>{{ __('admin.unit_price') }}</th>
                        <th>{{ __('admin.quantity') }}</th>
                        <th>{{ __('admin.total') }}</th>
                    </tr>
                    @foreach ($order->order_details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($detail->product_id && $detail->product->exists)
                                    <a href="{{ route('admin.products.edit', $detail->product_id) }}">{{ $detail->trans_product_name }}</a>
                                @else
                                    {{ $detail->trans_product_name }}
                                @endif
                            </td>
                            <td>{{ number_format($detail->price, 2) }}</td>
                            <td>{{ $detail->quantity }}</td>
                            <td>{{ number_format($detail->total, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-weight-bold bg-light">
                        <td colspan="4">{{ __('admin.total') }}</td>
                        <td>{{ number_format($order->total, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Status --}}
        <div class="card">
            <div class="card-header"><strong>{{ __('admin.update_status') }}</strong></div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('put')
                    <div class="form-group">
                        <label>{{ __('admin.order_status') }}</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            @foreach (\App\Models\Order::STATUSES as $s)
                                <option value="{{ $s }}" @selected(old('status', $order->status) === $s)>
                                    {{ __('admin.status_' . $s) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    </div>
                    <div class="form-group">
                        <label>{{ __('admin.order_note') }}</label>
                        <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note', $order->note) }}</textarea>
                        @error('note')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    </div>
                    <button class="btn btn-primary">{{ __('admin.save') }}</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">{{ __('admin.back') }}</a>
                </form>
            </div>
        </div>
    </div>

    {{-- Customer --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><strong>{{ __('admin.customer') }}</strong></div>
            <div class="card-body">
                <p><strong>{{ __('admin.name') }}:</strong> {{ $order->customer_name }}</p>
                <p><strong>{{ __('admin.phone') }}:</strong> <span dir="ltr">{{ $order->customer_phone }}</span></p>
                @if ($order->customer_address)
                    <p><strong>{{ __('admin.customer_address') }}:</strong> {{ $order->customer_address }}</p>
                @endif
                <p><strong>{{ __('admin.date') }}:</strong> {{ $order->created_at->format('Y-m-d H:i') }}</p>
                @if ($order->note)
                    <hr>
                    <p><strong>{{ __('admin.order_note') }}:</strong></p>
                    <p class="text-muted">{{ $order->note }}</p>
                @endif

                <hr>
                <a class="btn btn-success btn-block" target="_blank" href="{{ $order->whatsappLink($order->customer_phone) }}">
                    <i class="fab fa-whatsapp"></i> {{ __('admin.send_whatsapp') }}
                </a>
                <a class="btn btn-outline-success btn-block" target="_blank" href="https://wa.me/{{ $order->wa_phone }}">
                    <i class="fab fa-whatsapp"></i> {{ __('admin.contact_customer') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
