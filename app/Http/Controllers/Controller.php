<?php

namespace App\Http\Controllers;

use App\Good;
use App\GoodView;
use App\OrderAddGood;
use App\OrderAddGoodDetail;
use App\OrderSubGood;
use App\OrderSubGoodDetail;
use App\Pr;
use App\Requisition;
use App\User;
use App\Warehouse;
use App\WarehouseGood;
use App\WarehouseGoodBalance;
use App\WarehouseGoodView;
use App\WithdrawRedLabel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCurrentCode($warehouse, $code)
    {
        $now_at = Carbon::now();

        $month = $now_at->month;

        if (strlen($month) == 1) {
            $month = '0' . $month;
        }

        $year = substr($now_at->year + 543, -2);

        $search_code = $warehouse->code . $code . $year . $month;

        if ($code == 'PR') {
            $lastest_code = Pr::withTrashed()->where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();
        } elseif ($code == 'WR') {
            $lastest_code = WithdrawRedLabel::where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();
        } elseif ($code == 'RQ') {
            $lastest_code = Requisition::where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();
        }

        if ($lastest_code == null) {
            $current_code = $search_code . '-001';
            return $current_code;
        }

        $code = $lastest_code->code;

        $num = (integer) substr($code, -3);
        $code = $num + 1;
        $count = 3 - strlen($code);

        for ($i = 0; $i < $count; $i++) {
            $code = '0' . $code;
        }

        $current_code = $search_code . '-' . $code;

        return $current_code;
    }

    public function subGood($warehouse, $date, $user, $key_name, $key_id, $good_array, $coil_code_array, $amount_array)
    {
        $orderSubGood = new OrderSubGood;
        $orderSubGood->warehouse_id = $warehouse->id;
        $orderSubGood->date = $date;
        $orderSubGood->user_id = $user->id;
        $orderSubGood->key_name = $key_name;
        $orderSubGood->key_id = $key_id;
        $orderSubGood->save();

        $allCost = 0;
        foreach ($good_array as $key => $value) {
            $good = Good::find($value);
            $goodView = GoodView::where('warehouse_id', $warehouse->id)->where('good_id', $value)
                ->where('coil_code', $coil_code_array[$key])->first();

            if ($coil_code_array[$key] != null) {
                $name = $coil_code_array[$key].' '.$good->name;
            } else {
                $name = $good->code.' '.$good->name;
            }

            if ($goodView->balance_amount == null || $goodView->balance_amount < $amount_array[$key]) {
                return response()->json([
                    'status' => false,
                    'message' => 'จำนวนสินค้า '.$name.' ไม่เพียงพอ กรุณาตรวจสอบ',
                ], 200);
            }

            $orderSubGoodDetail = new OrderSubGoodDetail;
            $orderSubGoodDetail->order_sub_good_id = $orderSubGood->id;
            $orderSubGoodDetail->good_id = $good->id;
            $orderSubGoodDetail->coil_code = $coil_code_array[$key];
            $orderSubGoodDetail->amount = $amount_array[$key];
            $orderSubGoodDetail->save();

            $warehouseGoodViews = WarehouseGoodView::with('warehouseGood')->where('warehouse_id', $warehouse->id)
                ->where('good_id', $good->id)->where('coil_code', $coil_code_array[$key])
                ->where('balance_amount', '>', 0)->get();

            $amount = bcmul(-1, $amount_array[$key]);
            $cost = 0;
            foreach ($warehouseGoodViews as $warehouseGoodView) {
                if ($amount < 0) {
                    if ($warehouseGoodView->balance_amount > $amount) {
                        $warehouseGoodBalance = new WarehouseGoodBalance;
                        $warehouseGoodBalance->warehouse_good_id = $warehouseGoodView->warehouse_good_id;
                        $warehouseGoodBalance->amount = $amount;
                        $warehouseGoodBalance->ratio_cost = $warehouseGoodView->warehouseGood->ratio_cost;
                        $warehouseGoodBalance->cost = bcmul($amount, $warehouseGoodView->warehouseGood->ratio_cost, 2);
                        $warehouseGoodBalance->order_sub_good_detail_id = $orderSubGoodDetail->id;
                        $warehouseGoodBalance->save();
                        $amount = 0;
                        $cost = bcadd($cost, $warehouseGoodBalance->cost, 2);
                    } else {
                        $warehouseGoodBalance = new WarehouseGoodBalance;
                        $warehouseGoodBalance->warehouse_good_id = $warehouseGoodView->warehouse_good_id;
                        $warehouseGoodBalance->amount = bcmul(-1, $warehouseGoodView->balance_amount, 2);
                        $warehouseGoodBalance->ratio_cost = bcdiv($warehouseGoodView->balance_cost, $warehouseGoodView->balance_amount, 2);
                        $warehouseGoodBalance->cost = bcmul(-1, $warehouseGoodView->balance_cost);
                        $warehouseGoodBalance->order_sub_good_detail_id = $orderSubGoodDetail->id;
                        $warehouseGoodBalance->save();
                        $amount = bcsub($amount, $warehouseGoodBalance->amount, 2);
                        $cost = bcadd($cost, $warehouseGoodBalance->cost, 2);
                    }
                } else {
                    break;
                }
            }

            if ($amount != 0) {
                return [
                    'status' => false,
                    'message' => 'สินค้า '. $name .' ที่จะเบิกมีจำนวนไม่เพียงพอ หรือมีเหตุขัดข้อง กรุณาตรวจสอบ',
                ];
            }

            $orderSubGoodDetail->cost = $cost;
            $orderSubGoodDetail->save();

            $allCost = bcadd($allCost, $orderSubGoodDetail->cost, 2);
        }
        $orderSubGood->cost = $allCost;
        $orderSubGood->save();

        return [
            'status' => true,
            'message' => 'success',
            'order_sub_good_id' => $orderSubGood->id
        ];
    }

    public function addGood($warehouse, $date, $user, $key_name, $key_id, $status_cost, $order_sub_good_id_array, $good_array, $coil_code_array, $amount_array, $cost_array)
    {
        if ($status_cost == "Manual") {
            foreach ($good_array as $key => $value) {
                $orderAddGood = new OrderAddGood;
                $orderAddGood->warehouse_id = $warehouse->id;
                $orderAddGood->date = $date;
                $orderAddGood->user_id = $user->id;
                $orderAddGood->key_name = $key_name;
                $orderAddGood->key_id = $key_id;
                $orderAddGood->status_cost = $status_cost;
                $orderAddGood->cost = array_sum($cost_array);
                $orderAddGood->save();

                $good = Good::find($value);

                $orderAddGoodDetail = new OrderAddGoodDetail;
                $orderAddGoodDetail->order_add_good_id = $orderAddGood->id;
                $orderAddGoodDetail->good_id = $good->id;
                $orderAddGoodDetail->coil_code = $coil_code_array[$key];
                $orderAddGoodDetail->amount = $amount_array[$key];
                $orderAddGoodDetail->cost = $cost_array[$key];
                $orderAddGoodDetail->save();

                $warehouseGood = new WarehouseGood;
                $warehouseGood->warehouse_id = $warehouse->id;
                $warehouseGood->good_id = $good->id;
                $warehouseGood->amount = $amount_array[$key];
                $warehouseGood->coil_code = $coil_code_array[$key];
                $warehouseGood->ratio_cost = bcdiv($cost_array[$key], $cost_array[$key], 2);
                $warehouseGood->order_add_good_id = $orderAddGood->id;
                $warehouseGood->save();

                $warehouseGoodBalance = new WarehouseGoodBalance;
                $warehouseGoodBalance->warehouse_good_id = $warehouseGood->id;
                $warehouseGoodBalance->amount = $amount_array[$key];
                $warehouseGoodBalance->ratio_cost = bcdiv($cost_array[$key], $cost_array[$key], 2);
                $warehouseGoodBalance->cost = $cost_array[$key];
                $warehouseGoodBalance->save();
            }
        } else {
            $orderSubGoods = OrderSubGood::whereIn('id', $order_sub_good_id_array)->get();
            $all_amount = array_sum($amount_array);
            $all_cost = bcmul(-1, $orderSubGoods->sum('cost'), 2);
            $ratio_cost = $all_cost != 0 ? bcdiv($all_cost, $all_amount, 2) : 0;
            $length = count($good_array);

            $orderAddGood = new OrderAddGood;
            $orderAddGood->warehouse_id = $warehouse->id;
            $orderAddGood->date = $date;
            $orderAddGood->user_id = $user->id;
            $orderAddGood->key_name = $key_name;
            $orderAddGood->key_id = $key_id;
            $orderAddGood->status_cost = $status_cost;
            $orderAddGood->cost = $all_cost;
            $orderAddGood->save();

            foreach ($good_array as $key => $value) {
                $good = Good::find($value);

                $orderAddGoodDetail = new OrderAddGoodDetail;
                $orderAddGoodDetail->order_add_good_id = $orderAddGood->id;
                $orderAddGoodDetail->good_id = $good->id;
                $orderAddGoodDetail->coil_code = $coil_code_array[$key];
                $orderAddGoodDetail->amount = $amount_array[$key];
                $orderAddGoodDetail->cost = bcmul($amount_array[$key], $ratio_cost, 2);
                $orderAddGoodDetail->save();

                $warehouseGood = new WarehouseGood;
                $warehouseGood->warehouse_id = $warehouse->id;
                $warehouseGood->good_id = $good->id;
                $warehouseGood->amount = $amount_array[$key];
                $warehouseGood->coil_code = $coil_code_array[$key];
                $warehouseGood->ratio_cost = $ratio_cost;
                $warehouseGood->order_add_good_id = $orderAddGood->id;
                $warehouseGood->save();

                $warehouseGoodBalance = new WarehouseGoodBalance;
                $warehouseGoodBalance->warehouse_good_id = $warehouseGood->id;
                $warehouseGoodBalance->amount = $amount_array[$key];
                $warehouseGoodBalance->ratio_cost = $ratio_cost;
                $warehouseGoodBalance->cost = bcmul($amount_array[$key], $ratio_cost, 2);
                $warehouseGoodBalance->save();

                $all_cost = bcsub($all_cost, $warehouseGoodBalance->cost, 2);

                if ($key + 1 == $length) {
                    $warehouseGoodBalance->cost = bcadd($warehouseGoodBalance->cost, $all_cost, 2);
                    $warehouseGoodBalance->ratio_cost = bcdiv($warehouseGoodBalance->cost, $warehouseGoodBalance->amount, 2);
                    $warehouseGoodBalance->save();

                    $warehouseGood->ratio_cost = $warehouseGoodBalance->ratio_cost;
                    $warehouseGood->save();

                    $orderAddGoodDetail->cost = bcadd($orderAddGoodDetail->cost, $all_cost, 2);
                    $orderAddGoodDetail->save();
                }
            }

            foreach ($orderSubGoods as $orderSubGood) {
                $orderSubGood->order_add_good_id = $orderAddGood->id;
                $orderSubGood->save();
            }
        }

        return [
            'status' => true,
            'message' => 'success',
            'order_sub_good_id' => $orderAddGood->id
        ];
    }

    public function deleteOrderAddGood($key_name, $key_id)
    {
        $orderAddGoods = OrderAddGood::with('orderAddGoodDetails', 'orderSubGoods')->where('key_name', $key_name)->where('key_id', $key_id)->get();

        foreach ($orderAddGoods as $orderAddGood) {
            $warehouseGoods = WarehouseGood::with('good', 'warehouseGoodBalances')->where('order_add_good_id', $orderAddGood->id)->get();
            foreach ($warehouseGoods as $warehouseGood) {
                if ($warehouseGood->warehouseGoodBalances->count() > 1) {
                    return [
                        'status' => false,
                        'message' => 'สินค้า '. $warehouseGood->good->name .' มีการใช้ไปแล้ว กรุณายกเลิกบิลที่ใช้ไปก่อน',
                    ];
                }
                foreach ($warehouseGood->warehouseGoodBalances as $warehouseGoodBalance) {
                    $warehouseGoodBalance->delete();
                }
                $warehouseGood->delete();
            }
            foreach ($orderAddGood->orderSubGoods as $orderSubGood) {
                $orderSubGood->order_add_good_id = null;
                $orderSubGood->save();
            }
            foreach ($orderAddGood->orderAddGoodDetails as $orderAddGoodDetail) {
                $orderAddGoodDetail->delete();
            }
            $orderAddGood->delete();
        }

        return [
            'status' => true,
            'message' => 'success',
        ];
    }

    public function deleteOrderSubGood($key_name, $key_id)
    {
        $orderSubGoods = OrderSubGood::with('orderSubGoodDetails.warehouseGoodBalances')->where('key_name', $key_name)->where('key_id', $key_id)->get();

        foreach ($orderSubGoods as $orderSubGood) {
            foreach ($orderSubGood->orderSubGoodDetails as $orderSubGoodDetail) {
                foreach ($orderSubGoodDetail->warehouseGoodBalances as $warehouseGoodBalance) {
                    $warehouseGoodBalance->delete();
                }
                $orderSubGoodDetail->delete();
            }
            $orderSubGood->delete();
        }

        return [
            'status' => true,
            'message' => 'success',
        ];
    }
}
