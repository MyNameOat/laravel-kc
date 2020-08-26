@extends('layouts-inv.app')

@section('css')

    <link href="/css/plugins/dataTables/datatables.min.css" rel="stylesheet">

@endsection

@section('content')
<div class="row">
    <div class="ibox wrapper wrapper bg-white animated fadeInRight">
        <div class="ibox-title" style="height: 120px; margin-top: 5px;">
            <span><h2>ใบเบิกสินค้า</h2></span>
            <ol class="breadcrumb">
                <li class="breadvrumb-item"><a href="{{ route('inv.index') }}">หน้าหลัก</a></li>
                /
                <strong class="breadvrumb-item active" style="margin-top: 10px;">สร้างใบเบิกสินค้า</strong>
            </ol>
        </div>
    </div>
</div>
{{-- <form method="POST" action="{{ route('inv.save-store') }}" class="form-horizontal" id="FormSubmit"> --}}

<div class="row">
    <div class="ibox">
        <div class="ibox-content">
            <form action="{{ route('inv.save-store') }}" method="POST" id="form" class="form-horizontal">
                @csrf
                    @include('inv.requisitions.form')
            </form>
        </div>
    </div>
</div>
{{-- Table Show Good Select Append --}}
@include('inv.extra-good')
@endsection

@section('script')

@endsection
