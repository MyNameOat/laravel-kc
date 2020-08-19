<?php

namespace App\Http\Controllers\WhsCenter;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Customer;
use App\Member;
use App\Member_type;
use App\Bank;
use App\Warehouse;
use App\HS;
use App\MemberPointBenefit;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Exports\MemberSummaryExport;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    public function index (){
        return view('whs-center.members.set-members.index');
    }

    public function showCustomer(){
        return datatables()->of(
            Customer::where('member_id',null)
        )->toJson();
    }

    public function showMember(){
        return datatables()->of(
            Member::query()->with('customer', 'member_type')->orderBy('updated_at', 'desc')
        )->toJson();
    }

    public function checkOutCustomer(Request $req){

        $this->validate($req,['id' => 'required']);
        $customer_id = $req->id;

        DB::beginTransaction();

        foreach($customer_id as $value) {
            $member = new Member;
            $member->status = 1;
            $member->save();

            $customer = Customer::find($value);
            $customer->member_id = $member->id;
            $customer->save();
        }
        DB::commit();

        return redirect()->route("whs-center.members.set-members.index");

   }

    public function saveMember(Request $req){

        $this->validate($req,['member_id' => 'required', 'memberCode' => 'required', 'memberType' => 'required']);

        $checkCode = Member::where('code',$req->memberCode)->where('id','!=',$req->member_id)->first();
        if($checkCode){
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'รหัสสมาชิกซ้ำกับสมาชิกอื่น กรุณาสุ่มใหม่',
                'status' => 'error',
            ];
        }else{
            if($req->idCard){
                $customer = Customer::where('member_id' ,$req->member_id)->first();
                $customer->vat_code = $req->idCard;
                $customer->save();
            }

            $member = Member::find($req->member_id);
            $member->code = $req->memberCode;
            $member->member_type_id = $req->memberType;
            $member->status = 2;
            $member->bank_id = $req->bankName;
            $member->bank_account_number = $req->bankAccountNumber;
            if($member->save()){
                $data = [
                    'title' => 'บันทึกสำเร็จ',
                    'msg' => 'บันทึกข้อมูลสมาชิกสำเร็จ',
                    'status' => 'success',
                ];
            }else{
                $data = [
                    'title' => 'เกิดข้อผิดพลาด',
                    'msg' => 'บันทึกไม่สำเร็จ',
                    'status' => 'error',
                ];
            }
        }
        return $data;
    }

    public function uploadAvatar(Request $req){

        $this->validate($req,['member_id' => 'required','inpufile' => 'mimes:jpeg,jpg,png,gif|max:20000']);
        $member_id = $req->member_id;
        $picName = 'mem'.$member_id;
        if($req->hasFile('inpufile'))
        {
            $avatar = $req->file('inpufile');
            $filename = 'avatar'.date("Ymd",time()).'.'.$avatar->getClientOriginalExtension();

            if($avatar->storeAs('public/image/member/'.$picName, $filename)){
                $member = Member::find($member_id);
                $member->avatar = $picName.'/'.$filename;
                if($member->save()){
                    return redirect()->back()->with('status', 'บันทึกข้อมูลรูปภาพใหม่เรียบร้อยแล้ว!');
                }
            }
            return redirect()->back()->with('status', 'บันทึกข้อมูลรูปภาพไม่สำเร็จ!');
        }else{
            return redirect()->back()->with('status', 'บันทึกข้อมูลรูปภาพไม่สำเร็จ!');
        }
        return redirect()->back()->with('status', 'มีบางอย่างผิดพลาดกรุณาติดต่อเจ้าหน้าที่!');
    }

    public function uploadFile(Request $req){

        $this->validate($req,['member_id' => 'required','inpufile' => 'mimes:jpeg,jpg,png,gif|max:2048']);
        $member_id = $req->member_id;
        $dirName = 'mem'.$member_id;
        $member = Member::find($member_id);

        if($req->hasFile('idCardPic')){
            $image = $req->file('idCardPic');
            $filename = 'idCard'.date("Ymd",time()).'.'.$image->getClientOriginalExtension();
            $member->id_card = $dirName.'/'.$filename;

        }else if($req->hasFile('houseRegisPic')) {
            $image = $req->file('houseRegisPic');
            $filename = 'houseRegis'.date("Ymd",time()).'.'.$image->getClientOriginalExtension();
            $member->house_registration = $dirName.'/'.$filename;

        }else if($req->hasFile('bookBankPic')){
            $image = $req->file('bookBankPic');
            $filename = 'bookBank'.date("Ymd",time()).'.'.$image->getClientOriginalExtension();
            $member->book_bank = $dirName.'/'.$filename;

        }else if($req->hasFile('etc1Pic')){
            $image = $req->file('etc1Pic');
            $filename = 'etc1'.date("Ymd",time()).'.'.$image->getClientOriginalExtension();
            $member->etc1 = $dirName.'/'.$filename;

        }else if($req->hasFile('etc2Pic')){
            $image = $req->file('etc2Pic');
            $filename = 'etc2'.date("Ymd",time()).'.'.$image->getClientOriginalExtension();
            $member->etc2 = $dirName.'/'.$filename;
        }else{
            return redirect()->back()->with('status', 'บันทึกข้อมูลรูปภาพไม่สำเร็จ!');
        }

        if($image->storeAs('public/image/member/'.$dirName, $filename)){
            if($member->save()){
                return redirect()->back()->with('status', 'บันทึกข้อมูลรูปภาพใหม่เรียบร้อยแล้ว!');
            }
        }

        return redirect()->back()->with('status', 'มีบางอย่างผิดพลาดกรุณาติดต่อเจ้าหน้าที่!');
    }

    function destroyMember(Request $req){

        $this->validate($req,['member_id' => 'required']);

        $member_id = $req->member_id;

        $member = Member::find($member_id);
        $customer = Customer::where('member_id', $member_id)->first();
        $customer->member_id = null;

        if($customer->save() && $member->delete()){
            $data = [
                'title' => 'ลบสำเร็จ!',
                'msg' => 'ลบข้อมูลสมาชิกสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title' => 'เกิดข้อผิดพลาด',
                'msg' => 'ลบไม่สำเร็จ',
                'status' => 'error',
            ];
        }

        return $data;
    }

    public function checkMember(Request $req){
        $member = Member::find($req->member_id);
        $member_type = Member_type::find($member->member_type_id);
        $customer = Customer::where('member_id', $req->member_id)->first();
        $bank = Bank::find($member->bank_id);
        $data = [
            'member' => $member,
            'member_type' => $member_type,
            'customer' => $customer,
            'bank' => $bank,
        ];
        return $data;
    }

    public function showMemberType(){
        return Member_type::get();
    }

    public function showBank(){
        return Bank::get();
    }

    public function randomCode(){
        $code = 'MEM'.rand(123456,654321);
        return $code;
    }

    public function showWarehouse(){
        return Warehouse::get();
    }

    public function showProfile($member_id){

        $member = Member::where('id',$member_id)->with('customer', 'member_type', 'bank')->first();

        if($member == null){
            return abort(404);
        }

        $results = HS::with('customer.member', 'memberPointBenefit')
            ->where('customer_id', $member->customer->id)
            ->orderBy('id', 'desc')
            ->get();

        $totalPoint = 0;
        $totalAmount = 0;
        $totalBenefit = 0;
        foreach ($results as $result ) {
            $totalAmount = $totalAmount+$result->total_amount;
            if($result->memberPointBenefit){
                foreach ($result->memberPointBenefit as $value) {
                    $totalPoint = $totalPoint+$value->point;
                    $totalBenefit = $totalBenefit+$value->benefit;
                }
                $totalPoint = bcadd(0,$totalPoint,0);
            }
        }
        $totalPoint = number_format($totalPoint);
        $totalBenefit = number_format($totalBenefit,2);
        $totalAmount = number_format($totalAmount,2);//total paid all bill

        return view('whs-center.members.set-members.profile', compact('member','totalPoint','totalAmount', 'totalBenefit'));
    }

    public function showMemberSummaryExcel (Request $req){

        $memberTypeId = $req->input('memberTypeId');
        $warehouseId = $req->input('warehouseId');
        $startDate = $req->input('startDate');
        $endDate = $req->input('endDate');
        $startSeason = $req->input('startSeason');
        $endSeason = $req->input('endSeason');
        $btnExport = $req->input('btnExport');

        $memberType = Member_type::find($memberTypeId)->name;

        if( $warehouseId == 'all'){
            $warehouses = Warehouse::all();
        }else{
            $warehouses = Warehouse::where('id', $warehouseId)->get();
        }

        $startDate = date("Y-m-d", strtotime($startDate));
        $endDate =  date("Y-m-d", strtotime($endDate));
        $startSeason = date("Y-m-d", strtotime($startSeason));
        $endSeason = date("Y-m-d", strtotime($endSeason));
        $bigDatas = [];

        foreach($warehouses as $warehouse){

            $warehouseId = $warehouse->id;
            $tables = [];

            $thead = array(
                'startDate' => date("d/m/Y", strtotime($startDate)),
                'endDate' => date("d/m/Y", strtotime($endDate)),
                'startSeason' => date("d/m/Y", strtotime($startSeason)),
                'endSeason' => date("d/m/Y", strtotime($endSeason)),
                'warehouse' => $warehouse->name,
                'memberType' => $memberType,
            );

            $tbodies = array();
            $count = 1;
            $bank = '';

            $sumBenefitSelect = 0;
            $sumPointSelect = 0;
            $sumBalanceSelect = 0;
            $sumBenefitAll = 0;
            $sumPointAll = 0;
            $sumBalanceAll = 0;

            $results = Member::with('customer', 'member_type', 'bank')
                ->where('member_type_id', $memberTypeId)
                ->whereHas('customer', function (Builder $query) use ($warehouseId) {
                    $query->where('warehouse_id', $warehouseId);
                })
                ->get();


            foreach ($results as $result) {

                $totalPointSelect = 0;
                $totalBenefitSelect = 0;
                $totalBalanceSelect = 0;

                $totalPointAll = 0;
                $totalBenefitAll = 0;
                $totalBalanceAll = 0;

                $member_id = $result->id;

                if($result->bank){
                    $bank = $result->bank->name;
                }

                $memberPointBenefits = MemberPointBenefit::with('hs')
                    ->where('member_id', $member_id)
                    ->groupBy('h_s_id')
                    ->selectRaw('sum(point) as sumPoint, h_s_id')
                    ->selectRaw('sum(benefit) as sumBenefit, h_s_id')
                    ->get();
                //return $memberPointBenefits;

                foreach ($memberPointBenefits as $memberPointBenefit ) {

                    if($memberPointBenefit->hs){

                        $doc_date = date('Y-m-d', strtotime($memberPointBenefit->hs->doc_date));

                        if($doc_date >= $startSeason && $doc_date <= $endSeason){
                            $totalBalanceAll = $totalBalanceAll + $memberPointBenefit->hs->after_discount;
                            $totalPointAll = $totalPointAll+bcadd(0,$memberPointBenefit->sumPoint,0);
                            $totalBenefitAll = $totalBenefitAll + $memberPointBenefit->sumBenefit;
                        }
                        if($doc_date >= $startDate && $doc_date <= $endDate){
                            $totalBalanceSelect = $totalBalanceSelect + $memberPointBenefit->hs->after_discount;
                            $totalPointSelect = $totalPointSelect+bcadd(0,$memberPointBenefit->sumPoint,0);
                            $totalBenefitSelect = $totalBenefitSelect + $memberPointBenefit->sumBenefit;
                        }

                    }

                }

                $data = array(
                    'no' => $count,
                    'member_code' => $result->code,
                    'member_type' => $result->member_type->name,
                    'name' => $result->customer->name,
                    'vat_code' => $result->customer->vat_code,
                    'bank' => $bank,
                    'bank_account_number' => $result->bank_account_number,
                    'total_benefit_select' => number_format($totalBenefitSelect, 2),
                    'total_balance_select' => number_format($totalBalanceSelect, 2),
                    'total_point_select' => number_format($totalPointSelect, 2),
                    'total_benefit_all' => number_format($totalBenefitAll, 2),
                    'total_balance_all' => number_format($totalBalanceAll, 2),
                    'total_point_all' => number_format($totalPointAll, 2),
                );
                array_push($tbodies,$data);
                $count++;

                $sumBenefitSelect = $sumBenefitSelect + $totalBenefitSelect;
                $sumBalanceSelect = $sumBalanceSelect + $totalBalanceSelect;
                $sumPointSelect = $sumPointSelect + $totalPointSelect;
                $sumBenefitAll = $sumBenefitAll + $totalBenefitAll;
                $sumBalanceAll = $sumBalanceAll + $totalBalanceAll;
                $sumPointAll = $sumPointAll + $totalPointAll;

            }
            $tfoot = [
                'sum_benefit_select' => number_format($sumBenefitSelect, 2),
                'sum_balance_select' => number_format($sumBalanceSelect, 2),
                'sum_point_select' => number_format($sumPointSelect, 2),
                'sum_benefit_all' => number_format($sumBenefitAll, 2),
                'sum_balance_all' => number_format($sumBalanceAll, 2),
                'sum_point_all' => number_format($sumPointAll, 2),
            ];

            $tables = [
                'thead' =>  $thead,
                'tbodies' => $tbodies,
                'tfoot' => $tfoot,
            ];
            array_push($bigDatas,$tables);
        }

        //return $bigDatas;

        if($btnExport == 0){
            return view('whs-center.members.set-members.show-excel', compact('bigDatas'));
        }else if($btnExport == 1){
            return Excel::download(new MemberSummaryExport($bigDatas), 'MemberSummary.xlsx');
        }else{
            return abort(404);
        }

    }

    public function exportMemberSummaryExcel(Request $req){

        $bigDatas = json_decode($req->bigDatas, TRUE);

        return Excel::download(new MemberSummaryExport($bigDatas), 'MemberSummary.xlsx');
    }

    public function showPointBenefitDetail(Request $req, $member_id){

        $from = date($req->get('filter_start'));
        $to =  date($req->get('filter_end'));

        $member = Member::where('id',$member_id)->with('customer', 'member_type')->first();

        if($from == '' && $to == '' || $from == null && $to == null ){
            $results = HS::with('customer.member', 'memberPointBenefit')
            ->where('customer_id', $member->customer->id)
            ->orderBy('id', 'desc')
            ->get();

        }else{
            $results = HS::with('customer.member', 'memberPointBenefit')
            ->where('customer_id', $member->customer->id)
            ->whereDate('doc_date', '>=', $from)
            ->whereDate('doc_date', '<=', $to)
            ->orderBy('id', 'desc')
            ->get();

        }

            $totalBenefit = 0;
            $totalPoint = 0;

            $totalAmount = 0;
            $resutls_array = array();

            $benefitAllBill = 0;
            $pointAllBill = 0;

            foreach ($results as $result ) {
                $totalAmount = $totalAmount+$result->after_discount;
                if($result->memberPointBenefit){
                    foreach ($result->memberPointBenefit as $value) {
                        $totalBenefit = $totalBenefit+$value->benefit;
                        $totalPoint = $totalPoint+$value->point;
                    }
                    $data = array(
                        'hs' => $result,
                        'totalBenefit' => $totalBenefit,
                        'totalPoint' => bcadd(0,$totalPoint,0),
                    );
                }else{
                    $totalBenefit = 0;
                    $totalPoint = 0;
                    $data = array(
                        'hs' => $result,
                        'totalBenefit' => $totalBenefit,
                        'totalPoint' => bcadd(0,$totalPoint,0),
                    );
                }
                array_push($resutls_array,$data);
                $benefitAllBill = $benefitAllBill + $totalBenefit;
                $pointAllBill = $pointAllBill + bcadd(0,$totalPoint,0);
                $totalBenefit = 0;
                $totalPoint = 0;
            }
            // return $resutls_array;
            $pointAllBill = number_format($pointAllBill);
            $benefitAllBill = number_format($benefitAllBill, 2);
            $totalAmount = number_format($totalAmount, 2);

        return view('whs-center.members.set-members.point-benefit-detail', compact('member','benefitAllBill','pointAllBill','totalAmount','resutls_array'));
    }

    public function showHsBillPointBenefit($member_id, $h_s_id){

        $customer = Customer::with('member.member_type','customerBillAddress')->where('member_id',$member_id)->first();

        $results = MemberPointBenefit::with('good.unit','hs.warehouse','hsSku.informationReceiptSku','hsProduct.informationReceiptProduct', 'hsDoor.informationReceiptDoor', 'hsService.informationReceiptService')
            ->where('member_id',$member_id)
            ->where('h_s_id',$h_s_id)
            ->get();

        //return $results;
        if($results == '[]'){
            return abort(404);
        }

         //decare var for recieve data from database
         $hs_id = '';
         $hs_code = '';
         $hs_date = '';
         $warehouse = '';
         $amount = '';
         $price_unit = 0;
         $total_price = 0;
         $base_price = 0;
         $totalPriceInBill = 0;
         $length = 1;

         $bodies = array();
         $count = 0;
         $good_name = '';
         $good_unit = '';
         $good_code = '';

         $sumPoint = 0;
         $sumBenefit = 0;

         $area = 5;
         try {

            foreach ($results as $result ) {

                $hs_id = $result->hs->id;
                $hs_code = $result->hs->code;
                $hs_date = $result->hs->updated_at;
                $warehouse = $result->hs->warehouse->name;

                if($result->hsSku){
                    $amount = $result->hsSku->amount;
                    $price_unit = $result->hsSku->price_unit;
                    $total_price = $result->hsSku->informationReceiptSku->total_price;
                }else if($result->hsProduct){
                    $amount = $result->hsProduct->amount;
                    $price_unit = $result->hsProduct->price_unit;
                    $total_price = $result->hsProduct->informationReceiptProduct->total_price;
                    if($result->hsProduct->informationReceiptProduct->length != null && $result->hsProduct->informationReceiptProduct->length != 0){
                        $length = $result->hsProduct->informationReceiptProduct->length;
                        $price_unit = $price_unit/$length;
                    }
                }else if($result->hsDoor){
                    $amount = $result->hsDoor->amount;
                    $price_unit = $result->hsDoor->informationReceiptDoor->price_thick;
                    $total_price = $result->hsDoor->informationReceiptDoor->total_price;

                    if($result->hsDoor->informationReceiptDoor->area != null && $result->hsDoor->informationReceiptDoor->area != 0){
                        if($result->hsDoor->informationReceiptDoor->area > 5){
                            $area = $result->hsDoor->informationReceiptDoor->area;
                        }
                    }

                }else if($result->hsService){
                    $amount = $result->hsService->amount;
                    $price_unit = $result->hsService->price_unit;
                    $total_price = $result->hsService->total_price;
                    $good_name = $result->hsService->detail;
                    $good_code = '';
                    $good_unit = $result->hsService->unit;

                }else{
                    return abort(500);
                }

                $difference = 0;
                $totalPriceInBill = $totalPriceInBill+$total_price;
                $sumBenefit = $sumBenefit+$result->benefit;
                $sumPoint = $sumPoint+$result->point;

                if ($result->benefit != 0){
                    $difference = $result->benefit/$amount;
                    if($length != null || $length != 0){
                        $difference = $difference/$length;
                    }
                }

                if($result->hsDoor && $result->benefit > 0){
                    $difference = $result->benefit/$amount/$area;
                }

                $count ++ ;

                if($result->good){
                    $good_name = $result->good->name;
                    $good_code = $result->good->code;
                    $good_unit = $result->good->unit->name;
                }

                $data = array(
                    'no' => $count,
                    'good_name' => $good_name,
                    'good_code' =>  $good_code,
                    'good_unit' => $good_unit,
                    'amount' =>  bcadd(0,$amount,0),
                    'base_price' => number_format($result->base_price, 2),
                    'sale_price' =>  number_format($price_unit, 2),
                    'member_price' => number_format($result->price, 2),
                    'total_price' =>  number_format($total_price, 2),
                    'difference' => number_format($difference, 2),
                    'benefit' => number_format($result->benefit, 2),
                    'ratio' => number_format($result->ratio, 2),
                    'point' => number_format($result->point, 2),
                );

                array_push($bodies,$data);
            }
            $hs_date = date("d/m/Y", strtotime($hs_date));
            $header = [
                'hs_id' => $hs_id,
                'hs_code' => $hs_code,
                'hs_date' => $hs_date,
                'warehouse' => $warehouse,
            ];

            $discount = number_format($result->hs->discount_all, 2);
            $netPrice = number_format($result->hs->after_discount, 2);
            $pointInBill = bcadd(0,$sumPoint,0);

            $footer = [
                'totalPriceInBill' => number_format($totalPriceInBill,2),
                'sumBenefit' => number_format($sumBenefit ,2),
                'sumPoint' => $sumPoint,
                'pointInBill' => $pointInBill,
                'discount' => $discount,
                'netPrice' => $netPrice,
            ];

        //} catch (\Throwable $th) {
            //return abort(404);
        }catch (\Exception $e){
            return $e;
        }

        return view('whs-center.members.set-members.hs-bill-point-benefit', compact('member_id','customer','header', 'bodies', 'footer'));

    }


}
