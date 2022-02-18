<?php

namespace App\Service;

use Illuminate\Http\Request;

class BingoService
{
    public $room_id;
    public $nickname;

    public function __construct(Request $request)
    {
        $this->room_id = $request->room_id;
        $this->nickname = $request->nickname;
    }

    public function getBingoArr()
    {
        return session()->get("room")[$this->room_id]['users'][$this->nickname];
    }

    public function getUserNumber()
    {
        return count(session()->get("room")[$this->room_id]['users']);
    }

    public function getCurrentOrder()
    {
        return session()->get("room")[$this->room_id]['user_order'];
    }

    public function getRoom()
    {
        return session()->get("room.{$this->room_id}");
    }

    public function isBingo()
    {
        $line = $this->getLine();
        $room = $this->getRoom();

        if ($line === $room['win_line']) {
            return true;
        }
        return false;
    }

    public function next()
    {
        $room_number = $this->getUserNumber();
        $current_order = $this->getCurrentOrder();
        if ($room_number - 1 <= $current_order) {
            session()->put("room.{$this->room_id}.user_order", 0);
        } else {
            session()->increment("room.{$this->room_id}.user_order");
        }
    }

    public function getLine()
    {
        $bingoArrs = $this->getBingoArr($this->room_id, $this->nickname);

        $count = 0;
        $line = 0;
        $length = count($bingoArrs);

        //The horizontal number is zero can be a line
        foreach ($bingoArrs as $bingoArr) {
            foreach ($bingoArr as $k => $v) {
                if ($bingoArr[$k] == 0) {
                    $count++;
                } else {
                    $count = 0;
                    break;
                };
            }
            if ($length == $count) {
                $line++;
                $count = 0;
            }
        }

        //The vertical number is zero can be a line
        foreach ($bingoArrs as $key => $value) {
            foreach ($value as $k => $v) {
                if ($bingoArrs[$k][$key] == 0) {
                    $count++;
                } else {
                    $count = 0;
                    break;
                };
            }
            if ($length == $count) {
                $line++;
                $count = 0;
            }
        }

        //The incline number is zero can be a line
        foreach ($bingoArrs as $k => $v) {
            if ($bingoArrs[$k][$k] == 0) {
                $count++;
            } else {
                $count = 0;
                break;
            }
        }
        if ($length == $count) {
            $line++;
            $count = 0;
        }

        $row = $length - 1;
        for ($i = 0; $i < $length; $i++) {
            if ($bingoArrs[$row][$i] == 0) {
                $count++;
            } else {
                $count = 0;
                break;
            }
            $row--;
        }
        if ($length == $count) {
            $line++;
            $count = 0;
        }

        return $line;
    }
}
