<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(RoomRequest $request)
    {
        return $request->all();
    }
}
