<?php

namespace App\Http\Controllers\whs;

use App\GoodAudit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function dashboard()
    {
        $goodAudits = GoodAudit::where('warehouse_id', session('warehouse')['id'])->with(['prGoods' => function ($query) {
            $query->whereHas('pr', function (Builder $query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            })->whereDoesntHave('poGood.whReceiveGoods.rrGood');
        }])->get();
        return view('whs.dashboard', compact('goodAudits'));
    }
}
