<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // 로그인 안 되어 있으면 로그인 페이지로
            return redirect()->route('login');
        }

        if (!Auth::user()->is_admin) {
            // JSON 요청이면 403, 일반 요청이면 alert + 이전 페이지
            if ($request->expectsJson()) {
                return response()->json(['message' => '관리자가 아닙니다.'], 403);
            }

            return redirect()->back()->with('admin_alert', '관리자가 아닙니다.');
        }

        return $next($request);
    }
}
