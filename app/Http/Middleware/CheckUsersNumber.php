<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUsersNumber
{
    /**
     * The users number in room must less than 2 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (array_key_exists($request->nickname, session()->get("room.{$request->room_id}.users"))) {
            abort(response::HTTP_ACCEPTED, "Please Choose Another Nickname");
        }
        if (count(session()->get("room.{$request->room_id}.users")) >= 2) { 
            abort(response::HTTP_ACCEPTED, "The room is full. Please choose another room");
        }
        return $next($request);
    }
}
