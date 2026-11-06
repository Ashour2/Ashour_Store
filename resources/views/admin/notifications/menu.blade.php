@php
    $adminNotifications = auth()->user()->adminNotifications()->latest()->take(7)->get();
    $unreadNotifications = auth()->user()->adminNotifications()->whereNull('read_at')->count();
@endphp
<div class="admin-tool-dropdown dropdown notification-menu">
    <button class="{{ ($headerNotification ?? false) ? 'header-notification-button' : 'admin-tool border-0 bg-transparent' }}" data-toggle="dropdown" aria-label="الإشعارات">
        <span class="notification-icon-wrap"><i class="fas fa-bell"></i>@if($unreadNotifications)<b>{{ $unreadNotifications > 9 ? '9+' : $unreadNotifications }}</b>@endif</span><span>الإشعارات</span>
    </button>
    <div class="dropdown-menu admin-dropdown-menu notification-dropdown">
        <div class="notification-dropdown-head"><strong>الإشعارات</strong>@if($unreadNotifications)<span>{{ $unreadNotifications }} جديد</span>@endif</div>
        @forelse($adminNotifications as $notification)
            <a class="dropdown-item notification-item {{ $notification->is_unread ? 'unread' : '' }}" href="{{ route('admin.notifications.open', $notification) }}">
                <i class="fas fa-{{ $notification->type === 'order' ? 'receipt' : ($notification->type === 'consultation' ? 'comment-medical' : 'credit-card') }}"></i><span><b>{{ $notification->title }}</b><small>{{ $notification->body }}</small></span>
            </a>
        @empty
            <div class="notification-empty"><i class="far fa-bell"></i> لا توجد إشعارات جديدة</div>
        @endforelse
        <div class="dropdown-divider"></div>
        <div class="notification-dropdown-actions"><a href="{{ route('admin.notifications.index') }}">عرض كل الإشعارات</a>@if($unreadNotifications)<form method="POST" action="{{ route('admin.notifications.read-all') }}">@csrf<button type="submit">تعليم الكل كمقروء</button></form>@endif</div>
    </div>
</div>