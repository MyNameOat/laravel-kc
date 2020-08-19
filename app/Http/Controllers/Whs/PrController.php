<?php

namespace App\Http\Controllers\Whs;

use App\Good;
use App\Pr;
use App\PrGood;
use App\Type;
use App\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Session;

class PrController extends Controller
{
    public function index()
    {
        $prs = Pr::where('warehouse_id', session('warehouse')['id'])->where('edit_user_id', 0)->where('approve_user_id', 0)->get();

        return view('whs.prs.index', compact('prs'));
    }

    public function create(Request $request)
    {
        $goods = Good::with('type', 'unit')
            ->with(['goodView' => function ($query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            }])->get();

        $select_goods = Good::with('type', 'unit')->with(['goodView' => function ($query) {
            $query->where('warehouse_id', session('warehouse')['id']);
        }])->whereIn('id', $request->good_ids)->get();

        return view('whs.prs.create', compact('goods', 'select_goods'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $warehouse = Warehouse::find(session('warehouse')['id']);
        $pr = new Pr;
        $pr->department_id = $warehouse->id;
        $pr->warehouse_id = $warehouse->id;
        $pr->code = $this->getCurrentCode($warehouse, "PR");
        $pr->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $pr->required_at = Carbon::createFromFormat('d/m/Y', $request->required_at);
        $pr->detail = $request->detail;
        $pr->created_user_id = auth()->user()->id;
        $pr->save();

        foreach ($request->good_id as $key => $value) {
            $good = Good::find($value);

            $prGood = new PrGood;
            $prGood->pr_id = $pr->id;
            $prGood->good_id = $good->id;
            $prGood->amount = str_replace(",", "", $request->amount[$key]);

            if ($good->type_id == 1) {
                $prGood->unit_id = 1;
            } else {
                $prGood->unit_id = $good->unit_id;
            }

            $prGood->save();
        }

        DB::commit();

        Session::flash('status', 'สร้างใบขอซื้อสินค้าสำเร็จ');
        return redirect()->route("whs.prs.index");
    }

    public function show($id)
    {
        $pr = Pr::with('warehouse', 'createdUser', 'editUser', 'deletedUser', 'approveUser', 'noneApproveUser')
            ->with(['prGoods.good.goodView' => function ($query) {
            $query->where('warehouse_id', session('warehouse')['id']);
        }])->find($id);

        return view('whs.prs.show', compact('pr'));
    }

    public function edit($id)
    {
        $pr = Pr::with('warehouse', 'createdUser', 'editUser', 'deletedUser', 'approveUser', 'noneApproveUser')
            ->with(['prGoods.good.goodView' => function ($query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            }])->find($id);

        $goods = Good::with('type', 'unit')
            ->with(['goodView' => function ($query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            }])
            ->get();

        return view('whs.prs.edit', compact('pr', 'goods'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $pr = Pr::find($id);

        if (!$pr->updated_at->eq($request->updated_at)) {
            DB::rollback();
            return redirect()->route('whs.prs.index')->withErrors('เอกสารนี้มีการอัพเดตแล้ว กรุณาตรวสอบ');
        }

        $pr_new = new Pr;
        $pr_new->parent_id = $pr->id;
        $pr_new->warehouse_id = $pr->warehouse_id;
        $pr_new->code = $pr->code;
        $pr_new->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $pr_new->required_at = Carbon::createFromFormat('d/m/Y', $request->required_at);
        $pr_new->detail = $request->detail;
        $pr_new->created_user_id = auth()->user()->id;
        $pr_new->save();

        foreach ($request->good_id as $key => $value) {
            $good = Good::find($value);

            $prGood = new PrGood;
            $prGood->pr_id = $pr_new->id;
            $prGood->good_id = $good->id;
            $prGood->amount = str_replace(",", "", $request->amount[$key]);

            if ($good->type_id == 1) {
                $prGood->unit_id = 1;
            } else {
                $prGood->unit_id = $good->unit_id;
            }

            $prGood->save();
        }

        $pr->edit_user_id = auth()->user()->id;
        $pr->edit_at = Carbon::now();
        $pr->save();

        foreach ($pr->prGoods as $key => $prGood) {
            $prGood->delete();
        }

        DB::commit();

        Session::flash('status', 'แก้ไขใบขอซื้อสินค้าสำเร็จ');
        return redirect()->route('whs.prs.index');
    }

    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();

        $pr = Pr::find($id);

        if (!$pr->updated_at->eq($request->updated_at)) {
            DB::rollback();
            return redirect()->route('whs.prs.index')->withErrors('เอกสารนี้มีการอัพเดตแล้ว กรุณาตรวสอบ');
        }

        $pr->deleted_user_id = auth()->user()->id;
        $pr->save();

        foreach ($pr->prGoods as $key => $prGood) {
            $prGood->delete();
        }

        $pr->delete();

        DB::commit();

        Session::flash('status', 'ลบใบขอซื้อสินค้าสำเร็จ');
        return redirect()->route('whs.prs.index');
    }

    public function approveIndex()
    {
        DB::beginTransaction();

        $prs = Pr::where('warehouse_id', session('warehouse')['id'])
            ->where('created_user_id', '>', 0)
            ->where('edit_user_id', 0)
            ->where('deleted_user_id', 0)
            ->where('none_approve_user_id', 0)
            ->where('approve_user_id', 0)
            ->get();

        DB::commit();

        return view('whs.prs.approve', compact('prs'));
    }

    public function approveShow($id)
    {
        $pr = Pr::with('warehouse', 'createdUser', 'editUser', 'deletedUser', 'approveUser', 'noneApproveUser')
            ->with(['prGoods.good.goodView' => function ($query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            }])->find($id);

        return view('whs.prs.show', compact('pr'));
    }

    public function approveUpdate(Request $request, $id)
    {
        DB::beginTransaction();

        $pr = Pr::find($id);

        if (!$pr->updated_at->eq($request->updated_at)) {
            DB::rollback();
            return redirect()->route('whs.prs.index')->withErrors('เอกสารนี้มีการอัพเดตแล้ว กรุณาตรวสอบ');
        }

        if ($request->is_approve == 1) {

            $pr->approve_user_id = auth()->user()->id;
            $pr->approve_at = Carbon::now();

            Session::flash('status', 'อนุมัติใบขอซื้อสินค้าสำเร็จ');
        } else {
            if ($pr->prGoods()->withTrashed()->count() != $pr->prGoods->count() && $pr->deleted_user_id == 0) {
                DB::rollback();
                return back()->withErrors('Pr นี้มีการสั่งซื้อไปบางส่วนแล้ว จึงไม่สามารถยกเลิกได้');
            }

            $pr->approve_user_id = 0;
            $pr->approve_at = null;
            $pr->none_approve_user_id = auth()->user()->id;
            $pr->none_approve_at = Carbon::now();
            $pr->cancle_detail = $request->cancle_detail;

            Session::flash('status', 'ปฏิเสธอนุมัติขอซื้อสินค้าแล้ว');
        }

        $pr->save();

        DB::commit();

        return redirect()->route('whs.prs-approve.index');
    }

    public function reportIndex(Request $request)
    {
        if (isset($request->start_at) && $request->start_at != null) {
            $start_at = $request->start_at;
        } else {
            $start_at = Carbon::now()->format('d/m/Y');
        }

        if (isset($request->end_at) && $request->end_at != null) {
            $end_at = $request->end_at;
        } else {
            $end_at = Carbon::now()->format('d/m/Y');
        }

        $start = Carbon::createFromFormat('d/m/Y H:i:s', $start_at . ' 00:00:00');
        $end = Carbon::createFromFormat('d/m/Y H:i:s', $end_at . ' 23:59:59');

        $prs = Pr::withTrashed()
            ->where('warehouse_id', session('warehouse')['id'])
            ->where('edit_user_id', 0)
            ->where('document_at', '>', $start)
            ->where('document_at', '<=', $end)
            ->get();

        return view('whs.prs.report', compact('prs', 'start_at', 'end_at'));
    }

    public function reportShow($id)
    {
        $pr = Pr::with('warehouse', 'createdUser', 'editUser', 'deletedUser', 'approveUser', 'noneApproveUser')
            ->with(['prGoods.good.goodView' => function ($query) {
                $query->where('warehouse_id', session('warehouse')['id']);
            }])->find($id);

        return view('whs.prs.show', compact('pr'));
    }
}
