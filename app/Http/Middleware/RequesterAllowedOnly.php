<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequesterAllowedOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(
            \App\Models\Request::findOrFail($request->segment(2))->user_id != auth()->user()->id
            && auth()->user()->hasRole('sales director')
        ){
            abort(404);
        }
        return $next($request);
    }
}
