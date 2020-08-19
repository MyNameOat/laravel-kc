<?php
namespace App\Http\Controllers\WhsCenter;

use App\Http\Controllers\Controller;
use App\Good;
use App\GoodRatio;
use App\Member_type;
use App\GoodDetailPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetRatioGoodController extends Controller
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

        return view('whs-center.goods.set-ratio-goods.index', compact('load_type_coils','load_girths','load_thicks','load_colors','load_azs','load_gs','load_type_products','load_type_roofs'));
    }

    public function showGoodModal(){
        return datatables()->of(
            Good::query()->with('type', 'unit')->whereNotIn('type_id', [12])->where('is_check_ratio', 'not like', 1)
            // ->whereHas('type', function (Builder $query) {
            //     $query->where('is_product', 0);
            // })
        )->toJson();
    }

    public function showGoodRatio(){
        return datatables()->of(
            GoodRatio::query()->with('good', 'good.unit', 'good.type')
            ->whereNotNull('good_id')
            ->orderBy('updated_at', 'desc')
        )->toJson();
    }

    public function showGoodDetailPoint(){
        return datatables()->of(
            GoodRatio::query()->with('goodDetailPoint')->whereNotNull('good_detail_point_id')->orderBy('updated_at', 'desc')
        )->toJson();
    }

    public function storeGoodRatio(Request $req){

        $this->validate($req,['id' => 'required', 'item_id' => 'required', 'good_ratio' => 'required', 'is_good_id' => 'required',]);
        $check = false;

        $good_ratio = GoodRatio::find($req->id);
        if($good_ratio){
            if($good_ratio->delete()){
                $check = true;
            }else{
                $check = false;
            }
        }

        $good_ratio = new GoodRatio;
        if($req->is_good_id == 1){
            $good_ratio->good_id = $req->item_id;
            $good_ratio->ratio = $req->good_ratio;
            $good_ratio->save();
        }else if($req->is_good_id == 0){
            $good_ratio->good_detail_point_id = $req->item_id;
            $good_ratio->ratio = $req->good_ratio;
            $good_ratio->save();
        }else{
            $check = false;
        }

        if($check){
            $data = [
                'title' => 'บันทึกสำเร็จ',
                'msg' => 'บันทึกแต้มสินค้าสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'บันทึกแต้มสินค้าไม่สำเร็จ',
                'status' => 'error',
            ];
        }
       return $data;
    }


    public function deleteGoodRatio(Request $req){
        $this->validate($req,['id' => 'required', 'item_id' => 'required', 'is_good_id' => 'required',]);
        $check = false;


        $good_ratio = GoodRatio::find($req->id);
        if($req->is_good_id == 1){
            $good = Good::find($req->item_id);
            $good->is_check_ratio = 0;
            if($good->save() && $good_ratio->delete()){ $check = true;}

        }else if($req->is_good_id == 0){
            $good_detail_point = GoodDetailPoint::find($req->item_id);
            if($good_detail_point->delete() && $good_ratio->delete()){ $check = true;}
        }

        if($check){
            $data = [
                'title' => 'ลบสำเร็จ',
                'msg' => 'ลบแต้มสินค้าสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ลบแต้มสินค้าไม่สำเร็จ',
                'status' => 'error',
            ];
        }
        return $data;
    }

    public function checkOutGood(Request $req)
    {
        $this->validate($req,['id' => 'required']);
        DB::beginTransaction();
        foreach($req->id as $value) {
            $good = Good::find($value);
            $good->is_check_ratio = 1;
            $good->save();

            if(GoodRatio::find($value)){
                $good_ratio = GoodRatio::find($value);
                $good_ratio->ratio = 0;
                $good_ratio->save();
            }else{
                $good_ratio = new GoodRatio;
                $good_ratio->good_id = $value;
                $good_ratio->ratio = 0;
                $good_ratio->save();
            }
        }
        DB::commit();
        return redirect()->route("whs-center.goods.set-ratio-goods.index");
    }



    public function storeGoodDetailPoint(Request $req)
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

        $checkGoodDetailPoint = GoodDetailPoint::where('type_coil', $type_coil)
            ->where('girth', $girth)
            ->where('g', $g)
            ->where('az', $az)
            ->where('thick', $thick)
            ->where('color', $color)
            ->where('type_product', $type_product)
            ->where('type_roof', $type_roof)
            ->first();

        if($checkGoodDetailPoint){
            return redirect()->route("whs-center.goods.set-ratio-goods.index")->withErrors('มีข้อมูลนี้อยู่แล้ว!');
        }else{
            $goodDetailPoint = new GoodDetailPoint();
            $goodDetailPoint->type_coil = $type_coil;
            $goodDetailPoint->girth = $girth;
            $goodDetailPoint->g = $g;
            $goodDetailPoint->az = $az;
            $goodDetailPoint->thick = $thick;
            $goodDetailPoint->color = $color;
            $goodDetailPoint->type_product = $type_product;
            $goodDetailPoint->type_roof = $type_roof;
            if($goodDetailPoint->save()){
                $check1 = true;
            }
        }

        $goodDetailPoint = GoodDetailPoint::where('type_coil', $type_coil)
            ->where('girth', $girth)
            ->where('g', $g)
            ->where('az', $az)
            ->where('thick', $thick)
            ->where('color', $color)
            ->where('type_product', $type_product)
            ->where('type_roof', $type_roof)
            ->first();

            $good_ratio = new GoodRatio;
            $good_ratio->good_detail_point_id = $goodDetailPoint->id;
            $good_ratio->ratio = 0;
            if($good_ratio->save()){
                $check2 = true;
            }


        if($check1 && $check2){
            return redirect()->route("whs-center.goods.set-ratio-goods.index")->with('status', 'เพิ่มข้อมูลสำเร็จ!');
        }else{
            return redirect()->route("whs-center.goods.set-ratio-goods.index")->with('status', 'เพิ่มข้อมูลไม่สำเร็จ!');
        }

    }

    public function showMemberType(){
        return datatables()->of(
            Member_type::query()
        )->toJson();;
    }


    public function setBaseRatioMemType(Request $req)
    {

        $this->validate($req,['ratio' => 'required', 'id' => 'required', 'isCalCulatePoint' => 'required']);

        $ratio = $req->ratio;
        $id = $req->id;
        $isCalCulatePoint = $req->isCalCulatePoint;

        $memberType = Member_type::find($id);
        $memberType->ratio = $ratio;
        $memberType->is_calculate_point = $isCalCulatePoint;

        if($memberType->save()){
            $data = [
                'title' => 'บันทึกสำเร็จ',
                'msg' => 'บันทึกอัตราส่วนแต้มสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'บันทึกอัตราส่วนแต้มไม่สำเร็จ',
                'status' => 'error',
            ];
        }

        return $data;

    }


}
