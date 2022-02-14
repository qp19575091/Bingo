<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;


class RoomController extends Controller
{


    public function create(RoomRequest $request)
    {
        // session()->flush();
        // return;
        // return session()->get('room');
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


        return session()->get('room');
    }

    public function show(Request $request)
    {

        return session()->get("room.{$request->roomId}");
    }
}

// {
//     roomId: 123,
//     users: [
//         "a", 
//         "ㄖ"
//     ]
// }