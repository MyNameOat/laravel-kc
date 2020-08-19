<?php

namespace App\Http\Controllers\Whs;

use App\Requisition;
use App\RequisitionGood;
use App\Take;
use App\Type;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RequisitionController extends Controller
{
    public function index()
    {
        $requisitions = Requisition::where('warehouse_id', session('warehouse')['id'])->where('approve_user_id', 0)->get();

        return view('whs.requisitions.index', compact('requisitions'));
    }

    public function create()
    {
        $takes = Take::all();

        return view('whs.requisitions.create', compact('takes'));
    }

    public function store(Request $request)
    {
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

        foreach ($request->materialGoodId as $key => $value) {
            $requisitionGood = new RequisitionGood;
            $requisitionGood->requisition_id = $requisition->id;
            $requisitionGood->good_id = $value;
            $requisitionGood->coil_code = $request->materialGoodCoilCode[$key];
            $requisitionGood->amount = $request->materialAmount[$key];
            $requisitionGood->save();
        }

        DB::commit();

        return redirect()->route("whs.requisitions.index");
    }

    public function show($id)
    {
        $requisition = Requisition::find($id);

        return view('whs.requisitions.show', compact('requisition'));
    }

    public function edit($id)
    {
        $requisition = Requisition::find($id);
        $takes = Take::all();

        return view('whs.requisitions.edit', compact('requisition', 'takes'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $requisition = Requisition::find($id);

        foreach ($requisition->requisitionGoods as $requisitionGood) {
            $requisitionGood->delete();
        }


        $requisition->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $requisition->take_id = $request->take_id;
        $requisition->detail = $request->detail;
        $requisition->edit_user_id = auth()->user()->id;
        $requisition->edit_at = Carbon::now();
        $requisition->save();

        foreach ($request->materialGoodId as $key => $value) {
            $requisitionGood = new RequisitionGood;
            $requisitionGood->requisition_id = $requisition->id;
            $requisitionGood->good_id = $value;
            $requisitionGood->coil_code = $request->materialGoodCoilCode[$key];
            $requisitionGood->amount = $request->materialAmount[$key];
            $requisitionGood->save();
        }

        DB::commit();

        return redirect()->route("whs.requisitions.index");
    }
}
