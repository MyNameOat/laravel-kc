<table class="table table-bordered" id="goodTable">
    <thead>
    <tr>
        <th></th>
        <th>ประเภทสินค้า</th>
        <th>รหัสสินค้า</th>
        <th>ชื่อสินค้า</th>
        <th>หน่วยนับ</th>
    </tr>
    </thead>
    <tbody>
    @foreach($goods as $good)
        <tr>
            <input type="hidden" value="{{ $good->id }}" class="red_label_good_id">
            <input type="hidden" value="{{ $good->code }}" class="red_label_good_code">
            <input type="hidden" value="{{ $good->name }}" class="red_label_good_name">
            <input type="hidden" value="{{ $good->unit->name }}" class="red_label_unit_name">

            <td>
                <input type="checkbox" name="id[]" value="{{ $good->id }}">
            </td>
            <td>{{ $good->type->name }}</td>
            <td>{{ $good->code }}</td>
            <td>{{ $good->name }}</td>
            <td>{{ $good->unit->name }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
