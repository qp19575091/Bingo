<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Service\BingoService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function store(Request $request, BingoService $bingo)
    {
        $room = session()->get("room.{$request->room_id}");

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
    public function chooseNumber(Request $request, BingoService $bingo)
    {
        $room = $bingo->getRoom();
        if (($room['user_order'] != array_search($request->nickname, $room['user_id']))) {
            abort(Response::HTTP_FORBIDDEN, "Please wait for other.");
        }

        $bingoArrs = $bingo->getBingoArr();

        foreach ($bingoArrs as $k => $v) {
            if (in_array($request->number, $v)) {
                $key = array_search($request->number, $v);
                $bingoArrs[$k][$key] = 0;
                $bingo->next();
                break;
            }
        }

        session()->put("room.{$request->room_id}.users.{$request->nickname}", $bingoArrs);

        if ($bingo->isBingo()) {
            return "Game Over. User:" . $request->nickname . " Win.";
        }

        return session()->get("room")[$request->room_id];
    }
}
