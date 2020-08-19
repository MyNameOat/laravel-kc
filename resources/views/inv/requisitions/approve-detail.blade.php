@extends('layouts-inv.app')
@section('content')

<div class="row wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox">
        <div class="ibox-title">
            <p><h3>อนุมัติใบเบิก</h3><p>
            <span>หน้าหลัก/อนุมัติใบเบิก/รายละเอียดใบเบิก</span>
        </div>
    </div>
</div>
<div class="ibox wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-6">
                <span>
                    <h2>ใบเบิกสินค้า เลขที่ (
                        @foreach ($requisitions as $requisition)
                        {{ $requisition->code }}
                        @endforeach

                         )</h2>
                </span>
            </div>
            <div class="col-lg-6">
                <span class="pull-right">
                    <a href="{{ route('inv.approve-check-status-no',$requisition->id) }}" class="btn btn-primary waves-effect" style="font-size: 14px;">
                        อนุมัติใบเบิกสินค้า
                    </a>
                    <a href="{{ route('inv.approve-check-status-off', $requisition->id) }}" class="btn btn-danger waves-effect" style="font-size: 14px;" data-method="delete" data-confirm="ยืนยันการยกเลิกใบเบิกสินค้า">
                        ไม่อนุมัติใบเบิกสินค้า
                    </a>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6" style="text-align: center;">
            @foreach ($requisitions as $requisition)
                <p>วันที่เอกสาร :: {{ $requisition->code }}</p>
                <p>เบิกเพื่อ :: {{ $requisition->take->name }}</p>
                <p>คลัง :: {{ $requisition->warehouse->name }}</p>
                <p>หมายเหตุ :: {{ $requisition->detail }}</p>
                <p>ผู้บันทึก :: {{ $requisition->createUser->name }}</p>
            @endforeach
            </div>
            <div class="col-lg-6">

            </div>
        </div>
    </div>
</div>
<div class="row wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox">
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-responsive" id="tableReportBillDetall">
                    <thead>
                        <tr>
                            <th>รหัสสินค้า</th>
                            <th>รหัสคอยน์</th>
                            <th>ชื่อสินค้า</th>
                            <th>จำนวน</th>
                            <th>หน่วยนับ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requisitionGoods as $key => $requisitionGood)
                        <tr>
                            <td>{{ $requisitionGood->good->code }}</td>
                            <td>
                                @if($requisitionGood->good->type->is_coil == 1)
                                {{ $requisitionGood->warehouseGood->coil_code }}
                                @else
                                  -
                                @endif
                            </td>
                            <td>{{ $requisitionGood->good->name }}</td>
                            <td>{{ $requisitionGood->amount }}</td>
                            <td>{{ $requisitionGood->good->unit->name }}</td>
                        </tr>
                         @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
<script>

    var tableReportBillDetall = $('#tableReportBillDetall').DataTable();

</script>
@endsection
