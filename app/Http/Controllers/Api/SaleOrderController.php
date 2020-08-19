<?php

namespace App\Http\Controllers\Api;

use App\HS;
use App\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SaleOrderController extends Controller
{
    function summarySaleInDay()
    {
        $today = Carbon::today()->format('Y-m-d');
        $invoice_price = Invoice::whereDate('created_at', $today)->sum('total_amount');
        $hs_price = HS::whereDate('created_at', $today)->sum('total_amount');

        $warehouse_invoice_price = DB::table('invoices')
            ->select('warehouse_id', DB::raw('sum(total_amount) as total_amount'))
            ->whereDate('created_at', $today)
            ->whereNull('deleted_at')
            ->groupBy('warehouse_id')
            ->get();

        $warehouse_hs_price = DB::table('h_s_s')
            ->select('warehouse_id', DB::raw('sum(total_amount) as total_amount'))
            ->whereDate('created_at', $today)
            ->whereNull('deleted_at')
            ->groupBy('warehouse_id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => [
                'invoice_price' => $invoice_price,
                'hs_price' => $hs_price,
                'warehouse_invoice_price' => $warehouse_invoice_price,
                'warehouse_hs_price' => $warehouse_hs_price,
            ]
        ]);
    }
}
