<?php

namespace App\Http\Controllers\WhsCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Type;
use App\Good;
use App\Warehouse;

class ReportAmountGoodWarehouseController extends Controller
{
    public function index()
    {
        $types = Type::all();
        $warehouses = Warehouse::all();
        return view('whs-center.report-amount-good-warehouses.index',compact('types','warehouses'));
    }

    public function ajaxSearchGoodByType(Request $request)
    {
        $goods = Good::where('type_id', $request->type_id)->get();
        $view = View::make('whs-center.report-amount-good-warehouses.view-make.good-by-type', compact('goods'))->render();
            return response()->json([
                'html' => $view,
        ]);
    }

    public function ajaxSearchSelectedGoodList(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $good = Good::find($request->good_id);
        $warehouses = Warehouse::with([
        'goodviews' => function ($query) use ($good) {
            $query->where('good_id', $good->id);
            },
        'warehouseGoods' => function ($query) use ($good) {
            $query->with('warehouseGoodBalances')->where('good_id', $good->id);
        }])->get();
        $view = View::make('whs-center.report-amount-good-warehouses.view-make.selected-good-list', compact('good','warehouses','startDate','endDate'))->render();
            return response()->json([
                'html' => $view,
        ]);
    }

    public function sellMonthIndex()
    {
        $types = Type::all();
        $warehouses = Warehouse::all();
        return view('whs-center.report-sell-good-month-warehouses.index',compact('types','warehouses'));
    }

    public function ajaxSearchSelectedGoodListSellMonth(Request $request)
    {
        $month = $request->month;
        $good = Good::find($request->good_id);
        $warehouses = Warehouse::with([
        'warehouseGoods' => function ($query) use ($good) {
            $query->with('warehouseGoodBalances')->where('good_id', $good->id);
        }])->get();
        $view = View::make('whs-center.report-sell-good-month-warehouses.view-make.selected-good-list-month', compact('good','warehouses','month'))->render();
            return response()->json([
                'html' => $view,
        ]);
    }

    public function amountMonthIndex()
    {
        $types = Type::all();
        $warehouses = Warehouse::all();
        return view('whs-center.report-amount-good-month-warehouses.index',compact('types','warehouses'));
    }

    public function ajaxSearchSelectedGoodListAmountMonth(Request $request)
    {
        $month = $request->month;
        $good = Good::find($request->good_id);
        $warehouses = Warehouse::with([
        'warehouseGoods' => function ($query) use ($good) {
            $query->with('warehouseGoodBalances')->where('good_id', $good->id);
        }])->get();
        $view = View::make('whs-center.report-amount-good-month-warehouses.view-make.selected-good-list-month', compact('good','warehouses','month'))->render();
            return response()->json([
                'html' => $view,
        ]);
    }
}
