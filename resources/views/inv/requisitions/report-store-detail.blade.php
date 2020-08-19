<?php
    use Carbon\Carbon;
?>

@extends('layouts-inv.app')
@section('content')

<div class="row wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox">
        <div class="ibox-title">
            <p><h3>สร้างใบเบิกสินค้า</h3><p>
            <span>หน้าหลัก/สร้างใบเบิกสินค้า</span>
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
                        ยกเลิกใบเบิกสินค้า {{ $requisition->id }}
                    </a>
                </span>
            </div>
            @endforeach
        </div>
        <div class="row">
            @foreach ($requisitions as $requisition)
                <div class="ibox-content col-lg-12">
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-lg-4">
                            <label>วันที่เอกสาร :: {{ $document_at = $requisition->document_at }}
                                {{-- {{ $document_at  = date("d/m/Y") }} --}}
                                <input type="hidden" name="document_at" value="{{ $document_at  }}">
                            </label>
                        </div>
                        <div class="col-lg-4">
                            <p>เบิกเพื่อ :: {{ $requisition->take->name }}</p>
                            <p>เบิกเพื่อ :: <br>
                                <select name="take_id">
                                    <option value="{{ $requisition->take->id }}">{{ $requisition->take->name }}</option>
                                    @foreach($take_lists as $take_list)
                                    {
                                        <option value="{{ $take_list->id }}">{{ $take_list->name }}</option>
                                        @endforeach
                                    }
                                </select>
                            </p>
                            <p>หมายเหตุ :: {{ $requisition->detail }}</p>
                            <label>คลัง :: {{ $requisition->warehouse->name }}
                                <input type="hidden" name="warehouse_id" value="{{ $requisition->warehouse->name }}">
                            </label>
                        </div>
                        <div class="col-lg-4">
                            ผู้บันทึก :: {{ $user_create = auth()->user()->name }}
                            <input type="้hidden" name="user_create" value="{{ auth()->user()->id }}">
                        </div>
                    </div>
                </div>
            @endforeach
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
