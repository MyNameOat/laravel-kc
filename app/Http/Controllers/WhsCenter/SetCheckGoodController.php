<?php

namespace App\Http\Controllers\WhsCenter;

use App\Good;
use App\GoodSetting;
use App\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SetCheckGoodController extends Controller
{
    public function index()
    {
        $goods = Good::with('type')->where('is_check_min_max', 1)->get();
        $good_ids = $goods->pluck('id')->toArray();

        return view('whs-center.goods.set-check-goods.index', compact("goods", "good_ids"));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        foreach($request->id as $value) {
            $good = Good::find($value);
            $good->is_check_min_max = 1;
            $good->save();
        }

        DB::commit();

        return redirect()->route("whs-center.goods.set-check-goods.index");
    }

    public function show($id)
    {
        DB::beginTransaction();

        $good = Good::find($id);
        $warehouses = Warehouse::all();

        DB::commit();

        return view("whs-center.goods.set-check-goods.show", compact('good', 'warehouses'));
    }

    public function setMinMax(Request $request, $id)
    {
        DB::beginTransaction();

        foreach ($request->min as $key => $value) {
            $goodSetting = GoodSetting::where('good_id', $id)->where('warehouse_id', $key)->first();
            if ($goodSetting == null) {
                $goodSetting = new GoodSetting;
            }

            $goodSetting->good_id = $id;
            $goodSetting->warehouse_id = $key;
            $goodSetting->min = $value;
            $goodSetting->max = $request->max[$key];
            $goodSetting->save();
        }

        DB::commit();

        return redirect()->route("whs-center.goods.set-check-goods.show", $id)->with('status', 'บันทึกข้อมูลใหม่เรียบร้อยแล้ว!');;
    }
}
