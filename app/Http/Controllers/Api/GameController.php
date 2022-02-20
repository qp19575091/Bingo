<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
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
     * @bodyParam room_id string required Need room_id to show the room information. Example: "room1"
     * @bodyParam nickname string required The text of the nickname. Example: "Jacky"
     * @bodyParam number integer required The number of the bingo. Example: 3
     * 
     * @response 200 {
     *      "win_line": 1,
     *      "size": 3,
     *      "user_order": 0,
     *      "room_id": "user1",
     *      "user_number": 2,
     *      "users": {
     *          "user1": [
     *              [
     *                  1,
     *                  2,
     *                  3
     *              ]
     *          ],
     *          "user2": [
     *              [
     *                  4,
     *                  5,
     *                  6
     *              ]
     *          ]
     *      },
     *      "user_id": [
     *          "user1",
     *          "user2"
     *      ]
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *     "message": "This user is not in room."
     * }
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id not exits."
     * }
     * 
     * @response status=422 scenario="Unprocessable Content" {
     *      "message": "The given data was invalid.",
     *      "errors": {
     *          "number": [
     *              "The number must be an integer."
     *          ]
     *      }
     * }
     * 
     */
    public function store(GameRequest $request)
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
     * The selected number becomes zero.
     *
     * @bodyParam room_id string required Need room_id to show the room information. Example: "room1"
     * @bodyParam nickname string required The text of the nickname. Example: "Jacky"
     * @bodyParam number integer required The number of the bingo. Example: 3
     * 
     * @response 200 {
     *     "win_line": 1,
     *     "size": 3,
     *     "user_order": 1,
     *     "room_id": "user1",
     *     "user_number": 2,
     *     "users": {
     *         "user1": [
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
     *         "user2": [
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
     *         "user1",
     *         "user2"
     *     ]
     * }
     * @response 200 {
     *     "message" => "Game Over. User:a Is Win."
     * }
     * 
     * @response status=400 scenario="Bad Request" {
     *     "message": "This user is not in room."
     * }
     * @response status=400 scenario="Bad Request" {
     *     "message": "Please wait for other user."
     * }
     * @response status=404 scenario="Not Found" {
     *     "message": "This room_id not exits."
     * }
     * @response status=422 scenario="Unprocessable Content" {
     *      "message": "The given data was invalid.",
     *      "errors": {
     *          "number": [
     *              "The number must be an integer."
     *          ]
     *      }
     * }
     */
    public function chooseNumber(GameRequest $request, BingoService $bingo)
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
