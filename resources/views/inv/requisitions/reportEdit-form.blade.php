<?php
    use Crabon\Carbor;
?>
@extends('layouts-inv.app')
@section('content')

<div class="container" style="width: 100%">

        <div class="row">
            <div class="ibox-title col-lg-12">
                <p><h3>แก้ไขข้อมูลใบเบิกสินค้า</h3><p>
                <span>หน้าหลัก/แก้ไขข้อมูลใบเบิกสินค้า</span>
            </div>
        </div><br>
        <form action="#">
            {{-- Form edit report--}}
            <div class="row">
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
                                    <a href="#" class="btn btn-warning waves-effect" style="font-size: 14px;">
                                        บันทึก
                                    </a>
                                    <a href="{{ route('inv.report-bill-detail', $requisition->id ) }}" class="btn btn-danger waves-effect" style="font-size: 14px;" data-method="delete" data-confirm="ยืนยันการยกเลิกใบเบิกสินค้า">
                                        ยกเลิก
                                    </a>
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-lg-6" style="text-align: center;">
                            @foreach ($requisitions as $requisition)
                                <p>วันที่เอกสาร ::
                                    <span>
                                        <input type="hidden" class="form-control" value="{{ $requisition->code }}">
                                    {{ $requisition->code }}
                                    </span>
                                </p>
                                <p>คลัง ::
                                    <select class="form-control" name="edit_report_warehouse" id="editReportWarehouse" required>
                                        <option value="{{ $requisition->warehouse->name }}" >
                                            {{ $requisition->warehouse->name }}
                                        </option>
                                    </select>
                                    {{-- <input type="text" name="edit_report_warehouse" class="form-control" value="{{ $requisition->warehouse->name }}" required></p> --}}
                                    <p>ผู้บันทึก ::
                                    <input type="text" name="edit_report_user_name" class="form-control" value="{{ $requisition->createUser->name }}"></p>
                            @endforeach
                            </div>
                            <div class="col-lg-6">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox wrapper wrapper bg-white animated fadeInRight">
                {{-- Table Good Append  --}}
                @include('inv.requisitions.table-good-append')
                {{-- Modal Type Select Goods --}}
                @include('inv.modalGoodSelect')
            </div>
            <div class="ibox" style="margin-top: 30px;">
                <div class="ibox-content col-lg-12">
                    <label class="col-lg-12">ประเภทการเบิก <font color="red">*</font></label><br>
                    <select class="form-control" name="take_id" id="take_id">
                        <option value="">--- ประเภทการเบิก ---</option>
                        @foreach ($take_lists as $take)
                                <option value="{{ $take->id }}">{{ $take->name }}</option>
                        @endforeach
                    </select><br>
                    <label class="col-lg-12">หมายเหตุ</label><br><center>
                        <textarea style="width: 100%" name="detail" id="detail" cols="122" rows="6"></textarea>
                    </center><br>
                    <p>
                        <span class="pull-right">
                            <button type="button" onclick="onSubmitRequisition()" id="buttonSubmit" class="btn btn-primary">บันทึก</button>
                            <button class="btn btn-danger waves-effect ajaxify" type="button"
                            onclick="location.href='{{ route('inv.index') }}'">ยกเลิก</button>
                        </span>
                    </p>

                </div>
        </form>
</div>
{{-- Table Show Good Select Append --}}
@include('inv.extra-good')

@endsection
@section('script')
<script type="text/javascript">

	function onSubmitRequisition(button) {
		$('#buttonSubmit').prop( "disabled", true );
		var take = document.getElementById('take').value;
		if (take == '') {
			alert('กรุณาเลือกประเภทการเบิก');
			$('#buttonSubmit').prop( "disabled", false );
		}else{
			$('#form').submit();
		}
	}

	amount_limit = [];
	ratio_cost = [];

	function displayNumbers() {
		var numbers = 5; // replace 9 with your desire number
		var r = '';
		for (var i = 0; i < numbers; i++) {
			r += parseInt((Math.random() * 100), 10);
		}
		return r;
	}

	function formatNumber(number) {
		var number = number.toFixed(2) + '';
		var x = number.split('.');
		var x1 = x[0];
		var x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}

	function addRow() {

		var row = displayNumbers();
		var num = $('tr[data-attr=row]').length;
		num++;
		var html = '<tr data-attr="row" data-id="' + row +
			'"> <td class="col-lg-2"> <div class="input-group"> <div class="fg-line"> <input type="text" readonly class="form-control" id="code_' +
			row + '" /> <input type="hidden" class="form-control" name="good_id[]" id="id_' + row +
			'" /> </div> <span class="input-group-addon last" id="icon_' + row + '" onclick="getGoodModal(' + row +
			')"  style="cursor: pointer"> <i class="fa fa-search"></i> </span> </div> </td> <td class="col-lg-2"> <div class="fg-line coil-select_' +
			row + '"> <input type="hidden" name="coil_code[]" id="coil_id_' + row +
			'" /> <input type="text" readonly class="form-control" id="coil_code_' + row +
			'" /> </div> </td> <td> <label id="name_' + row +
			'"></label> </td> <td class="col-lg-1"> <div class="fg-line"> <input type="text" class="form-control" name="amount[]" id="goods_number_' +
			row + '" value="0.00" onkeyup="totalCost(' + row +
			')" /> </div> </td> <td class="col-lg-1"> <input type="hidden" name="unit_id[]" id="unit_' + row +
			'"> <div class="fg-line"> <input type="text" class="form-control" id="good_unit_' + row +
			'" readonly /> </div> </td> <td class="col-lg-1"> <div class="fg-line"> <label id="ratio_cost_' + row +
			'"></label> </div> </td> <td class="col-lg-1"> <div class="fg-line"> <input type="text" data-type="number_decimal" class="form-control" name="cost[]" id="cost_' +
			row +
			'" readonly /> </div> </td> <td class="col-lg-2"> <div class="fg-line"> <button type="button" name="button" class="btn btn-info" onClick="showModal(' +
			row + ')">ดูต้นทุน</button> <button type="button" name="button" class="btn btn-danger"onClick="deleteGoods(' +
			row + ')">ลบ</button> </div> </td> </tr>';
		$('#goodAppend').append(html);
		$('input[data-type=number]').number(true, 0);
	}

	function deleteGoods(row) {

		var amount = $('#goods_amount_row_' + row + '').val();

		var result_final = $('#goods_amount_real_final').val();

		var result_final_2 = parseInt(result_final) - parseInt(amount);

		var result_sum_format = formatNumber(parseInt(result_final) - parseInt(amount));

		for (i = 1; i < 4; i++) {
			$('#goods_amount_all_' + i + '').val(result_sum_format);
		}

		$('#goods_amount_all_final').val(result_sum_format);

		$('tr[data-id=' + row + ']').remove();

		$('#goods_amount_real_final').val(result_final_2)

	}

	function totalCost(row) {
		var amount = +$('#goods_number_' + row).val();
		var sum = 0;
		var i = 0;

		for (i = 0; i < amount_limit[row].length; i++) {
			if (amount <= amount_limit[row][i]) {
				sum = amount * ratio_cost[row][i];
				amount = 0;
				break;
			} else {
				sum = amount_limit[row][i] * ratio_cost[row][i];
				amount = amount - amount_limit[row][i];
			}
		}

		if (amount == 0) {
			$('#cost_' + row).val(sum.toFixed(2));
		} else {
			alert('คุณกรอกจำนวนเกินจำนวนสูงสุด');
			$('#goods_number_' + row).val(0.00);
		}
	}

	function showModal(row) {
		var rows = "";
		$("#tbodyratio").empty();
		var amount = +$('#goods_number_' + row).val();
		var i = 0;
		var sum = +$('#cost_' + row).val();
		if (amount <= amount_limit[row][i]) {
			rows += "<tr><td>" + amount + "</td>";
			rows += "<td>" + +ratio_cost[row, i] + "</td>";
			rows += "<td>" + (amount * ratio_cost[row, i]).toFixed(2) + "</td></tr>";
		} else {
			rows += "<tr><td>" + amount_limit[row][i] + "</td>";
			rows += "<td>" + ratio_cost[row, i] + "</td>";
			rows += "<td>" + (amount_limit[row][i] * ratio_cost[row, i]).toFixed(2) + "</td></tr>";
			amount = amount - amount_limit[row][i];
			i = i + 1;
			rows = addModal(rows, amount, i);
		}

		rows += "<tr><td colspan='2' style='font-weight:bold;'>รวม</td><td>" + sum + "</td></tr>";
		$(rows).appendTo("#showRatio tbody");
		$('#ratioModal').modal('show');
	}

	function addModal(rows, amount, i) {
		if (!!amount_limit[row][i]) {
			if (amount <= amount_limit[row][i]) {
				rows += "<tr><td>" + amount + "</td>";
				rows += "<td>" + ratio_cost[row, i] + "</td>";
				rows += "<td>" + (amount * ratio_cost[row, i]).toFixed(2) + "</td></tr>";
				return rows;
			} else {
				rows += "<tr><td>" + amount_limit[row][i] + "</td>";
				rows += "<td>" + ratio_cost[row, i] + "</td>";
				rows += "<td>" + (amount_limit[row][i] * ratio_cost[row, i]).toFixed(2) + "</td></tr>";
				amount = amount - amount_limit[row][i];
				i = i + 1;
				rows = addModal(rows, amount, i);
				return rows;
			}
		} else {
			alert('คุณกรอกจำนวนเกินจำนวนสูงสุด');
			$('#goods_number_' + row).val('0.00');
		}
	}

</script>
@endsection
