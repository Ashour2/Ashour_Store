<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // المشكلة 1: التحقق من تسجيل الدخول أولاً
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يرجى تسجيل الدخول أولاً');
        }

        // الوصول للوحة التحكم صار مبنيًا على الدور: أي مستخدم له دور يُعتبر من فريق العمل،
        // والصلاحيات التفصيلية تتحقق على كل مسار عبر can:
        if (! Auth::user()->isStaff()) {
            return redirect('/')->with('error', 'ليس لديك صلاحية للدخول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
