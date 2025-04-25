<?php

namespace App\Http\Middleware;

use App\Helpers\MyApp;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards){
        $this->authenticate($request, $guards);
        #set all permissions...
        MyApp::main()->permissionsProcess->setPermissionsUserAuth();
        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        $is_api_request = in_array('api',$request->route()->getAction('middleware'));
        return $request->expectsJson() | $is_api_request ? null : route('login');
    }
}
