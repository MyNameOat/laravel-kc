<table style="display: none;">
	<tbody>
		<tr class="addTr">
			<input type="hidden" name="coil_code[]" class="coil_code" >
            <input type="hidden" name="good_id[]" class="good_id" >
			<td>
				<span class="good_code"></span>
				<input type="hidden" name="warehouse_good_id[]" class="warehouse_good_id">
			</td>
			<td><span class="good_name"></span></td>
			<td><input type="text" name="amount[]" class="form-control amount" value=""></td>
			<td><span class="unit_name" name="unit_name[]"></span></td>
			<td><span class="amount_in_warehouse"></span></td>
			<td><button type="button" name="button" class="btn btn-danger" onClick="deleteRow(this)">ลบ</button></td>
		</tr>
	</tbody>
</table>
