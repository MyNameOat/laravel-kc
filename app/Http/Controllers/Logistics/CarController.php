<?php

namespace App\Http\Controllers\Logistics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\GoodAudit;
use App\Car;
use App\Log_car;
use App\Employee;
use App\Warehouse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Validator;
use Response;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('logistics.car.dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        dd($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        dd($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // โหลดหน้า ข้อมูลรถขนส่ง
    public function callpageDataList()
    {
        return view('logistics.car.ListDataCar');
    }

    //โหลดข้อมูล การขนส่ง
    public function ListDataCar()
    {
        $Datacar['data'] = Car::all();
        return $Datacar;
    }

    //โหลดหน้า เพิ่มข้อมูล
    public function callpageEvenAdd()
    {
        // dd('callpageEvenAdd');
        $aResult = [
            'rtCode' => '99',
            'detail' => 'callpageadd',
        ];
        return view('logistics.car.addeditLogistics', compact('aResult'));
    }

    // โหลดหน้าแก้ไข
    public function callpageedit($id)
    {

        $aResult = [
            'rtCode'  => '1',
            'detail'  => 'callpageadd',
            'contact' => Car::find($id),
        ];

        return view('logistics.car.addeditLogistics', compact('aResult'));

    }

    //เพิ่มข้อมูล car
    public function EventAddCar(Request $request)
    {
        try {
            DB::beginTransaction();
            $Car                        =  new Car();
            $rules =[
                'vehicle_number'        => 'required',
                'weight_car'            => 'required',
                'driver_name'           => 'required',
                'current_mileage'       => 'required',
                'tire_code'             => 'required',
                'bodycar_code'          => 'required',
                'car_year'              => 'required',
                'fuel_consumption_rate' => 'required',
                'maintenance_history'   => 'required',
                'claim_history'         => 'required',
                'engine_code_number'    => 'required',
                'copy_of_car_manual'    => 'required',
                'date_of_purchase'      => 'required',
                'car_value'             => 'required',
                'life_time'             => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->passes()) {

                $Car->vehicle_number         = $request->vehicle_number;
                $Car->weight_car             = $request->weight_car;
                $Car->driver_name            = $request->driver_name;
                $Car->current_mileage        = $request->current_mileage;
                $Car->tire_code              = $request->tire_code;
                $Car->bodycar_code           = $request->bodycar_code;
                $Car->car_year               = $request->car_year;
                $Car->fuel_consumption_rate  = $request->fuel_consumption_rate;
                $Car->maintenance_history    = $request->maintenance_history;
                $Car->claim_history          = $request->claim_history;
                $Car->engine_code_number     = $request->engine_code_number;
                $Car->copy_of_car_manual     = $request->copy_of_car_manual;
                $Car->date_of_purchase       = $request->date_of_purchase;
                $Car->car_value              = $request->car_value;
                $Car->life_time              = $request->life_time;
                $Car->status_car             = $request->status_car;
                $Car->wheel_car              = $request->wheel_car;
                $Car->system_oil             = $request->system_oil;
                $Car->speed_car              = $request->speed_car;
                $Car->system_gas             = $request->system_gas;
                $Car->system_gear            = $request->system_gear;
                $Car->save();

                if($request->hasFile('imagecar'))
                {
                    $imagecar = $request->file('imagecar');
                    foreach($imagecar as $fileImgcar)
                    {
                        $fileImgname    = date('dmyyHi').'.'.$fileImgcar->getClientOriginalName();
                        $fileImgcar->storeAs('public/imageCar', $fileImgname);
                        $data[]  = $fileImgname;
                    }

                    $Car->image_car = json_encode($data);
                    $Car->save();

                }

                DB::commit();
                return response()->json([
                    'recode'   => '1',
                    'status'   =>'success',
                    'callback' => $Car->id,
                ]);

            }

            return response()->json(['error'=>$validator->errors()->all()]);

        } catch (\Throwable $th) {

            return response()->json([
                'recode' => '99',
                'detail' => $th,
            ]);

        }
    }

    //แก้ไขข้อมูล car
    public function EventEditCar(Request $request)
    {

            DB::beginTransaction();
            $Car                        = Car::findOrFail($request->id);
            $rules =[
                'vehicle_number'        => 'required',
                'weight_car'            => 'required',
                'driver_name'           => 'required',
                'current_mileage'       => 'required',
                'tire_code'             => 'required',
                'bodycar_code'          => 'required',
                'car_year'              => 'required',
                'fuel_consumption_rate' => 'required',
                'maintenance_history'   => 'required',
                'claim_history'         => 'required',
                'engine_code_number'    => 'required',
                'copy_of_car_manual'    => 'required',
                'date_of_purchase'      => 'required',
                'car_value'             => 'required',
                'life_time'             => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->passes()) {

                $Car->vehicle_number         = $request->vehicle_number;
                $Car->weight_car             = $request->weight_car;
                $Car->driver_name            = $request->driver_name;
                $Car->current_mileage        = $request->current_mileage;
                $Car->tire_code              = $request->tire_code;
                $Car->bodycar_code           = $request->bodycar_code;
                $Car->car_year               = $request->car_year;
                $Car->fuel_consumption_rate  = $request->fuel_consumption_rate;
                $Car->maintenance_history    = $request->maintenance_history;
                $Car->claim_history          = $request->claim_history;
                $Car->engine_code_number     = $request->engine_code_number;
                $Car->copy_of_car_manual     = $request->copy_of_car_manual;
                $Car->date_of_purchase       = $request->date_of_purchase;
                $Car->car_value              = $request->car_value;
                $Car->life_time              = $request->life_time;
                $Car->status_car             = $request->status_car;
                $Car->wheel_car              = $request->wheel_car;
                $Car->system_oil             = $request->system_oil;
                $Car->speed_car              = $request->speed_car;
                $Car->system_gas             = $request->system_gas;
                $Car->system_gear            = $request->system_gear;
                $Car->save();

                if($request->hasFile('imagecar'))
                {
                    $imagecar = $request->file('imagecar');
                    foreach($imagecar as $fileImgcar)
                    {
                        Storage::delete('public/imageCar'.$image);
                        $fileImgname    = date('dmyyHi').'.'.$fileImgcar->getClientOriginalName();
                        $fileImgcar->storeAs('public/imageCar', $fileImgname);
                        $data[]  = $fileImgname;
                    }

                    $Car->image_car = json_encode($data);
                    $Car->save();

                }

                DB::commit();
                return response()->json([
                    'recode'   => '1',
                    'status'   =>'success',
                    'callback' => $Car->id,
                ]);
            }

            return response()->json(['error'=>$validator->errors()->all()]);

        }

    public function EventDeleleCar(Request $request)
    {
        $this->validate($request,['id' => 'required']);
        $id       = $request->id;
        $Car      = Car::find($id);
        $imgname  = json_decode($Car->image_car);
        if($Car->delete()){
            if(is_array($imgname)){
                foreach($imgname as  $imgcar) {
                    Storage::delete('public/imageCar/'.$imgcar);
                }
            }
            $data = [
                'title'  => 'ลบสำเร็จ',
                'msg'    => 'ลบข้อมูลสำเร็จ',
                'status' => 'success',
            ];
        }else{
            $data = [
                'title'  => 'เกิดข้อผิดพลาด',
                'msg'    => 'ลบข้อมูลม่สำเร็จ',
                'status' => 'error',
            ];
        }
        return $data;
    }

    // แสดงข้อมูลรถ
    public function EventShowDataCar($id)
    {
        return response()->json([
            'rtCode'  => '1',
            'contact' => Car::find($id),
        ]);
    }

    // โหลดหน้า ประวัติรถขนส่ง
    public function callpageHistoryCar($id)
    {

        $aResult = [
            'rtCode'     => '1',
            'car_id'     => $id,
            'Warehouse'  => Warehouse::get(),
        ];
        return view('logistics.car.historyCar', compact('aResult'));

    }

    // โหลดขหน้าListLogCar
    public function callpageListLogCar($id)
    {
        $aResult = [
            'rtCode'  => '1',
            'detail'  => 'loadpagelogcar',
            'car_id'  => $id,
        ];

        return view('logistics.car.datatablelogcar');
    }

    // โหลดข้อมูลlogcar
    public function dataListlogcar(Request $request)
    {

       $id      = $request->Listcar_id;
       $Log_car['data'] = Log_car::with('car','warehouse','final_warehouse')->where('car_id',$id)->orderBy('created_at', 'DESC')->get();
       return $Log_car;

    }

    //บันทึกข้อมูล Logcar
    public function EvenAddlogcar(Request $request)
    {

        $Log_car_updated =  Log_car::orderBy('id', 'desc')->first();
        if(isset($Log_car_updated)){
            $Log_car_updated->time_in  = $request->car_time;
            $Log_car_updated->save();
        }

        $logcar                = new Log_car();
        $logcar->car_id        = $request->car_id;
        $logcar->warehouses_id = $request->Warehouse_car;
        $logcar->bill_id       = $request->invoice_car;
        $logcar->date_car      = $request->car_date;
        $logcar->time_out      = $request->car_time;


        if($logcar->save()){
            return response()->json([
                'recode'   => '1',
                'status'   =>'success'
            ]);
        }else{
            return response()->json([
                'recode'   => '99',
                'status'   =>'error'
            ]);
        }


    }

    //โหลดหน้าแก้ไขข้อมูล logcar
    public function callpageEvenEditLogcar($id)
    {

        $log_car_data['data'] = Log_car::with('car','warehouse')->where('id',$id)->get();
        return  $log_car_data['data'];

    }

    // โหลดหน้าเพิ่มประเภทประวัติรถ
    public function callPageTransporttype()
    {
        $aResult = [
            'rtCode'        => '1',
            'warehouse'     => Warehouse::get(),
            'car'           => car::get(),
            'typeTransport' => ''
        ];

        return view('logistics.transporttype.dashboard', compact('aResult'));
    }

    public function evenAdddataLogCar(Request $request)
    {
        $logcar                = new Log_car();
        $historyType           = $request->car_historyType;
        switch ($historyType) {
            case 'คลัง' :

                $Log_car_updated =  Log_car::orderBy('id', 'desc')->first();

                if(isset($Log_car_updated)){
                    $Log_car_updated->time_in  = $request->car_datetime_warehouses;
                    $Log_car_updated->save();
                }

                $logcar->car_id        = $request->car_id;
                $logcar->typeTransport = $request->car_historyType;
                $logcar->warehouses_id = $request->car_warehouses_in;
                $logcar->date_car      = $request->car_datetime_warehouses;
                $logcar->time_out      = $request->car_datetime_warehouses;

                break;
            case 'ขนส่ง' :

                $Log_car_updated =  Log_car::orderBy('id', 'desc')->first();
                if(isset($Log_car_updated)){
                    $Log_car_updated->time_in  = $request->car_datetime_typetransport;
                    $Log_car_updated->save();
                }

                $logcar->car_id        = $request->car_id;
                $logcar->typeTransport = $request->car_historyType;
                $logcar->invoice_id    = $request->invoice_id;
                $logcar->date_car      = $request->car_datetime_typetransport;
                $logcar->time_out      = $request->car_datetime_typetransport;
                $logcar->final_warehouses_id  = $request->warehouse_final;
                break;
            }

            if($logcar->save()){
                return response()->json([
                    'recode'   => '1',
                    'status'   =>'success'
                ]);
            }else{
                return response()->json([
                    'recode'   => '99',
                    'status'   =>'error'
                ]);
            }
    }

    //ค้นหาประเภทประวัติรถ
    public function evengetdatatypecar($id)
    {

        $log_car = Log_car::where('car_id',$id)->get()->first();

        // if(isset($log_car)){

        //     $typeTransport =  $log_car->where('typeTransport','<>' ,$log_car->typeTransport)->get();

        // }else{
        //     $typeTransport = "";
        // }

        // $result = [

        //     'typeTransport' => $typeTransport[0]->typeTransport

        // ];

        return $log_car;

    }

    //โหลดหน้า เอกสาร
    public function callPagetransferDocument()
    {
        return view('logistics.transferDocument.dashboard');
    }

    //โหลดหน้าเพิ่มข้อมูลเอกสารขนส่ง
    public function callPagetransferDocumentadd()
    {
        return view('logistics.transferDocument.addedittransportdocuments');
    }

    //โหลดข้อมูล พนักงาน
    function listdataemployee()
    {
        $dataEmployee['data'] = Employee::all();
        return $dataEmployee;
    }


    function addemployeeid(Request $request)
    {
        dd($request->all());
    }


}






