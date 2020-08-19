<?php

namespace App\Http\Controllers\WhsCenter;

use App\PoGood;
use App\Pr;
use App\Vendor;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PoController extends Controller
{
    public function selectPr() {
        $prs = Pr::with('warehouse', 'poGoods', 'prGoods')->where('approve_user_id', '>', 0)->get();
        $prs = $prs->filter(function ($pr) {
            return $pr->poGoods->count() + $pr->prGoods->where('cancle_status', 1)->count() != $pr->prGoods->count();
        });
        foreach ($prs as $key => $pr) {
            $po_goods_count = $pr->poGoods->count();
            if ($po_goods_count == 0) {
                $pr->status = 0;
            } elseif ($po_goods_count > 0 && $po_goods_count < $pr->prGoods->count()) {
                $pr->status = 1;
            } elseif ($po_goods_count > 0 && $po_goods_count == $pr->prGoods->count()) {
                $pr->status = 2;
            }
        }

        return view('whs-center.pos.select-pr', compact('prs'));
    }

    public function selectVendor(Request $request) {
        if (!isset($request->pr_id)) {
            return redirect()->route('whs-center.pos.select-pr');
        }

        $pr = Pr::with('warehouse', 'prGoods.poGood.po.vendor', 'prGoods.good', 'prGoods.goodViews', 'prGoods.unit', 'prGoods.pr')->find($request->pr_id);
        $pr->prGoods = $pr->prGoods->filter(function ($prGood) {
            return $prGood->poGood == null && $prGood->cancle_status != 1;
        });

        foreach ($pr->prGoods as $key => $prGood) {
            $good = $prGood->good;
            $last_poGood = PoGood::where('good_id', $good->id)->get();
            if ($last_poGood->isEmpty()) {
                $prGood->status = 1;
            } else {
                $prGood->status = 2;
                $prGood->last_price = $last_poGood->first()->price;
                $prGood->last_vendor = $last_poGood->first()->po->vendor->name;
            }
        }

        $vendors = Vendor::all();
        return view('whs-center.pos.select-vendor', compact('vendors', 'pr'));
    }

    public function cancelPr(Request $request, $id)
    {
        DB::beginTransaction();

        $pr = Pr::find($id);
        if ($pr->prGoods()->withTrashed()->count() != $pr->prGoods->count() && $pr->deleted_user_id == 0) {
            return back()->withErrors('Pr นี้มีการสั่งซื้อไปบางส่วนแล้ว จึงไม่สามารถยกเลิกได้');
        }

        $pr->approve_user_id = 0;
        $pr->approve_at = null;
        $pr->none_approve_user_id = auth()->user()->id;
        $pr->none_approve_at = Carbon::now();
        $pr->cancle_detail = $request->cancel_detail;
        $pr->save();

        DB::commit();

        Session::flash('status', 'ปฏิเสธใบสั่งซื้อสินค้าแล้ว');
        return redirect()->route('whs-center.pos.select-pr');
    }

    public function clearPr($id)
    {
        DB::beginTransaction();

        $pr = Pr::find($id);

        $pr->prGoods = $pr->prGoods->filter(function ($prGood) {
            return ($prGood->poGood == 0);
        });

        foreach ($pr->prGoods as $prGood) {
            $prGood->cancle_status = 1;
            $prGood->save();
        }

        DB::commit();

        Session::flash('status', 'เคลียใบสั่งซื้อสินค้าแล้ว');
        return redirect()->route('whs-center.pos.select-pr');
    }
}
