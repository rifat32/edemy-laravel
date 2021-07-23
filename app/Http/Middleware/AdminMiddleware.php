<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
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
        $role = $request->user()->role;
        $roleArr = explode(" ", $role);

        $adminCheck = in_array("admin", $roleArr);
        if (!$adminCheck) {
            return response()->json(["message" => "You are not an admin"], 403);
        }
        return $next($request);
    }
}
