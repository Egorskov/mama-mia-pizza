<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;

class PizzaSseController extends Controller
{
    public function stream()
    {
        return response()->stream(function () {
            Redis::subscribe(['pizza.prices'], function ($message) {
                echo "data: {$message}\n\n";
                ob_flush();
                flush();
            });
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }
}
