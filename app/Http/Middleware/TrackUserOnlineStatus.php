<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserOnlineStatus
{
    /**
     * Handle an incoming request.
     * Updates last_activity_at and is_online for authenticated users.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            // Throttle updates to once per minute to avoid excessive DB writes
            $lastActivity = $user->last_activity_at;
            if (!$lastActivity || $lastActivity->diffInSeconds(now()) > 60) {
                $user->update([
                    'last_activity_at' => now(),
                    'is_online' => true,
                ]);
            }
        }

        return $next($request);
    }
}
