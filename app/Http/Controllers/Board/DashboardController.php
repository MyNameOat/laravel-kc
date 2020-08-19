<?php

namespace App\Http\Controllers\Board;

use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('board.dashboard');
    }

    public function summarySaleCurrentDay()
    {
        $now = Carbon::today();
        $warehouses = Warehouse::all();

        return view('board.summary-sale', compact('now', 'warehouses'));
    }
}
