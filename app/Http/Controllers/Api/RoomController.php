<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;


class RoomController extends Controller
{
    public function create(RoomRequest $request)
    {
        $room = [
            "roomId" => $request->roomId,
            "users" => [
                $request->nickname
            ]
        ];

        if (session()->has("room.{$request->roomId}")) {
            session()->push("room.{$request->roomId}.users", $request->nickname);
        } else {
            session()->put("room.{$request->roomId}", $room);
        }
    }

    public function show(Request $request)
    {
        return session()->get("room.{$request->roomId}");
    }

    public function delete(Request $request)
    {
        session()->pull("room.{$request->roomId}");
        return session()->get("room");
    }
}
