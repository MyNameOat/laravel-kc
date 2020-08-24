
@extends('layouts-inv.app')
@section('content')

<div class="row">
    <div class="ibox wrapper wrapper bg-white animated fadeInRight">
        <div class="ibox-title" style="height: 120px; margin-top: 5px;">
            <span><h2>ใบเบิกสินค้า</h2></span>
            <ol class="breadcrumb">
                <li class="breadvrumb-item"><a href="{{ route('inv.index') }}">หน้าหลัก</a></li>
                /
                <strong class="breadvrumb-item active" style="margin-top: 10px;">ใบเบิกสินค้าทั้งหมด</strong>
            </ol>
            <span class="pull-right">
                {{-- <a href="{{ route('inv.form-create') }}" class="btn btn-primary">
                <h4>สร้างใบเบิกสินค้า</h4>
                </a> --}}
            </span>
        </div>
    </div>
</div>

<div class="row wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox" style="margin-top: 30px;">
        <div class="col-lg-12">
            <div class="ibox-content">
                <label style="background-color: white;">
                    <h3>รายการสินค้า</h3>
                </label>
                <span class="pull-right">
                    <a href="{{ route('inv.form-create') }}" class="btn btn-primary">
                    <h4>สร้างใบเบิกสินค้า</h4>
                    </a><br><br>
                </span>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>คลังสินค้าที่เบิก</th>
                            <th>เลขที่เอกสาร</th>
                            <th>วันที่เอกสาร</th>
                            <th>วัตถุประสงค์การเบิก</th>
                            <th>ผู้บันทึก</th>
                            <th>ผู้อนุมัติ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requisitions as $requisition)
                        <tr>
                            <td>{{ $requisition->warehouse->name }}</td>
                            <td>{{ $requisition->code }}</td>
                            <td>{{ $requisition->document_at }}</td>
                            <td>{{ $requisition->take->name }}</td>
                            <td>
                                @if($requisition->created_user_id == 1)
                                <span class="label label-primary">Admin</span>
                                @else
                                <span class="label label-primary">{{ $requisition->createUser->username }}</span>
                                @endif
                            </td>
                            <td>
                                @if($requisition->approve_user_id == 0 && $requisition->none_approve_user_id == 0)
                                <span class="label label-warning">รอการตรวจสอบ</span>
                                @elseif($requisition->approve_user_id == 1)
                                <span class="label label-primary">Admin</span>
                                @elseif($requisition->approve_user_id > 1)
                                <span class="label label-primary">{{ $requisition->approveUser->username }}</span>
                                @elseif($requisition->none_approve_user_id == 1)
                                <span class="label label-danger">ไม่อนุมัติ/Admin</span>
                                @elseif($requisition->none_approve_user_id > 1)
                                <span
                                    class="label label-danger">ไม่อนุมัติ/{{ $requisition->noneApproveUser->username }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inv.report-store-detail', $requisition->id)  }}"
                                class="btn btn-info btn-xs">แสดงรายละเอียด</a>
                            </td>
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

    $('#warehouse').change(function () {
		var txt = $("#warehouse option:selected").text();
		var res = txt.split("_", 1);
		$("#code").val(res + 'xxxx-xxx');
	});

</script>
@endsection
