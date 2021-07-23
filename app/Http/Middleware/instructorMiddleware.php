<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class instructorMiddleware
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

        $instructor = in_array("instructor", $roleArr);

        if (!$instructor) {
            return response()->json(["message" => "You are not an instructor"], 403);
        }
        return $next($request);
    }
}
