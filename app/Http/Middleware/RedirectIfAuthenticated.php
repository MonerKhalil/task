<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $is_api_request = in_array('api',$request->route()->getAction('middleware'));
                if ($is_api_request){
                    return $this->responseError("Request Only Guest.");
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
