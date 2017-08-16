<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use Illuminate\Http\Request;

class BroadcastQueryController extends Controller
{
    /**
     * Gets a list of all online broadcasts.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $broadcasts = Broadcast::online();

        return response()->json($broadcasts);
    }
}
