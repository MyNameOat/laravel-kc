<?php

namespace App\Http\Middleware;

use Closure;

class CheckHaveSessionWarehouse
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
        if (!$request->session()->has('warehouse'))
        {
            return redirect()->route('store-warehouse');
        }
        return $next($request);
    }
}
