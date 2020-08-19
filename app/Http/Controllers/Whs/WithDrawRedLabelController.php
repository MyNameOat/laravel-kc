<?php

namespace App\Http\Controllers\Whs;

use App\Http\Controllers\Api\GoodController;
use App\OrderAddGood;
use App\User;
use App\Warehouse;
use App\WithdrawRedLabel;
use App\WithdrawRedLabelMaterial;
use App\WithdrawRedLabelProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class WithDrawRedLabelController extends Controller
{
    public function index()
    {
        $withdrawRedLabels = WithdrawRedLabel::with('warehouse', 'createdUser')->where('warehouse_id', session('warehouse')['id'])->get();

        return view('whs.withdraw-red-label.index', compact('withdrawRedLabels'));
    }

    public function create()
    {
        return view('whs.withdraw-red-label.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $warehouse = Warehouse::find($request->warehouse_id);

        $withdrawRedLabel = new WithdrawRedLabel;
        $withdrawRedLabel->warehouse_id = $warehouse->id;
        $withdrawRedLabel->code = $this->getCurrentCode($warehouse, "WR");
        $withdrawRedLabel->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $withdrawRedLabel->detail = $request->detail;
        $withdrawRedLabel->created_user_id = auth()->user()->id;
        $withdrawRedLabel->save();

        foreach ($request->materialGoodId as $key => $value) {
            $withdrawRedLabelMaterial = new WithdrawRedLabelMaterial;
            $withdrawRedLabelMaterial->withdraw_red_label_id = $withdrawRedLabel->id;
            $withdrawRedLabelMaterial->good_id = $value;
            $withdrawRedLabelMaterial->coil_code = $request->materialGoodCoilCode[$key];
            $withdrawRedLabelMaterial->amount = $request->materialAmount[$key];
            $withdrawRedLabelMaterial->save();

            foreach ($request->productGoodId[$key] as $key2 => $value2) {
                $withdrawRedLabelProduct = new WithdrawRedLabelProduct;
                $withdrawRedLabelProduct->withdraw_red_label_material_id = $withdrawRedLabelMaterial->id;
                $withdrawRedLabelProduct->good_id = $value2;
                $withdrawRedLabelProduct->amount = $request->productAmount[$key][$key2];
                $withdrawRedLabelProduct->save();
            }
        }

        DB::commit();

        return redirect()->route('whs.withdraw-red-label.index');
    }

    public function show($id)
    {
        $withdrawRedLabel = WithdrawRedLabel::find($id);

        return view('whs.withdraw-red-label.show', compact('withdrawRedLabel'));
    }

    public function edit($id)
    {
        $withdrawRedLabel = WithdrawRedLabel::find($id);

        return view('whs.withdraw-red-label.edit', compact('withdrawRedLabel'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $withdrawRedLabel = WithdrawRedLabel::find($id);

        foreach ($withdrawRedLabel->withdrawRedLabelMaterials as $withdrawRedLabelMaterial) {
            foreach ($withdrawRedLabelMaterial->withdrawRedLabelProducts as $withdrawRedLabelProduct) {
                $withdrawRedLabelProduct->delete();
            }
            $withdrawRedLabelMaterial->delete();
        }

        $withdrawRedLabel->document_at = Carbon::createFromFormat('d/m/Y', $request->document_at);
        $withdrawRedLabel->detail = $request->detail;
        $withdrawRedLabel->edit_user_id = auth()->user()->id;
        $withdrawRedLabel->edit_at = Carbon::now();
        $withdrawRedLabel->approve_user_id = 0;
        $withdrawRedLabel->approve_at = null;
        $withdrawRedLabel->none_approve_user_id = 0;
        $withdrawRedLabel->none_approve_at = null;
        $withdrawRedLabel->cancle_detail = null;
        $withdrawRedLabel->save();

        foreach ($request->materialGoodId as $key => $value) {
            $withdrawRedLabelMaterial = new WithdrawRedLabelMaterial;
            $withdrawRedLabelMaterial->withdraw_red_label_id = $withdrawRedLabel->id;
            $withdrawRedLabelMaterial->good_id = $value;
            $withdrawRedLabelMaterial->coil_code = $request->materialGoodCoilCode[$key];
            $withdrawRedLabelMaterial->amount = $request->materialAmount[$key];
            $withdrawRedLabelMaterial->save();

            foreach ($request->productGoodId[$key] as $key2 => $value2) {
                $withdrawRedLabelProduct = new WithdrawRedLabelProduct;
                $withdrawRedLabelProduct->withdraw_red_label_material_id = $withdrawRedLabelMaterial->id;
                $withdrawRedLabelProduct->good_id = $value2;
                $withdrawRedLabelProduct->amount = $request->productAmount[$key][$key2];
                $withdrawRedLabelProduct->save();
            }
        }

        DB::commit();

        return redirect()->route('whs.withdraw-red-label.index');
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        $withdrawRedLabel = WithdrawRedLabel::find($id);

        $response = $this->deleteOrderAddGood('WithdrawRedLabel', $withdrawRedLabel->id);
        if ($response['status'] == false) {
            DB::rollBack();
            return back()->withErrors($response['message']);
        }

        $this->deleteOrderSubGood('WithdrawRedLabel', $withdrawRedLabel->id);
        if ($response['status'] == false) {
            DB::rollBack();
            return back()->withErrors($response['message']);
        }

        foreach ($withdrawRedLabel->withdrawRedLabelMaterials as $withdrawRedLabelMaterial) {
            foreach ($withdrawRedLabelMaterial->withdrawRedLabelProducts as $withdrawRedLabelProduct) {
                $withdrawRedLabelProduct->delete();
            }
            $withdrawRedLabelMaterial->delete();
        }

        $withdrawRedLabel->delete();

        DB::commit();

        return redirect()->route('whs.withdraw-red-label.index');
    }

    public function approveIndex()
    {
        $withdrawRedLabels = WithdrawRedLabel::where('warehouse_id', session('warehouse')['id'])->where('approve_user_id', 0)->where('none_approve_user_id', 0)->get();

        return view('whs.withdraw-red-label.approve.index', compact('withdrawRedLabels'));
    }

    public function approveShow($id)
    {
        $withdrawRedLabel = WithdrawRedLabel::find($id);

        return view('whs.withdraw-red-label.approve.show', compact('withdrawRedLabel'));
    }

    public function approveStore(Request $request, $id)
    {
        DB::beginTransaction();

        $withdrawRedLabel = WithdrawRedLabel::with('withdrawRedLabelMaterials', 'withdrawRedLabelProducts')->find($id);
        if ($request->approveStatus == 1) {
            $warehouse = Warehouse::find($withdrawRedLabel->warehouse_id);
            $user = User::find($withdrawRedLabel->created_user_id);

            $withdrawRedLabel->approve_user_id = auth()->user()->id;
            $withdrawRedLabel->approve_at = Carbon::now();
            $withdrawRedLabel->save();

            foreach ($withdrawRedLabel->withdrawRedLabelMaterials as $withdrawRedLabelMaterial) {
                $good_array = [$withdrawRedLabelMaterial->good_id];
                $coil_code_array = [$withdrawRedLabelMaterial->coil_code];
                $amount_array = [$withdrawRedLabelMaterial->amount];
                $response = $this->subGood(
                    $warehouse,
                    $withdrawRedLabel->document_at,
                    $user,
                    'WithdrawRedLabel',
                    $withdrawRedLabel->id,
                    $good_array,
                    $coil_code_array,
                    $amount_array
                );

                if ($response['status'] == false) {
                    DB::rollBack();
                    return back()->withErrors($response['message']);
                }

                $product_good_array = [];
                $product_coil_code_array = [];
                $product_amount_array = [];
                $order_sub_good_id_array = [$response['order_sub_good_id']];
                foreach ($withdrawRedLabelMaterial->withdrawRedLabelProducts as $withdrawRedLabelProduct) {
                    array_push($product_good_array, $withdrawRedLabelProduct->good_id);
                    array_push($product_coil_code_array, $withdrawRedLabelProduct->coil_code);
                    array_push($product_amount_array, $withdrawRedLabelProduct->amount);
                }
                $response = $this->addGood(
                    $warehouse,
                    $withdrawRedLabel->document_at,
                    $user,
                    'WithdrawRedLabel',
                    $withdrawRedLabel->id,
                    'Auto',
                    $order_sub_good_id_array,
                    $product_good_array,
                    $product_coil_code_array,
                    $product_amount_array,
                    []
                );

                if ($response['status'] == false) {
                    DB::rollBack();
                    return back()->withErrors($response['message']);
                }
            }
        } else {
            $withdrawRedLabel->none_approve_user_id = auth()->user()->id;
            $withdrawRedLabel->none_approve_at = Carbon::now();
            $withdrawRedLabel->cancle_detail = $request->cancelDetail;
            $withdrawRedLabel->save();
        }

        DB::commit();

        return redirect()->route('whs.withdraw-red-label.approve.index');
    }
}
