<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        $user = Auth::guard('sanctum')->user();
		$hasAccess = false;
		$rolesArray = explode("|", $roles);
		foreach ($rolesArray as $role){
			if($user->getAccessLevel() >= User::getRoleAccessLevel($role)){
				$hasAccess = true;
				break;
			}
		}
		if( Auth::guard('sanctum')->guest() || !$hasAccess ) {
			return response([
				'status_code' => 403,
				'message' => 'Nemate pristup.',
			], 403);
		}
        
        return $next($request);
    }
}
