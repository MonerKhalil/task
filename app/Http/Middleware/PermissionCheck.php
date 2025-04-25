<?php

namespace App\Http\Middleware;

use App\Exceptions\UnAuthorizedException;
use App\Helpers\MyApp;
use Closure;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permissions
     * @return mixed
     * @throws UnAuthorizedException
     */
    public function handle($request, Closure $next,string $permissions){
        $permissionsOr = explode("|",$permissions);
        if (sizeof($permissionsOr) > 0){
            if (MyApp::main()->permissionsProcess->checkPermissionExists($permissionsOr)){
                return $next($request);
            }
        }
        $permissionsAnd = explode("&",$permissions);
        if (sizeof($permissionsAnd) > 0){
            if (MyApp::main()->permissionsProcess->checkPermissionExists($permissionsAnd,false)){
                return $next($request);
            }
        }
        throw new UnAuthorizedException("User does not have any of the necessary access rights.");
    }
}
