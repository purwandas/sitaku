@push('content')
<div class="card">
	<div id="divHeader" style="display: none;">
		<table id="tableBerkasSummary" class="table table-striped table-responsive table-bordered">
			<tr>
				<th>Total Price</th>
				<td width="150px"></td>
			</tr>
		</table>
	</div>
</div>
@endpush
@push('additional-js')
<script type="text/javascript">
	function currency(number, type = 'IDR') {
		return type + ' ' + parseInt(number).toLocaleString();;
	}
</script>
@endpush