<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Service\BingoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Game
 */
class GameController extends Controller
{
    /**
     * Store numeric values to fill bingoArr
     *
     * @bodyParam room_id string required Need room_id to show the room information. emxample: "room1"
     * @bodyParam nickname string required the text of the nickname. emxample: "Jacky"
     * @bodyParam number integer required the number of the bingo. emxample: 3
     * 
     * @response 200 {
     *      "win_line": 1,
     *      "size": 3,
     *      "user_order": 0,
     *      "room_id": "d",
     *      "user_number": 2,
     *      "users": {
     *          "a": [
     *              [
     *                  1,
     *                  2,
     *                  3
     *              ]
     *          ],
     *          "b": [
     *              [
     *                  4,
     *                  5,
     *                  6
     *              ]
     *          ]
     *      },
     *      "user_id": [
     *          "a",
     *          "b"
     *      ]
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *     "message": "message": "This user is not in room."
     * }
     * @response status=400 scenario="Bad Request" {
     *     "message": "This room_id is not fount."
     * }
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id not exits."
     * }
     */
    public function store(Request $request, BingoService $bingo)
    {
        $room = session()->get("room.{$request->room_id}");
        $bingoArrs = session()->get("room.{$request->room_id}.users.{$request->nickname}", []);

        foreach ($bingoArrs as $bingoArr) {
            if (in_array($request->number, $bingoArr)) {
                return response()->json(['message' => "The number in bingo must be unique."], Response::HTTP_BAD_REQUEST);
            }
        }

        for ($i = 0; $i < $room['size']; $i++) {
            if (count(session()->get("room.{$request->room_id}.users.{$request->nickname}.{$i}", [])) < $room['size']) {
                session()->push("room.{$request->room_id}.users.{$request->nickname}.{$i}", $request->number);
                break;
            }
        }

        return session()->get("room.{$request->room_id}");
    }

    /**
     * Choose a numeric to zero.
     *
     * @bodyParam room_id string required Need room_id to show the room information. emxample: "room1"
     * @bodyParam nickname string required the text of the nickname. emxample: "Jacky"
     * @bodyParam number integer required the number of the bingo. emxample: 3
     * 
     * @response 200 {
     *     "win_line": 1,
     *     "size": 3,
     *     "user_order": 1,
     *     "room_id": "d",
     *     "user_number": 2,
     *     "users": {
     *         "a": [
     *             [
     *                 0,
     *                 0,
     *                 3
     *             ],
     *             [
     *                 6,
     *                 7,
     *                 8
     *             ],
     *             [
     *                 1,
     *                 12,
     *                 13
     *             ]
     *         ],
     *         "b": [
     *             [
     *                 4,
     *                 5,
     *                 6
     *             ],
     *             [
     *                 13,
     *                 1,
     *                 0
     *             ],
     *             [
     *                 3,
     *                 7,
     *                 8
     *             ]
     *         ]
     *     },
     *     "user_id": [
     *         "a",
     *         "b"
     *     ]
     * }
     * @response 200 {
     *     "message" => "Game Over. User:a Is Win."
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *     "message": "message": "This user is not in room."
     * }
     * @response status=400 scenario="Bad Request" {
     *     "message": "message": "Please wait for other user."
     * }
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id not exits."
     * }
     */
    public function chooseNumber(Request $request, BingoService $bingo)
    {
        $room = $bingo->getRoom();
        if (($room['user_order'] != array_search($request->nickname, $room['user_id']))) {
            abort(Response::HTTP_BAD_REQUEST, "Please wait for other user.");
        }

        $bingoArrs = $bingo->getBingoArr();
        $user_id = $room['user_id'];

        foreach ($user_id as $user) {
            foreach ($bingoArrs[$user] as $k => $v) {
                if (in_array($request->number, $v)) {
                    $key = array_search($request->number, $v);
                    $bingoArrs[$user][$k][$key] = 0;
                    $bingo->next();
                    break;
                }
            }
            session()->put("room.{$request->room_id}.users", $bingoArrs);
            if ($bingo->isBingo($user)) {
                return response()->json(['message' => "Game Over. User:" . $request->nickname . "Is Win."]);
            }
        }

        return session()->get("room")[$request->room_id];
    }
}
