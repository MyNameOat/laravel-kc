@extends('layouts-inv.app')
@section('content')

<div class="ibox">
    <div class="ibox-title" style="width: 100%;">
        <p><h3>หน้าหลัก</h3><p>
    </div>
</div>

<div class="ibox">
    <div class="ibox-content" style="width: 100%;">
        <div class="ibox-content text-center" id="flot-dashboard-chart">
            {{-- Start Test Code --}}


            <span style=" text-center margin-top: 30%;">
                <a href="{{ route('inv.form-create') }}" class="btn btn-primary">
                    <i class="fa fa-search"></i> เบิกสินค้า
                </a>
            </span>

            <span style=" text-center margin-top: 30%;">
                <a href="#" class="btn btn-primary">
                    <i class="fa fa-diamond"></i> <span class="nav-label">อนุมัติใบเบิก
                 </a>
            </span>

            <span style=" text-center margin-top: 30%;">
                <a href="#" class="btn btn-primary">
                    <i class="fa fa-diamond"></i> <span class="nav-label">รายการใบเบิก
                </a>
            </span>


            {{-- End Test Code --}}
        </div>
    </div>
</div>


@endsection
@section('script')
<script>

</script>
@endsection
