<table class="table table-bordered" id="stockTable">
    <thead>
    <tr>
        <th>ประเภทสินค้า</th>
        <th>รหัสสินค้า</th>
        <th>ชื่อสินค้า</th>
        <th>จำนวนคงเหลือ</th>
        <th>หน่วยนับ</th>
        <th>ชุดคำสั่ง</th>
    </tr>
    </thead>
    <tbody>
    @foreach($goodViews as $goodView)
        <tr>
            <input type="hidden" value="{{ $goodView->good_id }}" class="stock_good_id">
            <input type="hidden" value="{{ $goodView->coil_code }}" class="stock_coil_code">
            <input type="hidden" value="{{ $goodView->good->code }}" class="stock_good_code">
            <input type="hidden" value="{{ $goodView->good->name }}" class="stock_good_name">
            <input type="hidden" value="{{ $goodView->balance_amount }}" class="stock_balance_amount">
            <input type="hidden" value="{{ $goodView->good->unit->name }}" class="stock_unit_name">

            <td>{{ $goodView->good->type->name }}</td>
            <td>{{ $goodView->coil_code != null ? $goodView->coil_code : $goodView->good->code }}</td>
            <td>{{ $goodView->good->name }}</td>
            <td>{{ $goodView->balance_amount }}</td>
            <td>{{ $goodView->good->unit->name }}</td>
            <td>
                <button type="button" class="btn btn-info btnGetStock">เลือก</button>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
