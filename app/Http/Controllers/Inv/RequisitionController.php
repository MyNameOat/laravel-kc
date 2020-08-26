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

class RequisitionController extends Controller
{
    public function dashboard(){
        return view('inv.dashboard');
    }

    /**
     * Display a listing of the resource.
     * @param int $menu_id
     * @return \Illmuinate\Http\Response
     */
    public function index(){

        $warehouse_id = session('warehouse')['id'];
        $menuAccesses = menuAccesses(1);
        // dd($menuAccesses);
        $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        // dd($department_ids);
        $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 0)->whereIn('none_approve_user_id', [0,1])
        ->whereIn('edit_user_id', [0,1])->get();
        // dd($requisitions);
        $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id');
        // dd($whs_lists);
        $breadcrumbs = [
            'รายการใบเบิกสินค้าที่สร้าง' => '',
        ];

        $pagename = 'รายการใบเบิกสินค้าที่สร้าง';

        return view('inv.requisitions.index', compact('breadcrumbs', 'pagename', 'requisitions', 'whs_lists'));
    }

         /**
     * Display a listing of the resource.
     * @param int $menu_id
     * @return \Illmuinate\Http\Response
     */
    public function approve(){
        $menuAccesses = menuAccesses(2);
        $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 0)->where('none_approve_user_id', 0)->get();
        $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id');

        $breadcrumbs = [
            'รายการใบเบิกสินค้าที่สร้าง' => '',
        ];

        $pagename = 'รายการใบเบิกสินค้าที่สร้าง';

        // $menuAccesses = menuAccesses(2);

        // $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        // $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 0)->where('edit_user_id', 1)->get();
        // $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id');
        // $breadcrumbs = [
        //     'รายการใบเบิกสินค้าที่รออนุมัติ' => '',
        // ];

        // $pagename = 'รายการใบเบิกสินค้าที่รออนุมัติ';

        return view('inv.requisitions.approve', compact('breadcrumbs', 'pagename', 'requisitions', 'whs_lists'));
    }

        /**
     * Display a listing of the resource.
     * @param int $menu_id
     * @return \Illmuinate\Http\Response
     */
    public function reportRequisitions()
    {
        $menuAccesses = menuAccesses(1);
        $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 1)->where('warehouse_id', session('warehouse')['id'])->get();
        $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id');

        $breadcrumbs = [
            'รายการใบเบิกสินค้าที่สร้าง' => '',
        ];

        $pagename = 'รายการใบเบิกสินค้าที่สร้าง';

        // $menuAccesses = menuAccesses(1);

        // $department_ids = $menuAccesses->count() ? $menuAccesses->pluck('department_id')->toArray() : [0];
        // $whs_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('FullName', 'id')->prepend('ทั้งหมด', 0);
        // $whs_id_lists = Warehouse::whereIn('department_id', $department_ids)->get()->pluck('id');
        // $requisitions = Requisition::whereIn('department_id', $department_ids)->where('approve_user_id', 1)->where('warehouse_id', session('warehouse')['id'])->get();
        // $breadcrumbs = [
        //     'รายงานใบเบิกสินค้า' => '',
        // ];

        // $pagename = 'รายงานใบเบิกสินค้า';

        return view('inv.requisitions.report-requisition', compact('breadcrumbs', 'pagename', 'whs_lists', 'requisitions', 'warehouse_id', 'start_at', 'end_at'));
    }

    public function reportStoreDetail($id_bill){
        $requisitions = Requisition::with('take')->where('id', $id_bill)->get();
        // return($requisitions);
        $requisitionGoods = RequisitionGood::with('requisition')->where('requisition_id', $id_bill)->get();
        $take_lists = Take::all();
        return view('inv.requisitions.report-store-detail', compact('requisitionGoods', 'requisitions','take_lists'));
    }

    //Modal Good Select
    public function searchGoods(Request $request){

        $sess_wh_id = session('warehouse')['id'];
        $type_id = $request->type_id;
        if ($request->type_id == "all") {
            $good_views = GoodView::with('good.type', 'good.unit')
                ->where('warehouse_id', (int) $sess_wh_id)
                ->limit(100)
                ->get();
        } else {
            $good_views = GoodView::with('good.type', 'good.unit')
                ->whereHas('good', function ($query) use ($type_id) {
                    $query->where('type_id', (int) $type_id);
                })
                ->where('warehouse_id', (int) $sess_wh_id)
                ->get();
        }

        $data = array();
        foreach ($good_views as $key => $good_view) {
            $coil_code = ($request->type_id == 1) ? $good_view->coil_code : null;
            $code = ($request->type_id == 1) ? $good_view->coil_code : $good_view->good->code;
            $warehouse_good_id = $good_view->warehouse_good_id;
            $amount = WarehouseGoodBalance::where('warehouse_good_id', $warehouse_good_id)->sum('amount');
            $data[] = [
                'code' => $code . '<input type="hidden" class="coil_code" value="'
                . $coil_code . '"><input type="hidden" class="good_id" value="'
                . $good_view->good->id . '"><input type="hidden" class="warehouse_good_id" value="'
                . $warehouse_good_id . '">',
                'name' => $good_view->good->name,
                'amount' => $amount,
                'unit' => $good_view->good->unit->name,
                'check' => '<input type="checkbox" class="check-list">',
            ];
        }

        return response()->json([
            'data' => $data,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function formCreate(Request $request){
        $sess_wh = session('warehouse')['id'];
        $whs = Warehouse::where('id', $request->sess_wh)->first();
        $types = Type::all();
        $takes = Take::all();
        $breadcrumbs = [
            'รายการใบเบิกสินค้าที่สร้าง' => route('inv.index'),
            'สร้างใบเบิกสินค้า' => '',
        ];

        $pagename = 'สร้างใบเบิกสินค้า';

        return view('inv.requisitions.form-create', compact('breadcrumbs', 'pagename', 'whs', 'takes', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveStore(RequisitionRequest $request)
    {
        // return $request;
        DB::beginTransaction();

        $warehouse = Warehouse::find($request->warehouse_id);

        $requisition = new Requisition;
        $requisition->code = $this->getCurrentCode($warehouse, "RQ");
        $requisition->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $requisition->take_id = $request->take_id;
        $requisition->department_id = $warehouse->department_id;
        $requisition->warehouse_id = $warehouse->id;
        $requisition->detail = $request->detail;
        $requisition->created_user_id = auth()->user()->id;
        $requisition->save();

        // foreach ($request->good_id as $key => $value) {
        //     $requisitionGood = new RequisitionGood;
        //     $requisitionGood->requisition_id = $requisition->id;
        //     $requisitionGood->warehouse_good_id = $request->warehouse_good_id[$key];
        //     $requisitionGood->good_id = $value;
        //     $requisitionGood->amount = $request->amount[$key];
        //     $requisitionGood->unit_id = $requisitionGood->good->unit->id;
        //     $requisitionGood->coil_code = $request->coil_code[$key];
        //     $requisitionGood->save();
        // }

        foreach ($request->warehouse_good_id as $key => $value) {
            $warehouse_good = WarehouseGood::find($value);
            $requisition_good = new RequisitionGood;
            $requisition_good->requisition_id = $requisition->id;
            $requisition_good->warehouse_good_id = $warehouse_good->id;
            $requisition_good->good_id = $warehouse_good->good_id;
            $requisition_good->amount = $request->amount[$key];
            $requisition_good->unit_id = $requisition_good->good->unit_id;
            $requisition_good->save();

            $warehouse_good_balance = new WarehouseGoodBalance;
            $warehouse_good_balance->warehouse_good_id = $warehouse_good->id;
            $warehouse_good_balance->amount = -$request->amount[$key];
            $warehouse_good_balance->requisition_good_id = $requisition_good->id;

            if ($warehouse_good->warehouseGoodBalances->last()->amount_balance - $request->amount[$key] == 0) {
                $warehouse_good_balance->cost = -$warehouse_good->warehouseGoodBalances->last()->cost_balance;
                $warehouse_good_balance->amount_balance = 0;
                $warehouse_good_balance->cost_balance = 0;
                $warehouse_good_balance->ratio_cost = $warehouse_good->warehouseGoodBalances->last()->cost_balance / $request->amount[$key];
            } else {
                $warehouse_good_balance->ratio_cost = $warehouse_good->warehouseGoodBalances->last()->ratio_cost;
                $warehouse_good_balance->cost = bcmul(-1.00, $warehouse_good->warehouseGoodBalances->last()->ratio_cost * $request->amount[$key], 2);
                $warehouse_good_balance->amount_balance = $warehouse_good->warehouseGoodBalances->last()->amount_balance - $request->amount[$key];
                $warehouse_good_balance->cost_balance = $warehouse_good->warehouseGoodBalances->last()->cost_balance - $warehouse_good_balance->cost;
            }

            $warehouse_good_balance->save();
        }

        DB::commit();

        return redirect()->route("inv.index");

        // $warehouse_id = session('warehouse')['id'];
        // DB::beginTransaction();
        // foreach ($request->warehouse_good_id as $key => $value) {
        //     $goodview = GoodView::where('warehouse_good_id', $value)->first();
        //     if ($request->amount[$key] > 10000) {
        //         return back()->withErrors('สินค้าไม่เพียงพอ');
        //     }
        // }

        // $warehouse = Warehouse::find($warehouse_id);
        // $requisition = new Requisition;
        // $requisition->code = $this->getCode($warehouse);
        // $requisition->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        // $requisition->take_id = $request->take_id;
        // $requisition->department_id = $warehouse->department_id;
        // $requisition->warehouse_id = $warehouse->id;
        // $requisition->detail = $request->detail;
        // $requisition->created_user_id = auth()->user()->id;
        // $requisition->save();

        // foreach ($request->warehouse_good_id as $key => $value) {
        //     $warehouse_good = WarehouseGood::find($value);
        //     $requisition_good = new RequisitionGood;
        //     $requisition_good->requisition_id = $requisition->id;
        //     $requisition_good->warehouse_good_id = $warehouse_good->id;
        //     $requisition_good->good_id = $warehouse_good->good_id;
        //     $requisition_good->amount = $request->amount[$key];
        //     $requisition_good->unit_id = $warehouse_good->good->unit_id;
        //     $requisition_good->save();

        //     $warehouse_good_balance = new WarehouseGoodBalance;
        //     $warehouse_good_balance->warehouse_good_id = $warehouse_good->id;
        //     $warehouse_good_balance->amount = -$request->amount[$key];
        //     $warehouse_good_balance->requisition_good_id = $requisition_good->id;

        //     if ($warehouse_good->warehouseGoodBalances->last()->amount_balance - $request->amount[$key] == 0) {
        //         $warehouse_good_balance->cost = -$warehouse_good->warehouseGoodBalances->last()->cost_balance;
        //         $warehouse_good_balance->amount_balance = 0;
        //         $warehouse_good_balance->cost_balance = 0;
        //         $warehouse_good_balance->ratio_cost = $warehouse_good->warehouseGoodBalances->last()->cost_balance / $request->amount[$key];
        //     } else {
        //         $warehouse_good_balance->ratio_cost = $warehouse_good->warehouseGoodBalances->last()->ratio_cost;
        //         $warehouse_good_balance->cost = bcmul(-1.00, $warehouse_good->warehouseGoodBalances->last()->ratio_cost * $request->amount[$key], 2);
        //         $warehouse_good_balance->amount_balance = $warehouse_good->warehouseGoodBalances->last()->amount_balance - $request->amount[$key];
        //         $warehouse_good_balance->cost_balance = $warehouse_good->warehouseGoodBalances->last()->cost_balance - $warehouse_good_balance->cost;
        //     }

        //     $warehouse_good_balance->save();
        // }

        // DB::commit();

        // return redirect()->route('inv.index');
    }

    public function formEdit($id_bill)
    {
        $sess_wh = session('warehouse')['id'];
        $requisitions = Requisition::where('id', $id_bill)->get();
        $requisitionGoods = RequisitionGood::with('requisition','warehouseGoodBalance')->where('requisition_id', $id_bill)->get();
        $types = Type::all();
        $takes = Take::all();

        return view('inv.requisitions.form-edit', compact('requisitionGoods', 'requisitions','types','takes'));
    }


    public function updateStore(Request $request)
    {
        // return $request;
        DB::beginTransaction();

        $requisition = Requisition::find($request->requisition_id);

        foreach ($requisition->requisitionGoods as $requisitionGood) {
            $requisitionGood->delete();
        }

        $requisition->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $requisition->take_id = $request->take_id;
        $requisition->detail = $request->detail;
        $requisition->edit_user_id = auth()->user()->id;
        $requisition->edit_at = Carbon::now();
        $requisition->save();

        foreach ($request->good_id as $key => $value) {
            $requisitionGood = new RequisitionGood;
            $requisitionGood->requisition_id = $requisition->id;
            $requisitionGood->warehouse_good_id = $request->warehouse_good_id[$key];
            $requisitionGood->good_id = $value;
            $requisitionGood->coil_code = $request->coil_code[$key];
            $requisitionGood->amount = $request->amount[$key];
            $requisitionGood->unit_id = $requisitionGood->good->unit_id;
            $requisitionGood->save();
        }

        DB::commit();

        return redirect()->route('inv.index');

    }

    public function deleteStore($id_bill){
        DB::beginTransaction();
        $requisition_good = RequisitionGood::where('requisition_id', $id_bill)->first();
        $requisition_id = $requisition_good->id;
        $warehouse_good_balance_delete = WarehouseGoodBalance::where('requisition_good_id', $requisition_id)->delete();
        $requisition_delete = Requisition::where('id', $id_bill)->delete();
        $requisition_good_deletes = RequisitionGood::where('requisition_id', $id_bill)->delete();
        DB::commit();
        return redirect()->route('inv.index');
    }

    public function approveDetail($id_bill)
    {
        $requisitions = Requisition::where('id', $id_bill)->get();
        $requisitionGoods = RequisitionGood::with('requisition')->where('requisition_id', $id_bill)->get();
        return view('inv.requisitions.approve-detail', compact('requisitionGoods', 'requisitions'));
    }

    public function approveCheckStatusNo($id_bill){
        // return $id_bill;
        DB::beginTransaction();
        $requisition = Requisition::where('id', $id_bill)
        ->update([
            'approve_user_id' => 1
        ]);
        // return $requisition;
        DB::commit();

        return redirect()->route('inv.report-status-config');
    }

    public function approveCheckStatusOff($id_bill){
        // return $id_bill;
        DB::beginTransaction();
        $requisition = Requisition::where('id', $id_bill)
        ->update([
            'none_approve_user_id' => 1
        ]);
        // return $requisition;
        DB::commit();

        return redirect()->route('inv.index');
    }

    public function reportStatusDelete($id){
        // dd($id);
        DB::beginTransaction();
        $requisition_good = RequisitionGood::where('requisition_id', $id)->first();
        $requisition_id = $requisition_good->id;
        $warehouse_good_balance_delete = WarehouseGoodBalance::where('requisition_good_id', $requisition_id)->delete();
        $requisition_delete = Requisition::where('id', $id)->delete();
        $requisition_good_deletes = RequisitionGood::where('requisition_id', $id)->delete();
        DB::commit();
        return redirect()->route('inv.report-status-config');
    }


    public function getCode($warehouse)
    {
        $now_at = Carbon::now();

        $month = $now_at->month;

        if (strlen($month) == 1) {
            $month = '0' . $month;
        }

        $year = substr($now_at->year + 543, -2);

        $search_code = $warehouse->code . 'RQ' . $year . $month;

        $lastest_code = Requisition::withTrashed()->where('code', 'LIKE', $search_code . '%')->orderBy('code', 'desc')->first();

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

}
