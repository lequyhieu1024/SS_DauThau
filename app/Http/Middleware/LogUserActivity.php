<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $request->method(),
                'details' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payload' => json_encode($request->all())
            ]);
        }

        return $response;
    }
}
