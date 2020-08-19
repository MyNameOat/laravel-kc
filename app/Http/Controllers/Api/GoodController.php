<?php

namespace App\Http\Controllers\Api;

use App\Good;
use App\GoodView;
use App\Http\Requests\AddGoodRequest;
use App\Http\Requests\SubGoodRequest;
use App\Http\Requests\TradeGoodRequest;
use App\OrderAddGood;
use App\OrderSubGood;
use App\OrderSubGoodDetail;
use App\User;
use App\Warehouse;
use App\WarehouseGood;
use App\WarehouseGoodBalance;
use App\WarehouseGoodView;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodController extends Controller
{
    

//    public function tradeGood(TradeGoodRequest $request)
//    {
//        $order_sub_good_id = $this->subGood($request->sub_good_array);
//
//        array_push($$request->add_good_array['order_sub_good_id'], $order_sub_good_id);
//
//        $this->addGood($request->add_good_array);
//    }
}
