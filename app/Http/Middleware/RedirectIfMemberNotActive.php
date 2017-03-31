<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfMemberNotActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $member = $request->user()->members()->orderBy('logined_at', 'desc')->first();

        if (!$member || !$member->actived) {
            return redirect('/profile' . (! $request->has('project_id') ?: '?project_id=' . $request->get('project_id')));
        }

        return $next($request);
    }
}
