<?php

namespace App\Http\Controllers;

use App\Models\Good;
use App\Models\GoodOption;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Exceptions;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

}
