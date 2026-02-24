<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupplierPricePublisher;

class PizzaOfferController extends Controller
{
    /**
     * @throws \Exception
     */
    public function show(Request $request, $id, SupplierPricePublisher $publisher)
    {
        $suppliers = ['CheesyLand', 'TomatoKing', 'SpiceFactory'];
        $publisher->sendPriceRequests($id, $suppliers);
        return response()->json(['status' => 'dispatched']);
    }
}
