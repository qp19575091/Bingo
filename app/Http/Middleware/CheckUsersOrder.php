<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsersOrder
{
    /**
     * Check the users order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $room = session()->get("room.{$request->room_id}");
        if ($room['user_order'] == array_search($request->nickname, $room['users'])) {
            return $next($request);
        }
        abort(Response::HTTP_FORBIDDEN, "Please wait for other.");
    }
}
