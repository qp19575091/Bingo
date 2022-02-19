<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class RoomController extends Controller
{
    /**
     * create a new room.
     
     * @bodyParam room_id string required the text of the room_id. emxample: "room1"
     * @bodyParam nickname string required the text of the nickname. emxample: "Jacky"
     * 
     * @response 201 {
     *     
     * }
     * 
     * @response status=202 scenario="Accepted" {
     *     "message": "The room is full. Please choose another room"
     * }
     */
    public function store(RoomRequest $request)
    {
        $room = [
            "win_line" => $request->win_line,
            "size" => $request->size,
            "user_order" => 0,
            "room_id" => $request->room_id,

            "users" => [
                $request->nickname => []
            ]
        ];

        if (session()->has("room.{$request->room_id}")) {
            return "The room_id has exists";
        }
        session()->put("room.{$request->room_id}", $room);
        session()->push("room.{$request->room_id}.user_id", $request->nickname);


        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * User can join a room
     *
     * @bodyParam room_id string required the text of the room_id. emxample: "room1"
     * @bodyParam nickname string required the text of the nickname. emxample: "Jacky"
     * 
     * @response 201 {
     *     
     * }
     * 
     * @response status=202 scenario="Accepted" {
     *     "message": "The room is full. Please choose another room"
     * }
     */
    public function join(Request $request)
    {
        session()->push("room.{$request->room_id}.user_id", $request->nickname);
        $users = session()->get("room.{$request->room_id}.users");
        $users +=  array($request->nickname => []);
        session()->put("room.{$request->room_id}.users", $users);


        return session()->get("room.{$request->room_id}.users");
    }
    /**
     * show a room.
     *
     * @bodyParam room_id string required Need room_id to show the room information. emxample: "room1"
     * 
     * @response 200 {
     *     "user_order": 0,
     *     "room_id": 2,
     *     "users": [
     *         "Jacky",
     *         "Ben"
     *     ]
     * }
     * 
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id is not fount."
     * }
     */
    public function show(Request $request)
    {
        // return session()->put("room.{$request->room_id}.user_order", 0);
        $room = session()->get("room.{$request->room_id}");

        // return $room['users']['a'];
        // return array_keys($room['users'],"a");

        if (is_null($room)) {
            abort(Response::HTTP_NOT_FOUND, "This room_id is not fount.");
        }

        return response()->json($room, Response::HTTP_OK);
    }

    /**
     * Delete a room.
     *
     * @bodyParam room_id string required Need room_id to delete the room information. emxample: "room1"
     * 
     * @response 204 {
     *    
     * }
     * 
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id is not fount."
     * }
     */
    public function delete(Request $request)
    {
        session()->pull("room.{$request->room_id}");
        return response()->noContent();
    }
}
