<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Room
 */
class RoomController extends Controller
{
    /**
     * create a new room.
     
     * @bodyParam room_id string required The text of the room_id. Example: "room1"
     * @bodyParam nickname string required The text of the nickname. Example: "Jacky"
     * @bodyParam size integer required The bingo size. Example: 3
     * @bodyParam win_line integer required Game over when the number of connections is equal to win_line. Example: 2
     * @bodyParam user_number integer required The number of users in a room. Example: 3
     * 
     * @response 201 {
     *     "win_line": 1,
     *     "size": 3,
     *     "user_order": 0,
     *     "room_id": "room1",
     *     "user_number": 2,
     *     "users": {
     *         "user1": []
     *     },
     *     "user_id": [
     *         "user1"
     *     ]
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *      "message": "The room_id has exists."
     * }
     * @response status=422 scenario="Unprocessable Content" {
     *      "message": "The given data was invalid.",
     *      "errors": {
     *          "size": [
     *              "The size must not be greater than 10."
     *          ]
     *      }
     * }
     */
    public function store(RoomRequest $request)
    {
        $room = [
            "win_line" => $request->win_line,
            "size" => $request->size,
            "user_order" => 0,
            "room_id" => $request->room_id,
            "user_number" => $request->user_number,
            "users" => [
                $request->nickname => []
            ]
        ];

        if (session()->has("room.{$request->room_id}")) {
            return response()->json(['message' => "The room_id has exists"], Response::HTTP_BAD_REQUEST);
        }

        session()->put("room.{$request->room_id}", $room);
        session()->push("room.{$request->room_id}.user_id", $request->nickname);


        return response()->json([], Response::HTTP_CREATED);
    }

    /**
     * User can join a room
     *
     * @bodyParam room_id string required The text of the room_id. Example: "room1"
     * @bodyParam nickname string required The text of the nickname. Example: "Jacky"
     * 
     * @response 200 {
     *     
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *      "message": "Please Choose Another Nickname."
     * }
     * @response status=400 scenario="Bad Request" {
     *      "message": "The room is full. Please choose another room."
     * }
     * @response status=404 scenario="Not Found" {
     *      "message": "This room_id not exits."
     * }
     * 
     */
    public function join(Request $request)
    {
        session()->push("room.{$request->room_id}.user_id", $request->nickname);
        $users = session()->get("room.{$request->room_id}.users");
        $users +=  array($request->nickname => []);
        session()->put("room.{$request->room_id}.users", $users);

        return response()->json(session()->get("room.{$request->room_id}.users"), response::HTTP_OK);
    }
    /**
     * Show a room.
     *
     * @bodyParam room_id string required Need room_id to show the room information. Example: "room1"
     * 
     * @response 200 {
     *     "win_line": 1,
     *     "size": 3,
     *     "user_order": 0,
     *     "room_id": "room",
     *     "user_number": 2,
     *     "users": {
     *         "user1": []
     *     },
     *     "user_id": [
     *         "user1"
     *     ]
     * }
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id not exits."
     * }
     */
    public function show(Request $request)
    {
        $room = session()->get("room.{$request->room_id}");

        if (is_null($room)) {
            abort(Response::HTTP_NOT_FOUND, "This room_id is not fount.");
        }

        return response()->json($room, Response::HTTP_OK);
    }

    /**
     * Delete a room.
     *
     * @bodyParam room_id string required Need room_id to delete the room information. Example: "room1"
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
