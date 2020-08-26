
@extends('layouts-inv.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('css')
    <link href="/css/plugins/dataTables/datatables.min.css" rel="stylesheet">
@endsection

@section('content')

<div class="row wrapper wrapper bg-white animated fadeInRight">
    <div class="ibox">
        <div class="ibox-title" style="height: 120px; margin-top: 5px;">
            <div class="col-sm-6">
                <span><h2>ใบเบิกสินค้า</h2></span>
                <ol class="breadcrumb">
                    <li class="breadvrumb-item"><a href="{{ route('inv.index') }}">หน้าหลัก</a></li>
                    /
                    <strong class="breadvrumb-item active" style="margin-top: 10px;">ใบเบิกสินค้าทั้งหมด</strong>
                </ol>
            </div>
            <div class="col-sm-6">
                <span class="pull-right" style="margin-top: 25px; margin-right: 20px;">
                    <a href="{{ route('inv.form-create') }}" class="btn btn-primary">
                        <i class="fa fa-edit"></i>
                        สร้างใบเบิกสินค้า
                    </a>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="ibox row wrapper wrapper bg-white animated fadeInRight">
        <div class="ibox-content col-lg-12">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>คลังสินค้าที่เบิก</th>
                    <th>เลขที่เอกสาร</th>
                    <th>วันที่เอกสาร</th>
                    <th>วัตถุประสงค์การเบิก</th>
                    <th class="text-center" width="8%">ผู้บันทึก</th>
                    <th class="text-center" width="10%">ผู้อนุมัติ</th>
                    <th class="text-center" width="10%">รายละเอียด</th>
                    <th class="text-center" width="7%">ลบ</th>
                </tr>
                </thead>
                <tbody>
                @foreach($requisitions as $requisition)
                    <tr>
                        <td>{{ $requisition->warehouse->name }}</td>
                        <td>{{ $requisition->code }}</td>
                        <td>{{ $requisition->document_at->format('d/m/Y') }}</td>
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
                            <a href="{{ route('inv.report-store-detail', $requisition->id) }}"
                               class="btn btn-info btn-xs">แสดงรายละเอียด</a><br>

                        </td>
                        <td>
                            <button class="btn btn-danger btn-xs delete_user" data-id="{{ $requisition->id }}">Delete {{ $requisition->id }}</button>
                        </td>
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

    $(document).on('click','.delete_user',function(){
        var id = $(this).attr('data-id');
        var token = $("meta[name='csrf-token']").attr("content");
            $.ajax({
        type: 'POST',
        url: "/inv/requisition/report-status/delete/"+id,

        data: {
            _token: token,
            _method: 'POST',
            id: id,
        },
            success: function (response) {
                console.log("ASD");

            }
        });
        window.location.reload();
    });

</script>
@endsection
