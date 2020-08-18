<div class="tabs-container" style="margin-top: 40px; width:90%;">
    <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#detail" aria-controls="detail" role="tab" data-toggle="tab">รายการสินค้า</a></li>
        <span class="pull-right">
            <li class="active">
                <a type="button" class="btn btn-primary" name="btn_GoodModal" onclick="getGoodModal()">เพิ่มสินค้า</a>
            </li>
        </span>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="detail">
            <div class="panel-body">
                <table class="table table-bordered" width="100%">
                    <thead>
                        <tr>
                            <th class="text-center">รหัสสินค้า</th>
                            <th class="text-center">ชื่อสินค้า</th>
                            <th class="text-center col-sm-2">จำนวน</th>
                            <th class="text-center">หน่วยนับ</th>
                            <th class="text-center">จำนวนคงเหลือในคลัง</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="goodAppend">
                        @if(isset($requisitionGoods))
                        @foreach($requisitionGoods as $key => $requisitionGood)
                            <tr>
                                <td>
                                    @if($requisitionGood->good->type->is_coil == 1)
                                    {{ $requisitionGood->warehouseGood->coil_code }}
                                    <input type="hidden" name="wh_good_coil[]" value="{{ $requisitionGood->warehouseGood->coil_code }}">

                                    @else
                                    {{ $requisitionGood->good->code }}
                                    <input type="hidden" name="good_code[]" value="{{ $requisitionGood->good->code }}">

                                    @endif
                                </td>

                                <td>{{ $requisitionGood->good->name }}
                                    <input type="hidden" name="good_name[]" value="{{ $requisitionGood->good->name }}">
                                </td>
                                    <input type="hidden" name="good_id[]" value="{{ $requisitionGood->good->id }}">
                                </td>
                                <td><input type="text" name="edit_amount[]" value="{{ $requisitionGood->amount }}">
                                </td>
                                <td>{{ $requisitionGood->good->unit->name }}
                                    <input type="hidden" name="good_unit_id[]" value="{{ $requisitionGood->good->unit->id }}">
                                </td>
                                <td>
                                    {{ $requisitionGood->warehouseGoodBalance->sum('amount') }}
                                    <input type="hidden" name="good_balance_amount[]" value="{{ $requisitionGood->warehouseGoodBalance->amount }}"></td>
                                <td style="text-align: center;">
                                    <button type="button" name="button" class="btn btn-danger" onClick="deleteRow(this)">ลบ
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @else

                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('inv.modal-good-select')
