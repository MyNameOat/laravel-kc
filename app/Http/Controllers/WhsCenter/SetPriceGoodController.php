<?php

namespace App\Http\Controllers\WhsCenter;


use App\Http\Controllers\Controller;
use App\Good;
use App\GoodDetailBenefit;
use App\GoodPrice;
use App\GoodWarehouse;
use App\Warehouse;
use App\Member_type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetPriceGoodController extends Controller
{

    public function index (){

        $load_type_coils = DB::table('load_type_coils')->get();
        $load_girths = DB::table('load_girths')->get();
        $load_thicks = DB::table('load_thicks')->get();
        $load_colors = DB::table('load_colors')->get();
        $load_azs = DB::table('load_azs')->get();
        $load_gs = DB::table('load_gs')->get();
        $load_type_products = DB::table('load_type_products')->get();
        $load_type_roofs = DB::table('load_type_roofs')->get();

        return view('whs-center.goods.set-price-goods.index', compact('load_type_coils','load_girths','load_thicks','load_colors','load_azs','load_gs','load_type_products','load_type_roofs'));
    }

    public function showGoodModal(){
        return datatables()->of(
           Good::query()->with('type', 'unit')->whereNotIn('type_id', [12])->where('is_check_price', 'not like', 1)
            // ->whereHas('type', function (Builder $query) {
            //     $query->where('is_product', 0);
            // })
        )->toJson();
    }

    public function showGoodPrice(){
        return datatables()->of(
            Good::query()->with('type', 'unit')->where('is_check_price', 'like', 1)->orderBy('updated_at', 'desc')
        )->toJson();
    }

    public function showGoodDetailBenefit(){

        return datatables()->of(
            GoodDetailBenefit::query()->orderBy('updated_at', 'desc')
        )->toJson();
    }

    public function checkOutGood(Request $req)
    {
        $this->validate($req,['id' => 'required']);
        $good_ids = $req->id;
        $warehouses = Warehouse::all();

        DB::beginTransaction();

        foreach($good_ids as $good_id) {
            $good = Good::find($good_id);
            $good->is_check_price = 1;
            $good->save();

            $checkGoodwarehouse = GoodWarehouse::where('good_id', $good_id);
            if($checkGoodwarehouse){
                $checkGoodwarehouse->delete();
            }
            foreach($warehouses as $warehouse) {
                $goodWarehouse = GoodWarehouse::where('good_id', $good_id)->first();
                $goodWarehouse = new GoodWarehouse;
                $goodWarehouse->good_id = $good_id;
                $goodWarehouse->warehouse_id = $warehouse->id;
                $goodWarehouse->save();
            }
        }

        DB::commit();
        return redirect()->route("whs-center.goods.set-price-goods.index")->with('status', 'เพิ่มข้อมูลใหม่เรียบร้อยแล้ว!');
    }

    public function showWarehouseByGood($good_id){
        $goodDetailBenefit = '';
        $good = Good::find($good_id);
        $goodWarehouses = GoodWarehouse::query()->where('good_id', $good_id)->with('warehouse')->get();

        return view('whs-center.goods.set-price-goods.base-price', compact('good', 'goodWarehouses' ,'goodDetailBenefit'));
    }

    public function showWarehouseByGoodDetailBenefit($good_detail_benefit_id){
        $good = '';
        $goodDetailBenefit = GoodDetailBenefit::find($good_detail_benefit_id);
        $goodWarehouses = GoodWarehouse::query()->where('good_detail_benefit_id', $good_detail_benefit_id)->with('warehouse')->get();

        return view('whs-center.goods.set-price-goods.base-price', compact('goodDetailBenefit', 'goodWarehouses','good'));
    }


    public function deleteGood(Request $req){

            $this->validate($req,['good_id' => 'required']);
            $good_id = $req->good_id;

            $good = Good::find($good_id);
            $good->is_check_price = 0;

            $goodWarehouses = GoodWarehouse::where('good_id', $good_id)->get();
            foreach ($goodWarehouses as $goodWarehouse) {

                $goodPrice = GoodPrice::where('good_warehouse_id', $goodWarehouse->id);
                if($goodPrice){
                    $goodPrice->delete();
                }
            }
            $goodWarehouses = GoodWarehouse::where('good_id', $good_id);

            if($good->save() && $goodWarehouses->delete()){
                $data = [
                    'title' => 'ลบสำเร็จ',
                    'msg' => 'ลบการตั้งค่าราคาสินค้าสำเร็จ',
                    'status' => 'success',
                ];
            }else{
                $data = [
                    'title' => 'เกิดข้อผิดพลาด',
                    'msg' => 'ลบการตั้งค่าราคาสินค้าไม่สำเร็จ',
                    'status' => 'error',
                ];
            }
            return $data;
    }

    public function setBasePrice(Request $req, $good_id) {

            $goodWarehouses = $req->goodWarehouses;

            DB::beginTransaction();

            foreach($goodWarehouses as $goodWarehouse_id => $value) {
                $data = GoodWarehouse::where('id', $goodWarehouse_id)->where('good_id',$good_id)->first();
                $data->base_price = $value;
                $data->save();
            }

            DB::commit();

            return redirect()->back()->with('status', 'บันทึกข้อมูลใหม่เรียบร้อยแล้ว!');
    }

    public function setGoodDetailBenefitBasePrice(Request $req, $good_detail_benefit_id) {

        $goodWarehouses = $req->goodWarehouses;

        DB::beginTransaction();

        foreach($goodWarehouses as $goodWarehouse_id => $value) {
            $data = GoodWarehouse::where('id', $goodWarehouse_id)->where('good_detail_benefit_id',$good_detail_benefit_id)->first();
            $data->base_price = $value;
            $data->save();
        }

        DB::commit();

        return redirect()->route("whs-center.goods.set-price-goods.showWarehouse", $good_detail_benefit_id )->with('status', 'บันทึกข้อมูลใหม่เรียบร้อยแล้ว!');
}

    public function infoGood(Request $req, $good_id){

        $good_id = $req->good_id;
        $good_warehouse_id = $req->good_warehouse_id;
        $warehouse_id = $req->warehouse_id;

        $mem_types = Member_type::all();
        $good = Good::find($good_id);
        $warehouse = Warehouse::find($warehouse_id);

        $good_price = [];

        foreach ($mem_types as $mem_type) {
            $goodPrice = GoodPrice::where(['good_warehouse_id' => $good_warehouse_id,'member_type_id' => $mem_type->id])->first();
            if($goodPrice){
                $data = [
                    'good' => $good,
                    'warehouse' => $warehouse,
                    'good_warehouse_id' => $good_warehouse_id,
                    'member_type_name' => $mem_type->name,
                    'member_type_id' => $mem_type->id,
                    'good_price_id' => $goodPrice->id,
                    'good_price' => $goodPrice->price,
                ];
            }else{
                $data = [
                    'good' => $good,
                    'warehouse' => $warehouse,
                    'good_warehouse_id' => $good_warehouse_id,
                    'member_type_name' => $mem_type->name,
                    'member_type_id' => $mem_type->id,
                    'good_price_id' => '',
                    'good_price' => '',
                ];
            }
            array_push($good_price,$data);
        }
        return $good_price;
    }

    public function infoGoodDetailBenefit(Request $req){

        $good_detail_benefit_id = $req->good_detail_benefit_id;
        $good_warehouse_id = $req->good_warehouse_id;
        $warehouse_id = $req->warehouse_id;

        $mem_types = Member_type::all();
        $goodDetailBenefit = GoodDetailBenefit::find($good_detail_benefit_id);
        $warehouse = Warehouse::find($warehouse_id);

        $good_price = [];

        foreach ($mem_types as $mem_type) {
            $goodPrice = GoodPrice::where(['good_warehouse_id' => $good_warehouse_id,'member_type_id' => $mem_type->id])->first();
            if($goodPrice){
                $data = [
                    'good' => $goodDetailBenefit,
                    'warehouse' => $warehouse,
                    'good_warehouse_id' => $good_warehouse_id,
                    'member_type_name' => $mem_type->name,
                    'member_type_id' => $mem_type->id,
                    'good_price_id' => $goodPrice->id,
                    'good_price' => $goodPrice->price,
                ];
            }else{
                $data = [
                    'good' => $goodDetailBenefit,
                    'warehouse' => $warehouse,
                    'good_warehouse_id' => $good_warehouse_id,
                    'member_type_name' => $mem_type->name,
                    'member_type_id' => $mem_type->id,
                    'good_price_id' => '',
                    'good_price' => '',
                ];
            }
            array_push($good_price,$data);
        }
        return $good_price;
    }


    public function setPrice(Request $req)
    {

        $good_warehouse_id = $req->good_warehouse_id;
        $good_prices = $req->good_prices;
        $check_good_price = GoodPrice::where('good_warehouse_id', $good_warehouse_id)->first();
        if ($check_good_price) {
            $check_good_price = GoodPrice::where('good_warehouse_id', $good_warehouse_id);
            $check_good_price->delete();
        }

        $check = false;
        DB::beginTransaction();
        foreach ($good_prices as $good_price) {
            $goodPrice = GoodPrice::where('good_warehouse_id', $good_warehouse_id)->where('member_type_id', $good_price['member_type_id'])->first();
            $goodPrice = new GoodPrice;
            $goodPrice->good_warehouse_id = $good_warehouse_id ;
            $goodPrice->member_type_id = $good_price['member_type_id'];
            $goodPrice->price = $good_price['price'];
            if($goodPrice->save()){
                $check = true;
            }else{
                $check = false;
            }
        }
        DB::commit();
        if($check){
            $data = [
                'title' => 'บันทึกสำเร็จ',
                'msg' => 'บันทึกราคาสินค้าสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'บันทึกราคาสินค้าสำเร็จไม่สำเร็จ',
                'status' => 'error',
            ];
        }

        return $data;
    }

    public function storeGoodDetailBenefit(Request $req)
    {

        $type_coil = $req->load_type_coil;
        $girth = $req->load_girth;
        $g = $req->load_g;
        $az = $req->load_az;
        $thick = $req->load_thick;
        $color = $req->load_color;
        $type_product = $req->load_type_product;
        $type_roof = $req->load_type_roof;
        $check1 = false;
        $check2 = false;

        $checkGoodDetailBenefit = GoodDetailBenefit::where('type_coil', $type_coil)
            ->where('girth', $girth)
            ->where('g', $g)
            ->where('az', $az)
            ->where('thick', $thick)
            ->where('color', $color)
            ->where('type_product', $type_product)
            ->where('type_roof', $type_roof)
            ->first();

        if($checkGoodDetailBenefit){
            return redirect()->route("whs-center.goods.set-price-goods.index")->withErrors('มีข้อมูลนี้อยู่แล้ว!');
        }else{
            $goodDetailBenefit = new GoodDetailBenefit();
            $goodDetailBenefit->type_coil = $type_coil;
            $goodDetailBenefit->girth = $girth;
            $goodDetailBenefit->g = $g;
            $goodDetailBenefit->az = $az;
            $goodDetailBenefit->thick = $thick;
            $goodDetailBenefit->color = $color;
            $goodDetailBenefit->type_product = $type_product;
            $goodDetailBenefit->type_roof = $type_roof;
            if($goodDetailBenefit->save()){
                $check1 = true;
            }
        }

        $goodDetailBenefit = GoodDetailBenefit::where('type_coil', $type_coil)
            ->where('girth', $girth)
            ->where('g', $g)
            ->where('az', $az)
            ->where('thick', $thick)
            ->where('color', $color)
            ->where('type_product', $type_product)
            ->where('type_roof', $type_roof)
            ->first();

        $good_detail_benefit_id = $goodDetailBenefit->id;
        $warehouses = Warehouse::all();

        foreach($warehouses as $warehouse) {
            $goodWarehouse = new GoodWarehouse;
            $goodWarehouse->good_detail_benefit_id = $good_detail_benefit_id;
            $goodWarehouse->warehouse_id = $warehouse->id;
            if($goodWarehouse->save()){
                $check2 = true;
            }

        }

        if($check1 && $check2){
            return redirect()->route("whs-center.goods.set-price-goods.index")->with('status', 'เพิ่มข้อมูลสำเร็จ!');
        }else{
            return redirect()->route("whs-center.goods.set-price-goods.index")->with('status', 'เพิ่มข้อมูลไม่สำเร็จ!');
        }

    }

    public function deleteGoodDetailBenefit(Request $req){

        $this->validate($req,['good_detail_benefit_id' => 'required']);
        $good_detail_benefit_id = $req->good_detail_benefit_id;

        $goodDetailBenefit = GoodDetailBenefit::find($good_detail_benefit_id);

        $goodWarehouses = GoodWarehouse::where('good_detail_benefit_id', $good_detail_benefit_id)->get();
        foreach ($goodWarehouses as $goodWarehouse) {

            $goodPrice = GoodPrice::where('good_warehouse_id', $goodWarehouse->id);
            if($goodPrice){
                $goodPrice->delete();
            }
        }
        $goodWarehouses = GoodWarehouse::where('good_detail_benefit_id', $good_detail_benefit_id);

        if($goodDetailBenefit->delete() && $goodWarehouses->delete()){
            $data = [
                'title' => 'ลบสำเร็จ',
                'msg' => 'ลบการตั้งค่าราคาสินค้าสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ลบการตั้งค่าราคาสินค้าไม่สำเร็จ',
                'status' => 'error',
            ];
        }
        return $data;
    }
}
