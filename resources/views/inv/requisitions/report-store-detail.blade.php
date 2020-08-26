<?php
    use Carbon\Carbon;
?>

@extends('layouts-inv.app')
@section('content')

<div class="row">
    <div class="ibox wrapper wrapper bg-white animated fadeInRight">
        <div class="ibox-title" style="height: 120px; margin-top: 5px;">
            <span><h2>ใบเบิกสินค้า</h2></span>
            <ol class="breadcrumb">
                <li class="breadvrumb-item"><a href="{{ route('inv.index') }}">หน้าหลัก</a></li>
                /
                <strong class="breadvrumb-item active" style="margin-top: 10px;">รายละเอียดใบเบิก</strong>
            </ol>
        </div>
    </div>
</div>
<div class="ibox wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox-content">
        <div class="row">
            @foreach ($requisitions as $requisition)
            <div class="col-lg-6">
                <span>
                    <h2>ใบเบิกสินค้า เลขที่ ({{ $requisition->code }})</h2>
                </span>
            </div>
            <div class="col-lg-6">
                <span class="pull-right">
                    <a href="{{ route('inv.form-edit', $requisition->id) }}" class="btn btn-warning waves-effect" style="font-size: 14px;">
                        แก้ไขใบเบิกสินค้า
                    </a>
                    <a href="{{ route('inv.delete-store', $requisition->id) }}" class="btn btn-danger waves-effect" style="font-size: 14px;" data-method="delete" data-confirm="ยืนยันการยกเลิกใบเบิกสินค้า">
                        ยกเลิกใบเบิกสินค้า
                    </a>
                </span>
            </div>
            @endforeach
        </div>
        <div class="row animated fadeInRight" style="margin-top: 1%">
            <div class="ibox-content form-control">
                @foreach ($requisitions as $requisition)
                    <div class="row" style="margin-left: 15%">
                        <div class="row col-md-10" style="margin-top: 1%">
                            วันที่เอกสาร :: {{ $document_at = $requisition->document_at }}
                            {{-- {{ $document_at  = date("d/m/Y") }} --}}
                            <input type="hidden" name="document_at" value="{{ $document_at  }}">
                        </div>
                        <div class="row col-md-10" style="margin-top: 1%">
                            เบิกเพื่อ :: {{ $requisition->take->name }}
                        </div>
                        <div class="row col-md-10" style="margin-top: 1%">
                            หมายเหตุ :: {{ $requisition->detail }}
                        </div>
                        <div class="row col-md-10" style="margin-top: 1%">
                            คลัง :: {{ $requisition->warehouse->name }}
                        </div>
                        <div class="row col-md-10" style="margin-top: 1%">
                            <input type="hidden" name="warehouse_id" value="{{ $requisition->warehouse->name }}">
                            ผู้บันทึก :: {{ $user = auth()->user()->name }}
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="ibox wrapper wrapper bg-white animated fadeInRight">
        <div class="ibox-content">
            <table class="table table-bordered" id="tableReportBillDetall">
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

@endsection
@section('script')
<script src="/js/plugins/dataTables/datatables.min.js"></script>
<script type="text/javascript">

    $(function(){
        $('.table').dataTable({
            pageLength: 25,
            responsive: true,
        });
    });
</script>
@endsection
