<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HSSku;
use App\HSProduct;
use App\HSDoor;
use App\HSService;
use App\MemberPointBenefit;
use App\GoodDetail;
use App\Good;
use App\GoodWarehouse;
use App\WorkerLog;
use App\GoodDetailBenefit;
use App\GoodDetailPoint;

class CalculateBenefit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:calculate-benefit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->calculatePointBenefitHSSku();
            $this->calculatePointBenefitHSProduct();
            $this->calculatePointBenefitHSDoor();
            $this->calculatePointBenefitHSService();
            $workerLog = new WorkerLog;
            $workerLog->name = 'calculate-point-benefit';
            $workerLog->status = 'Running';
            $workerLog->description = 'calculate member point benefit - run every minute';
            $workerLog->save();
          } catch (\Exception $e) {
            $workerLog = new WorkerLog;
            $workerLog->name = 'calculate-point-benefit';
            $workerLog->status = 'Error';
            $workerLog->description = $e->getMessage();
            $workerLog->save();
          }

    }

    private function calculatePointBenefitHSSku(){

        $h_s_sku_id = '';
        $h_s_id = '';
        $warehouse_id = '';
        $baseRatio = 0;

        $results = HSSku::
            with('good.goodRatio', 'informationReceiptSku.informationReceipt.customer.member.member_type')
            ->where('is_calculate_benefit', '=', 0)
            ->orderBy('id')
            ->limit(60)
            ->get();

        foreach ($results as $result) {
            $h_s_sku_id = $result->id;
            $h_s_id = $result->h_s_id;
            $good_id = $result->good_id;
            $total_price = $result->informationReceiptSku->total_price;
            $warehouse_id = $result->informationReceiptSku->informationReceipt->warehouse_id;

            $member_status = '';
            $member_id = '';
            $member_type_id = '';

            $hsSkuPrice = $result->price_unit;
            $amount = $result->amount;
            $base_price = 0;
            $memberPrice = 0;
            $benefit = 0;
            $point = 0;
            $ratio = null;

            if($result->informationReceiptSku->informationReceipt->customer && $result->informationReceiptSku->informationReceipt->customer->member){ //if is a member!

                $member_status = $result->informationReceiptSku->informationReceipt->customer->member->status;
                $member_id = $result->informationReceiptSku->informationReceipt->customer->member->id;
                $member_type_id = $result->informationReceiptSku->informationReceipt->customer->member->member_type_id;
                $baseRatio = $result->informationReceiptSku->informationReceipt->customer->member->member_type->ratio;
                $member_calculate_point = $result->informationReceiptSku->informationReceipt->customer->member->member_type->is_calculate_point;
                $amount = $result->amount;

                if($member_status == 2){// if member status is 2 -> calculate benefit

                    if($result->good && $result->good->is_check_price == 1){// if good have setting price

                        $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                            ->where('warehouse_id', $warehouse_id)
                            ->where('good_id', $good_id)
                            ->first();

                        $base_price = $good_warehouse->base_price;

                        if($good_warehouse->goodPrice){
                            $goodPrices = $good_warehouse->goodPrice;
                           foreach ($goodPrices as $goodPrice) {
                               if($goodPrice->member_type_id == $member_type_id){
                                    $memberPrice = $goodPrice->price;
                                    if($memberPrice > 0){
                                        $benefit = $this->calBenefit($hsSkuPrice, $base_price, $memberPrice);
                                    }
                               }
                           }
                        }

                        $benefit = $benefit * $amount;
                    }

                    if($benefit > 0){ //if benefit > 0 --> total_price = total_price - benefit
                        echo '<br> OLD TOTAL PRICE = '.$total_price.'<br>';
                        $total_price = $total_price - $benefit;
                        echo 'NEW TOTAL PRICE = '.$total_price.'<br>';
                    }

                    $ratio = $baseRatio;
                    if($member_calculate_point == 1){
                        if($result->good && $result->good->is_check_ratio == 1){// check calculate point
                            if($result->good->goodRatio){// if good have good_ratio (handle null)
                                $ratio = $result->good->goodRatio->ratio;
                            }
                            $point = $this->calPoint($ratio, $total_price);
                        }else{
                            $point = $this->calPoint($ratio, $total_price);
                        }

                    }
                    if($memberPrice != null || $memberPrice != 0){
                        if($hsSkuPrice < $memberPrice){
                            $point = 0;
                        }
                    }

                    $memberPointBenefit = MemberPointBenefit::with('hs')->where('h_s_sku_id', $h_s_sku_id)->where('h_s_id', $h_s_id)->where('good_id', $good_id)->first();

                    if($memberPointBenefit == null || $memberPointBenefit == ''){
                        $memberPointBenefit = new MemberPointBenefit;
                    }
                    $memberPointBenefit->h_s_id = $h_s_id;
                    $memberPointBenefit->h_s_sku_id = $h_s_sku_id;
                    $memberPointBenefit->member_id = $member_id;
                    $memberPointBenefit->good_id = $good_id;
                    $memberPointBenefit->warehouse_id = $warehouse_id;
                    $memberPointBenefit->base_price = $base_price;
                    $memberPointBenefit->sale_price = $hsSkuPrice;
                    $memberPointBenefit->price = $memberPrice;
                    $memberPointBenefit->benefit = strval($benefit);
                    $memberPointBenefit->total_price = $total_price;
                    $memberPointBenefit->ratio = $ratio;
                    $memberPointBenefit->point = $point;
                    $memberPointBenefit->save();

                    // echo 'h_s_id = '.$h_s_id.'<br>';
                    // echo 'h_s_sku_id = '.$h_s_sku_id.'<br>';
                    // echo 'member_id = '.$member_id.'<br>';
                    // echo 'good_id = '.$good_id.'<br>';
                    // echo 'warehouse_id = '.$warehouse_id.'<br>';
                    // echo 'sku_price = '.$hsSkuPrice.'<br>';
                    // echo 'base_price = '.$base_price.'<br>';
                    // echo 'memberPrice = '.$memberPrice.'<br>';
                    // echo 'amount = '.$amount.'<br>';
                    // echo '-->benefit = '.$benefit.'<br>';
                    // echo 'total_price = '.$total_price.'<br>';
                    // echo 'ratio = '.$ratio.'<br>';
                    // echo 'point = '.$point.'<br><br>';
                }
            }
            $result->is_calculate_benefit = 1;
            $result->save();
        }
        echo 'Last HSSku_id : '.$h_s_sku_id .'<br>';
        //return 'Success!';
    }

    private function calculatePointBenefitHSProduct(){

        $h_s_product_id = '';
        $h_s_id = '';
        $warehouse_id = '';
        $good_id = null;
        $good = null;
        $hsProductPrice = 0;

        $results = HSProduct::
            with('good', 'informationReceiptProduct.informationReceipt.customer.member.member_type', 'informationReceiptProduct.good')
            ->where('is_calculate_benefit', '=', 0)
            ->orderBy('id')
            ->limit(70)
            ->get();

        // $results = HSProduct::
        //     with('good', 'informationReceiptProduct.informationReceipt.customer.member.member_type', 'informationReceiptProduct.good')
        //     ->where('h_s_id', 9691)
        //     ->get();


        foreach ($results as $result) {
            $h_s_product_id = $result->id;
            $h_s_id = $result->h_s_id;
            $warehouse_id = $result->informationReceiptProduct->informationReceipt->warehouse_id;
            $amount = $result->amount;
            $hsProductPrice = $result->price_unit;
            $total_price = $result->informationReceiptProduct->total_price;

            if($result->informationReceiptProduct->length != null && $result->informationReceiptProduct->length != 0){
                $hsProductPrice = $hsProductPrice/$result->informationReceiptProduct->length;
            }

            //declare for recieve data form data base
            $good_id = '';
            $member_status = '';
            $member_id = '';
            $member_type_id = '';
            $base_price = 0;
            $memberPrice = 0;
            $benefit = 0;
            $point = 0;
            $ratio = null;

            //echo 'h_s_product_id = '.$h_s_product_id;
            if($result->informationReceiptProduct->informationReceipt->customer && $result->informationReceiptProduct->informationReceipt->customer->member){//if customer is member(have row in Member table)
                //echo 'Found in member!!'.$member_id .'<br>';
                $member_status = $result->informationReceiptProduct->informationReceipt->customer->member->status;
                $member_id = $result->informationReceiptProduct->informationReceipt->customer->member_id;
                $member_type_id = $result->informationReceiptProduct->informationReceipt->customer->member->member_type_id;
                $baseRatio  = $result->informationReceiptProduct->informationReceipt->customer->member->member_type->ratio;
                $member_calculate_point = $result->informationReceiptProduct->informationReceipt->customer->member->member_type->is_calculate_point;

                if($member_status == 2){//check member status
                    //echo 'member_status = '.$member_status.'<br>';
                    if($result->good_id){//if hs product have a good_id
                        $good_id = $result->good_id;
                        $good = $result->good;
                    }else if($result->informationReceiptProduct->good_id){//if hs product not have good_id then find in informationReceiptProduct
                        $good_id = $result->informationReceiptProduct->good_id;
                        $good = $result->informationRecieptProduct->good;
                    }else{// if not found good_id in hsProduct and informationRecieptProduct then find in good detail
                       // echo 'Find in good detail';
                        $goodDetail = GoodDetail::where('type_coil', $result->informationReceiptProduct->type_coil)
                            ->where('girth', $result->informationReceiptProduct->girth)
                            ->where('thick', $result->informationReceiptProduct->thick)
                            ->where('g', $result->informationReceiptProduct->g)
                            ->where('color', $result->informationReceiptProduct->color)
                            ->where('type_product', $result->informationReceiptProduct->type_product)
                            ->where('type_roof', $result->informationReceiptProduct->type_roof)
                            ->first();

                        if($goodDetail){
                            echo 'Found good id in good detail';
                            $good_id = $goodDetail->good_id;
                            $good = Good::where('id',$good_id)->first();
                            if($good->is_checck_price == 1){
                                $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                    ->where('warehouse_id', $warehouse_id)
                                    ->where('good_id', $good_id)
                                    ->first();
                                //return $good_warehouse;
                                $base_price = $good_warehouse->base_price;

                                if($good_warehouse->goodPrice){
                                    $goodPrices = $good_warehouse->goodPrice;
                                    foreach ($goodPrices as $goodPrice) {
                                        if($goodPrice->member_type_id == $member_type_id){
                                            $memberPrice = $goodPrice->price;
                                            if($memberPrice > 0){
                                                $benefit = $this->calBenefit($hsProductPrice, $base_price, $memberPrice);
                                            }
                                        }
                                    }
                                }
                            }else{

                                $girth = null;
                                if($result->informationReceiptProduct->type_product == 'แผ่นตรง' || $result->informationReceiptProduct->type_product == 'แผ่นโค้ง'){
                                    $girth = '914';
                                }else{
                                    $girth = $result->informationReceiptProduct->girth;
                                }

                                $good_detail_benefits = GoodDetailBenefit::
                                    orWhere('type_coil', $result->informationReceiptProduct->type_coil)
                                    ->orWhere('girth', $girth)
                                    ->orWhere('thick', $result->informationReceiptProduct->thick)
                                    ->orWhere('g', $result->informationReceiptProduct->g)
                                    ->orWhere('color', $result->informationReceiptProduct->color)
                                    ->orWhere('type_product', $result->informationReceiptProduct->type_product)
                                    ->orWhere('type_roof', $result->informationReceiptProduct->type_roof)
                                    ->get();

                                //return $good_detail_benefits;

                                $goodDetailBenefit = $this->checkGoodDetail($good_detail_benefits, $result->informationReceiptProduct);

                                if($goodDetailBenefit){
                                    //echo '<br> goodDetailBenefit_id : '.$goodDetailBenefit->id;
                                    $good_detail_benefit_id = $goodDetailBenefit->id;
                                    $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                        ->where('warehouse_id', $warehouse_id)
                                        ->where('good_detail_benefit_id', $good_detail_benefit_id)
                                        ->first();
                                    //return $good_warehouse;
                                    //echo '<br>HAVE GOOD DETAIL BENEFIT';
                                    $base_price = $good_warehouse->base_price;

                                    if($good_warehouse->goodPrice){
                                        $goodPrices = $good_warehouse->goodPrice;
                                        foreach ($goodPrices as $goodPrice) {
                                            if($goodPrice->member_type_id == $member_type_id){
                                                $memberPrice = $goodPrice->price;
                                                if($memberPrice > 0){
                                                    $benefit = $this->calBenefit($hsProductPrice, $base_price, $memberPrice);
                                                }
                                            }
                                        }
                                    }
                                    $benefit = $benefit * $amount;
                                }
                        }

                        }else{
                            //echo 'Have a problem';
                            $result->is_problem = 1;
                            $result->save();
                        }
                    }

                    if($good){
                        if($good['is_check_price'] == 1){// if good have a setting price
                            echo 'is_check_price == 1';
                            $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                ->where('warehouse_id', $warehouse_id)
                                ->where('good_id', $good_id)
                                ->first();
                            //return $good_warehouse;
                            $base_price = $good_warehouse->base_price;

                            if($good_warehouse->goodPrice){
                                $goodPrices = $good_warehouse->goodPrice;
                                foreach ($goodPrices as $goodPrice) {
                                    if($goodPrice->member_type_id == $member_type_id){
                                        $memberPrice = $goodPrice->price;
                                        if($memberPrice > 0){
                                            $benefit = $this->calBenefit($hsProductPrice, $base_price, $memberPrice);
                                        }
                                    }
                                }
                            }

                            $benefit = $benefit * $amount;

                        }else{

                                $girth = null;
                                if($result->informationReceiptProduct->type_product == 'แผ่นตรง' || $result->informationReceiptProduct->type_product == 'แผ่นโค้ง'){
                                    $girth = '914';
                                }else{
                                    $girth = $result->informationReceiptProduct->girth;
                                }

                                $good_detail_benefits = GoodDetailBenefit::
                                    orWhere('type_coil', $result->informationReceiptProduct->type_coil)
                                    ->orWhere('girth', $girth)
                                    ->orWhere('thick', $result->informationReceiptProduct->thick)
                                    ->orWhere('g', $result->informationReceiptProduct->g)
                                    ->orWhere('color', $result->informationReceiptProduct->color)
                                    ->orWhere('type_product', $result->informationReceiptProduct->type_product)
                                    ->orWhere('type_roof', $result->informationReceiptProduct->type_roof)
                                    ->get();

                                //return $good_detail_benefits;

                                $goodDetailBenefit = $this->checkGoodDetail($good_detail_benefits, $result->informationReceiptProduct);

                                if($goodDetailBenefit){
                                    //echo '<br> goodDetailBenefit_id : '.$goodDetailBenefit->id;
                                    $good_detail_benefit_id = $goodDetailBenefit->id;
                                    $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                        ->where('warehouse_id', $warehouse_id)
                                        ->where('good_detail_benefit_id', $good_detail_benefit_id)
                                        ->first();
                                    //return $good_warehouse;
                                    echo '<br>HAVE GOOD DETAIL BENEFIT<br>';
                                    $base_price = $good_warehouse->base_price;

                                    if($good_warehouse->goodPrice){
                                        $goodPrices = $good_warehouse->goodPrice;
                                        foreach ($goodPrices as $goodPrice) {
                                            if($goodPrice->member_type_id == $member_type_id){
                                                $memberPrice = $goodPrice->price;
                                                if($memberPrice > 0){
                                                    echo 'sale_price = '.$hsProductPrice;
                                                    echo '<br>base_price = '.$base_price;
                                                    echo '<br>member_price = '.$memberPrice;
                                                    $benefit = $this->calBenefit($hsProductPrice, $base_price, $memberPrice);
                                                    echo '<br>after cal benefit  = '.$benefit.'<br>';
                                                }
                                            }
                                        }
                                    }
                                    $benefit = $benefit * $amount;
                                }
                        }
                    }

                    if($result->informationReceiptProduct->length != null && $result->informationReceiptProduct->length != 0){
                        echo '-->benefit = '.$benefit.'<br>';
                        echo '-->length = '.$result->informationReceiptProduct->length.'<br>';
                        $benefit = $benefit * $result->informationReceiptProduct->length;
                        echo '-->benefit * length = '.$benefit.'<br>';
                    }

                    if($benefit > 0){ //if benefit > 0 --> total_price = total_price - benefit
                        //echo '<br> OLD TOTAL PRICE = '.$total_price.'<br>';
                        $total_price = $total_price - $benefit;
                        //echo 'NEW TOTAL PRICE = '.$total_price.'<br>';
                    }

                    $ratio = $baseRatio;
                    if($good && $member_calculate_point == 1){
                        if($good['is_check_ratio'] == 1){// if good have good_ratio (handle null)
                            //echo '<br>IF good check RATIO = '.$ratio.'<br>';
                            $ratio = $good['goodRatio']->ratio;
                            $point = $this->calPoint($ratio, $total_price);
                        }else{//if good not have good_ratio set ratio = 500
                            $good_detail_points = GoodDetailPoint::with('goodRatio')
                                ->orWhere('type_coil', $result->informationReceiptProduct->type_coil)
                                ->orWhere('girth', $girth)
                                ->orWhere('thick', $result->informationReceiptProduct->thick)
                                ->orWhere('g', $result->informationReceiptProduct->g)
                                ->orWhere('color', $result->informationReceiptProduct->color)
                                ->orWhere('type_product', $result->informationReceiptProduct->type_product)
                                ->orWhere('type_roof', $result->informationReceiptProduct->type_roof)
                                ->get();

                                //check setting by good detail
                                $goodDetailPoint = $this->checkGoodDetail($good_detail_points, $result->informationReceiptProduct);

                            if($goodDetailPoint){
                                //echo '<br>IF good detail RATIO = '.$ratio.'<br>';
                                $ratio = $goodDetailPoint->goodRatio->ratio;
                                $point = $this->calPoint($ratio, $total_price);
                            }else{
                                //echo '<br>ELSE RATIO = '.$ratio.'<br>';
                                $point = $this->calPoint($ratio, $total_price);
                            }
                        }
                    }else{
                        if($member_calculate_point == 1){
                            //echo '<br>NO GOOODDDD = '.$ratio.'<br>';
                            $point = $this->calPoint($ratio, $total_price);

                        }else{
                            $point = 0;
                        }

                    }

                    if($memberPrice != null || $memberPrice != 0){
                        $memberPrice = (string) $memberPrice;
                        $hsProductPrice = (string) $hsProductPrice;
                        if($hsProductPrice < $memberPrice){
                            $point = 0;
                        }
                    }

                    $memberPointBenefit = MemberPointBenefit::where('h_s_product_id', $h_s_product_id)->where('h_s_id', $h_s_id)->where('good_id', $good_id)->first();
                    //return $memberPointBenefit;
                    if($memberPointBenefit == null || $memberPointBenefit == ''){
                        $memberPointBenefit = new MemberPointBenefit;
                    }
                    $memberPointBenefit->h_s_id = $h_s_id;
                    $memberPointBenefit->h_s_product_id = $h_s_product_id;
                    $memberPointBenefit->member_id = $member_id;
                    $memberPointBenefit->good_id = $good_id;
                    $memberPointBenefit->warehouse_id = $warehouse_id;
                    $memberPointBenefit->base_price = $base_price;
                    $memberPointBenefit->sale_price = strval($hsProductPrice);
                    $memberPointBenefit->price = $memberPrice;
                    $memberPointBenefit->benefit = strval($benefit);
                    $memberPointBenefit->total_price = $total_price;
                    $memberPointBenefit->ratio = $ratio;
                    $memberPointBenefit->point = $point;
                    $memberPointBenefit->save();

                    // echo '<br>h_s_id = '.$h_s_id.'<br>';
                    // echo 'h_s_product_id = '.$h_s_product_id.'<br>';
                    // echo 'member_id = '.$member_id.'<br>';
                    // echo 'good_id = '.$good_id.'<br>';
                    // echo 'warehouse_id = '.$warehouse_id.'<br>';
                    // echo 'sale_price = '.$hsProductPrice.'<br>';
                    // echo 'base_price = '.$base_price.'<br>';
                    // echo 'memberPrice = '.$memberPrice.'<br>';
                    // echo 'amount = '.$amount.'<br>';
                    // echo '-->benefit = '.$benefit.'<br>';
                    // echo 'total_price = '.$total_price.'<br>';
                    // echo 'ratio = '.$ratio.'<br>';
                    // echo 'point = '.$point.'<br><br>';
                }
            }
            $result->is_calculate_benefit = 1;
            $result->save();

        }
        echo 'Last HSProduct_id : '.$h_s_product_id .'<br>';
       //return 'Success!';
    }

    private function calculatePointBenefitHSDoor(){

        $h_s_door_id = '';
        $h_s_id = '';
        $warehouse_id = '';
        $good_id = null;
        $good = null;

        // $results = HSDoor::
        //     with('good', 'informationReceiptDoor.informationReceipt.customer.member.member_type')
        //     ->where('h_s_id', 10483)
        //     ->get();

         $results = HSDoor::
            with('good', 'informationReceiptDoor.informationReceipt.customer.member.member_type')
            ->where('is_calculate_benefit', '=', 0)
             ->whereHas('informationReceiptDoor', function ( $query) {
                    $query->where('deleted_at', null);
            })
            ->orderBy('id')
            ->limit(30)
            ->get();

        //return $results;
        foreach ($results as $result) {

                $h_s_door_id = $result->id;
                $h_s_id = $result->h_s_id;
                $warehouse_id = $result->informationReceiptDoor->informationReceipt->warehouse_id;
                $amount = $result->amount;
                $total_price = $result->informationReceiptDoor->total_price;

                $area = 5;
                if($result->informationReceiptDoor->area != null && $result->informationReceiptDoor->area != 0){
                    if($result->informationReceiptDoor->area > 5){
                        $area = $result->informationReceiptDoor->area;
                    }
                }

                $hsDoorPrice = (float) $result->informationReceiptDoor->price_thick;

                //declare for recieve data form data base
                $good_id = '';
                $member_status = '';
                $member_id = '';
                $member_type_id = '';
                $base_price = 0;
                $memberPrice = 0;
                $benefit = 0;
                $point = 0;
                $ratio = null;

                //echo 'h_s_door_id = '.$h_s_door_id;
                if($result->informationReceiptDoor->informationReceipt->customer && $result->informationReceiptDoor->informationReceipt->customer->member){//if customer is member(have row in Member table)
                    //echo 'Found in member!!'.$member_id .'<br>';
                    $member_status = $result->informationReceiptDoor->informationReceipt->customer->member->status;
                    $member_id = $result->informationReceiptDoor->informationReceipt->customer->member_id;
                    $member_type_id = $result->informationReceiptDoor->informationReceipt->customer->member->member_type_id;
                    $baseRatio = $result->informationReceiptDoor->informationReceipt->customer->member->member_type->ratio;
                    $member_calculate_point = $result->informationReceiptDoor->informationReceipt->customer->member->member_type->is_calculate_point;

                    if($member_status == 2){//check member status
                        if($result->good_id){//if hs product have a good_id
                            $good_id = $result->good_id;
                            $good = $result->good;
                        }else{// if not found good_id in hsProduct and informationRecieptProduct then find in good detail
                        // echo 'Find in good detail';
                            $goodDetail = GoodDetail::where('thick', $result->informationReceiptDoor->thick)
                                ->where('color', $result->informationReceiptDoor->color)
                                ->where('type_product', $result->informationReceiptDoor->type_door)
                                ->where('type_roof', $result->informationReceiptDoor->name)
                                ->first();

                            if($goodDetail){
                                echo 'Found good id in good detail';
                                $good_id = $goodDetail->good_id;
                                $good = Good::where('id',$good_id)->first();
                                if($good->is_checck_price == 1){
                                    $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                        ->where('warehouse_id', $warehouse_id)
                                        ->where('good_id', $good_id)
                                        ->first();
                                    //return $good_warehouse;
                                    $base_price = $good_warehouse->base_price;

                                    if($good_warehouse->goodPrice){
                                        $goodPrices = $good_warehouse->goodPrice;
                                        foreach ($goodPrices as $goodPrice) {
                                            if($goodPrice->member_type_id == $member_type_id){
                                                $memberPrice = $goodPrice->price;
                                                if($memberPrice > 0){
                                                    $benefit = $this->calBenefit($hsDoorPrice, $base_price, $memberPrice);
                                                }
                                            }
                                        }
                                    }
                                }else{

                                    $good_detail_benefits = GoodDetailBenefit::
                                    orWhere('thick', $result->informationReceiptDoor->thick)
                                    ->orWhere('color', $result->informationReceiptDoor->color)
                                    ->orWhere('type_product', $result->informationReceiptDoor->type_door)
                                    ->get();

                                    //return $good_detail_benefits;

                                    $goodDetailBenefit = $this->checkGoodDetail($good_detail_benefits, $result->informationReceiptDoor);

                                    if($goodDetailBenefit){
                                        //echo '<br> goodDetailBenefit_id : '.$goodDetailBenefit->id;
                                        $good_detail_benefit_id = $goodDetailBenefit->id;
                                        $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                            ->where('warehouse_id', $warehouse_id)
                                            ->where('good_detail_benefit_id', $good_detail_benefit_id)
                                            ->first();
                                        //return $good_warehouse;
                                        //echo '<br>HAVE GOOD DETAIL BENEFIT';
                                        $base_price = $good_warehouse->base_price;

                                        if($good_warehouse->goodPrice){
                                            $goodPrices = $good_warehouse->goodPrice;
                                            foreach ($goodPrices as $goodPrice) {
                                                if($goodPrice->member_type_id == $member_type_id){
                                                    $memberPrice = $goodPrice->price;
                                                    if($memberPrice > 0){
                                                        $benefit = $this->calBenefit($hsDoorPrice, $base_price, $memberPrice);
                                                    }
                                                }
                                            }
                                        }
                                        $benefit = $benefit * $amount * $area;
                                    }
                                }


                            }else{
                                echo 'Have a problem';
                                $result->is_problem = 1;
                                $result->save();
                            }
                        }
                        if($good){
                            if($good['is_check_price'] == 1){// if good have a setting price
                                $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                    ->where('warehouse_id', $warehouse_id)
                                    ->where('good_id', $good_id)
                                    ->first();
                                //return $good_warehouse;
                                $base_price = $good_warehouse->base_price;

                                if($good_warehouse->goodPrice){
                                    $goodPrices = $good_warehouse->goodPrice;
                                    foreach ($goodPrices as $goodPrice) {
                                        if($goodPrice->member_type_id == $member_type_id){
                                            $memberPrice = $goodPrice->price;
                                            $benefit = $this->calBenefit($hsDoorPrice, $base_price, $memberPrice);
                                        }
                                    }
                                }
                                $benefit = $benefit * $amount * $area;
                            }else{

                                $good_detail_benefit = GoodDetailBenefit::
                                    orWhere('thick', $result->informationReceiptDoor->thick)
                                    ->orWhere('color', $result->informationReceiptDoor->color)
                                    ->orWhere('type_product', $result->informationReceiptDoor->type_door)
                                    ->get();

                                //return $good_detail_benefit;
                                $goodDetailBenefit = $this->checkDoorDetail($good_detail_benefit, $result->informationReceiptDoor);

                                if($goodDetailBenefit){
                                    $good_detail_benefit_id = $goodDetailBenefit->id;
                                    $good_warehouse = GoodWarehouse::with('goodPrice','warehouse')
                                        ->where('warehouse_id', $warehouse_id)
                                        ->where('good_detail_benefit_id', $good_detail_benefit_id)
                                        ->first();
                                    //return $good_warehouse;
                                    $base_price = $good_warehouse->base_price;

                                    if($good_warehouse->goodPrice){
                                        $goodPrices = $good_warehouse->goodPrice;
                                        foreach ($goodPrices as $goodPrice) {
                                            if($goodPrice->member_type_id == $member_type_id){
                                                $memberPrice = $goodPrice->price;
                                                if($memberPrice > 0){
                                                    $benefit = $this->calBenefit($hsDoorPrice, $base_price, $memberPrice);
                                                }
                                            }
                                        }
                                    }
                                    $benefit = $benefit * $amount * $area;
                                }

                            }
                        }

                        if($benefit > 0){ //if benefit > 0 --> total_price = total_price - benefit
                            //echo '<br> OLD TOTAL PRICE = '.$total_price.'<br>';
                            $total_price = $total_price - $benefit;
                            //echo 'NEW TOTAL PRICE = '.$total_price.'<br>';
                        }

                        $ratio = $baseRatio;
                        if($member_calculate_point == 1){
                            if($good){
                                if($good['is_check_ratio'] == 1){// if good have good_ratio (handle null)
                                    //echo '<br>IF good check RATIO = '.$ratio.'<br>';
                                    $ratio = $good['goodRatio']->ratio;
                                    $point = $this->calPoint($ratio, $total_price);
                                }else{//if good not have good_ratio set ratio = 500
                                    $good_detail_point = GoodDetailPoint::with('goodRatio')
                                        ->orWhere('thick', $result->informationReceiptDoor->thick)
                                        ->orWhere('color', $result->informationReceiptDoor->color)
                                        ->orWhere('type_product', $result->informationReceiptDoor->type_door)
                                        ->get();

                                    $goodDetailPoint = $this->checkDoorDetail($good_detail_point, $result->informationReceiptDoor);

                                    if($goodDetailPoint){
                                        //echo '<br>IF good detail RATIO = '.$ratio.'<br>';
                                        $ratio = $good_detail_point->goodRatio->ratio;
                                        $point = $this->calPoint($ratio, $total_price);
                                    }else{
                                        //echo '<br>ELSE RATIO = '.$ratio.'<br>';
                                        $point = $this->calPoint($ratio, $total_price);
                                    }
                                }
                            }else{
                                //echo '<br>NO GOOODDDD = '.$ratio.'<br>';
                                $point = $this->calPoint($ratio, $total_price);
                            }
                        }
                        if($memberPrice != null || $memberPrice != 0){
                            $memberPrice = (string) $memberPrice;
                            $hsDoorPrice = (string) $hsDoorPrice;
                            if($hsDoorPrice < $memberPrice){
                                $point = 0;
                            }
                        }

                        $memberPointBenefit = MemberPointBenefit::where('h_s_door_id', $h_s_door_id)->where('h_s_id', $h_s_id)->where('good_id', $good_id)->first();
                        //return $memberPointBenefit;
                        if($memberPointBenefit == null || $memberPointBenefit == ''){
                            $memberPointBenefit = new MemberPointBenefit;
                        }
                        $memberPointBenefit->h_s_id = $h_s_id;
                        $memberPointBenefit->h_s_door_id = $h_s_door_id;
                        $memberPointBenefit->member_id = $member_id;
                        $memberPointBenefit->good_id = $good_id;
                        $memberPointBenefit->warehouse_id = $warehouse_id;
                        $memberPointBenefit->base_price = $base_price;
                        $memberPointBenefit->sale_price = $hsDoorPrice;
                        $memberPointBenefit->price = $memberPrice;
                        $memberPointBenefit->benefit = strval($benefit);
                        $memberPointBenefit->total_price = $total_price;
                        $memberPointBenefit->ratio = $ratio;
                        $memberPointBenefit->point = $point;
                        $memberPointBenefit->save();

                        // echo 'h_s_id = '.$h_s_id.'<br>';
                        // echo 'h_s_door_id = '.$h_s_door_id.'<br>';
                        // echo 'member_id = '.$member_id.'<br>';
                        // echo 'good_id = '.$good_id.'<br>';
                        // echo 'warehouse_id = '.$warehouse_id.'<br>';
                        // echo 'sale_price = '.$hsDoorPrice.'<br>';
                        // echo 'base_price = '.$base_price.'<br>';
                        // echo 'memberPrice = '.$memberPrice.'<br>';
                        // echo 'amount = '.$amount.'<br>';
                        // echo '-->benefit = '.$benefit.'<br>';
                        // echo 'total_price = '.$total_price.'<br>';
                        // echo 'ratio = '.$ratio.'<br>';
                        // echo 'point = '.$point.'<br><br>';
                    }
                }
                $result->is_calculate_benefit = 1;
                $result->save();


        }
        echo 'Last h_s_door_id : '.$h_s_door_id .'<br>';
       //return 'Success!';
    }

    private function calculatePointBenefitHSService(){

        $h_s_service_id = '';
        $h_s_id = '';

        $results = HSService::
            with('informationReceiptService.informationReceipt.customer.member.member_type')
            ->where('is_calculate_benefit', '=', 0)
            ->orderBy('id')
            ->limit(50)
            ->get();

            foreach ($results as $result) {
                $h_s_service_id = $result->id;
                $h_s_id = $result->h_s_id;
                $warehouse_id = $result->informationReceiptService->informationReceipt->warehouse_id;
                $hsServicePrice = $result->total_price;
                $total_price = $result->total_price;
                //declare for recieve data form data base
                $member_status = '';
                $member_id = '';
                $point = 0;
                $ratio = null;

                if($result->informationReceiptService->informationReceipt->customer && $result->informationReceiptService->informationReceipt->customer->member){//if customer is member(have row in Member table)

                    $member_status = $result->informationReceiptService->informationReceipt->customer->member->status;
                    $member_id = $result->informationReceiptService->informationReceipt->customer->member_id;
                    $baseRatio = $result->informationReceiptService->informationReceipt->customer->member->member_type->ratio;
                    $member_calculate_point = $result->informationReceiptService->informationReceipt->customer->member->member_type->is_calculate_point;

                    if($member_status == 2 && $member_calculate_point == 1){//check member status

                        $ratio = $baseRatio;
                        $point = bcadd(0,$total_price/$ratio,2);

                        $memberPointBenefit = MemberPointBenefit::where('h_s_service_id', $h_s_service_id)->where('h_s_id', $h_s_id)->first();
                        //return $MemberPointBenefit;
                        if($memberPointBenefit == null || $memberPointBenefit == ''){
                            $memberPointBenefit = new MemberPointBenefit;
                        }
                        $memberPointBenefit->h_s_id = $h_s_id;
                        $memberPointBenefit->h_s_service_id = $h_s_service_id;
                        $memberPointBenefit->member_id = $member_id;
                        $memberPointBenefit->good_id = null;
                        $memberPointBenefit->warehouse_id = $warehouse_id;
                        $memberPointBenefit->base_price = 0;
                        $memberPointBenefit->sale_price = $hsServicePrice;
                        $memberPointBenefit->price = 0;
                        $memberPointBenefit->benefit = 0;
                        $memberPointBenefit->total_price = $hsServicePrice;
                        $memberPointBenefit->ratio = $ratio;
                        $memberPointBenefit->point = $point;
                        $memberPointBenefit->save();

                        echo 'total_price = '.$total_price.'<br>';
                        echo 'ratio = '.$ratio.'<br>';
                        echo 'point = '.$point.'<br><br>';
                    }
                }
                $result->is_calculate_benefit = 1;
                $result->save();
            }
            echo 'Last h_s_service_id : '.$h_s_service_id .'<br>';
       //return $results;

    }

    private function calBenefit($salePrice, $basePrice, $memberPrice){

        $salePrice = (string) $salePrice;;
        $benefit = 0;

        $benefit = $salePrice - $memberPrice;
        $dif = $basePrice - $memberPrice;
        if($benefit < 0){
            $benefit = 0;
        }else if( $benefit > $dif){
            $benefit = $dif;
        }else{
            $benefit = $benefit;;
        }

        return $benefit;
    }

    private function calPoint($ratio, $totalPrice){
        $point = 0;
        if($ratio != 0 || $ratio != null || $ratio != ''){//if good have good_ratio but ratio is 0, not set ratio and not cal point
            $point = bcadd(0,$totalPrice/$ratio,2);
        }
        return $point;
    }

    private function checkGoodDetail($goodDetails, $informationReceipt){

        $goodDetailReturn = null;
        $girth = null;

        if($informationReceipt->type_product == 'แผ่นตรง' || $informationReceipt->type_product == 'แผ่นโค้ง'){
            $girth = '914';
        }else{
            $girth = $informationReceipt->girth;
        }

        $check = 0;

        foreach ($goodDetails as $goodDetail) {

            $break = 0;
            $count = 0;

            if($goodDetail->type_coil !== null && $informationReceipt->type_coil !== null && $goodDetail->type_coil !== $informationReceipt->type_coil){
                $break = 1;
            }else if($goodDetail->type_coil == $informationReceipt->type_coil){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->girth !== null && $girth !== null && $goodDetail->girth !== $girth){
                $break = 1;
            }else if($goodDetail->girth == $girth){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->thick !== null && $informationReceipt->thick !== null && $goodDetail->thick !== $informationReceipt->thick){
                $break = 1;
            }else if($goodDetail->thick == $informationReceipt->thick){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->g !== null && $informationReceipt->g !== null && $goodDetail->g !== $informationReceipt->g){
                $break = 1;
            }else if($goodDetail->g == $informationReceipt->g){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->color !== null && $informationReceipt->color !== null && $goodDetail->color !== $informationReceipt->color){
                $break = 1;
            }else if($goodDetail->color == $informationReceipt->color){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->type_product !== null && $informationReceipt->type_product !== null && $goodDetail->type_product !== $informationReceipt->type_product){
                $break = 1;
            }else if($goodDetail->type_product == $informationReceipt->type_product){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->type_roof !== null && $informationReceipt->type_roof !== null && $goodDetail->type_roof !== $informationReceipt->type_roof){
                $break = 1;
            }else if($goodDetail->type_roof == $informationReceipt->type_roof){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($break == 0 && $count > $check){
                $goodDetailReturn = $goodDetail;
                $check = $count;

            }

        }
        return $goodDetailReturn;
    }

    private function checkDoorDetail($goodDetails, $informationReceipt){

        $goodDetailReturn = null;

        $check = 0;

        foreach ($goodDetails as $goodDetail) {

            $break = 0;
            $count = 0;

            //echo $informationReceipt->thick;
            if($goodDetail->thick !== null && $informationReceipt->thick !== null && $goodDetail->thick !== $informationReceipt->thick){
                $break = 1;
            }else if($goodDetail->thick == $informationReceipt->thick){
                $count ++ ;
            }else{
                $count += 0 ;
            }


            if($goodDetail->color !== null && $informationReceipt->color !== null && $goodDetail->color !== $informationReceipt->color){
                $break = 1;
            }else if($goodDetail->color == $informationReceipt->color){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($goodDetail->type_product !== null && $informationReceipt->type_door !== null && $goodDetail->type_product !== $informationReceipt->type_door){
                $break = 1;
            }else if($goodDetail->type_product == $informationReceipt->type_door){
                $count ++ ;
            }else{
                $count += 0 ;
            }

            if($break == 0 && $count > $check){
                $goodDetailReturn = $goodDetail;
                $check = $count;

            }

        }
        return $goodDetailReturn;
    }

}
