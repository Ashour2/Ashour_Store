@extends('site.layout')

@section('title', __('site.order_placed'))

@section('content')
@php $pharmacyWa = $order->pharmacy->wa_phone; @endphp
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="bg-white border rounded-4 p-4 p-md-5 text-center">
                <div class="cart-empty-icon mb-3" style="background:#dcfce7;color:#16a34a">
                    <i class="fas fa-circle-check"></i>
                </div>
                <h3 class="fw-bold">{{ __('site.order_placed') }}</h3>
                <p class="text-muted mb-3">تم إرسال طلبك إلى {{ $order->pharmacy->trans_name ?: 'الصيدلية المختارة' }}. ستتواصل معك الصيدلية لتأكيده.</p>

                <div class="d-inline-block bg-ph-soft text-ph fw-bold rounded-pill px-4 py-2 mb-4">
                    {{ __('site.order_number') }}: <span dir="ltr">{{ $order->order_number }}</span>
                </div>

                <table class="table table-sm text-start">
                    <tbody>
                        @foreach ($order->order_details as $detail)
                            <tr>
                                <td>{{ $detail->trans_product_name }}</td>
                                <td class="text-muted" style="width:70px">× {{ $detail->quantity }}</td>
                                <td class="text-end fw-semibold" style="width:100px">{{ number_format($detail->total, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="fw-bold">
                            <td colspan="2">{{ __('site.total') }}</td>
                            <td class="text-end text-ph">{{ number_format($order->total, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
                    @if ($pharmacyWa)
                        <a href="{{ $order->pharmacyWhatsappLink() }}" target="_blank" id="waOrder"
                           class="btn btn-success btn-lg rounded-pill px-4">
                            <i class="fab fa-whatsapp"></i> إرسال الطلب إلى {{ $order->pharmacy->trans_name }}
                        </a>
                    @endif
                    <a href="{{ route('medicines.index') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                        {{ __('site.continue_shopping') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
