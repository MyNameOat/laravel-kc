<?php

namespace App\Http\Controllers\Inv;

use App\Good;
use App\Http\Controllers\Controller;
use App\Http\Requests\RequisitionRequest;
use App\Requisition;
use App\Warehouse;
use App\Take;
use App\Type;
use App\GoodView;
use App\WarehouseGood;
use App\WarehouseGoodBalance;
use App\RequisitionGood;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function App\Http\menuAccesses;

class RequisitionApprove extends Controller
{
    public function dashboard(){
        return view('/');
    }

    /**
     * Display a listing of the resource.
     * @param int $menu_id
     * @return \Illmuinate\Http\Response
     */
    public function index(){

        $menuAccesses = menuAccesses(1);
        $warehouse_id = session('warehouse')['id'];
        $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 0)->where('edit_user_id', 0)->get();
        $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id');
        // dd($requisitions);
        $breadcrumbs = [
            'รายการใบเบิกสินค้าที่สร้าง' => '',
        ];

        $pagename = 'รายการใบเบิกสินค้าที่สร้าง';

        return view('requisition.index', compact('breadcrumbs','pagename','whs_lists','requisitions'));
    }
}
