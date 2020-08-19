@extends('layouts-center.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>เลือกคลังสินค้า</h5>
                    </div>
                    <div class="ibox-content">
                        <form action="{{ route('store-warehouse') }}" method="post" class="form-horizontal">
                            @csrf
                            <div class="form-group"><label class="col-sm-2 control-label">คลังสินค้า</label>
                                <div class="col-sm-10">
                                    <select class="form-control m-b select2_demo_1" name="warehouse_id">
                                        @if (!auth()->user()->hasRole('admin'))
                                            @foreach(auth()->user()->user_warehouses as $warehouse)
                                                <option value="{{ $warehouse->warehouse_id }}">{{ $warehouse->warehouse->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="button" onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" >บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
