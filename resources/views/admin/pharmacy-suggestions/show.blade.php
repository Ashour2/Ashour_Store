@extends('admin.master')
@section('title', 'معاينة اقتراح الصيدلية')
@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div><h3 class="font-weight-bold mb-1"><i class="fas fa-lightbulb text-warning"></i> معاينة الاقتراح</h3><span class="text-muted">تواصل وارد من صيدلية ضمن المنصة.</span></div>
    <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-right"></i> العودة للإشعارات</a>
</div>

<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <span class="badge badge-warning mb-3">اقتراح جديد</span>
                <h4 class="font-weight-bold mb-3">{{ $suggestion->title }}</h4>
                <div class="p-3 rounded" style="background:#f7f9fc;white-space:pre-line;line-height:2">{{ $suggestion->message }}</div>
                <div class="text-muted mt-3"><i class="far fa-clock"></i> أرسل {{ $suggestion->created_at->translatedFormat('d F Y - h:i A') }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm border-0 mb-3"><div class="card-body">
            <h6 class="font-weight-bold border-bottom pb-2">بيانات المرسل</h6>
            <p class="mb-2"><i class="fas fa-clinic-medical text-primary"></i> <strong>الصيدلية:</strong> {{ $suggestion->pharmacy?->trans_name ?? '—' }}</p>
            <p class="mb-2"><i class="fas fa-user text-primary"></i> <strong>الصيدلي:</strong> {{ $suggestion->user?->name ?? '—' }}</p>
            <p class="mb-0"><i class="fas fa-phone text-primary"></i> <strong>الجوال:</strong> {{ $suggestion->pharmacy?->phone ?? '—' }}</p>
        </div></div>
        <div class="card shadow-sm border-0"><div class="card-body">
            <h6 class="font-weight-bold">حالة الاقتراح</h6>
            <form method="POST" action="{{ route('admin.pharmacy-suggestions.status', $suggestion) }}">@csrf @method('PUT')
                <select name="status" class="form-control mb-2">
                    <option value="new" @selected($suggestion->status === 'new')>جديد</option>
                    <option value="reviewing" @selected($suggestion->status === 'reviewing')>قيد المراجعة</option>
                    <option value="completed" @selected($suggestion->status === 'completed')>تم التنفيذ</option>
                    <option value="declined" @selected($suggestion->status === 'declined')>غير مناسب حالياً</option>
                </select>
                <button class="btn btn-primary btn-block"><i class="fas fa-save"></i> حفظ الحالة</button>
            </form>
        </div></div>
    </div>
</div>
@endsection
