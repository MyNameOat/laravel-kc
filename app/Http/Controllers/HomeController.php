<?php

namespace App\Http\Controllers;

use App\Good;
use App\GoodView;
use App\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function selectWarehouse()
    {
        $warehouses = Warehouse::all();

        return view('select-warehouse', compact('warehouses'));
    }

    public function storeWarehouse(Request $request)
    {
        $warehouse = Warehouse::find($request->warehouse_id);
        $request->session()->put('warehouse', $warehouse);
        return redirect()->route('select-program');
    }

    public function selectProgram()
    {
        if (session('warehouse') == null) {
            return redirect()->route('select-warehouse');
        }
        return view('select-program');
    }

    public function getStockTable(Request $request)
    {
        $goodViews = GoodView::with('good.type', 'good.unit')->where('warehouse_id', session('warehouse')['id'])->where('balance_amount', '>', 0);

        if ($request->only_array != null) {
            $good_id_array = Good::whereIn('type_id', $request->only_array)->pluck('id');
            $goodViews = $goodViews->whereIn('good_id', $good_id_array);
        }

        if ($request->except_array != null) {
            $good_id_array = Good::whereIn('type_id', $request->except_array)->pluck('id');
            $goodViews = $goodViews->whereNotIn('good_id', $good_id_array);
        }

        $goodViews = $goodViews->get();

        $view = View::make('stock-table', compact('goodViews'))->render();
        return response()->json([
            'html' => $view,
        ]);
    }

    public function getRedLabelTable()
    {
        $goods = Good::with('type', 'unit')->where('type_id', 12)->get();

        $view = View::make('red-label-table', compact('goods'))->render();
        return response()->json([
            'html' => $view,
        ]);
    }

    public function getGoodTable(Request $request)
    {
        $goods = Good::with('type', 'unit')->whereNotIn('type_id', [12]);

        if ($request->good_ids != null && count($request->good_ids) > 0) {
            $goods = $goods->whereNotIn('id', $request->good_ids);
        }

        $goods = $goods->get();

        $view = View::make('good-table', compact('goods'))->render();
        return response()->json([
            'html' => $view,
        ]);
    }
}
