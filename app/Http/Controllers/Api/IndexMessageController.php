<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class IndexMessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['profiles'])->get();
        //$messages = Message::all();
        //dd($messages);
        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
}
