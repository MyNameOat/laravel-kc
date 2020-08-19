@extends('layouts-inv.app')
@section('content')

<div class="row">
    <div class="ibox">
        <div class="col-lg-12">
            <div class="ibox-title" style="height:120px;">
                <span><h1>รายงานอนุมัติใบเบิก</h1></span>
                <span>หน้าหลัก/รายงานอนุมัติใบเบิก</span>
            </div>
            <div class="ibox-content">
                <label style="background-color: white;">
                    <h3>รายการสินค้า</h3>
                </label>
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
                                <span class="label label-primary">อนุมัติ/Admin</span>
                                @elseif($requisition->approve_user_id > 1)
                                <span class="label label-primary">{{ $requisition->approveUser->username }}</span>
                                @elseif($requisition->none_approve_user_id == 1)
                                <span class="label label-danger">ไม่อนุมัติ/Admin</span>
                                @elseif($requisition->none_approve_user_id > 1)
                                <span
                                    class="label label-danger">ไม่อนุมัติ{{ $requisition->noneApproveUser->username }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inv.approve-detail', $requisition->id)  }}"
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
