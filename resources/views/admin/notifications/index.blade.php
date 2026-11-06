@extends('admin.master')
@section('title', 'الإشعارات')
@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between"><div><h3 class="card-title font-weight-bold">كل الإشعارات</h3><small class="text-muted d-block mt-1">طلباتك واستشاراتك وتحديثات الاشتراك.</small></div><form method="POST" action="{{ route('admin.notifications.read-all') }}">@csrf<button class="btn btn-outline-primary btn-sm"><i class="fas fa-check-double"></i> تعليم الكل كمقروء</button></form></div>
    <div class="list-group list-group-flush">
        @forelse($notifications as $notification)
            <a class="list-group-item list-group-item-action {{ $notification->is_unread ? 'notification-page-unread' : '' }}" href="{{ route('admin.notifications.open', $notification) }}"><div class="d-flex justify-content-between align-items-start"><div><strong>{{ $notification->title }}</strong><p class="mb-0 text-muted mt-1">{{ $notification->body }}</p></div><small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small></div></a>
        @empty
            <div class="p-5 text-center text-muted">لا توجد إشعارات حالياً.</div>
        @endforelse
    </div>
    <div class="card-footer">{{ $notifications->links() }}</div>
</div>
@endsection