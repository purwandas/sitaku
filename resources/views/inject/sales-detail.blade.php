@php
$identity    = "detailModal";

$name        = $name ?? "sales";
$tableDetail = $name."Datatable";
$tableRoute  = $name.".datatable-detail";
@endphp

@push('modal-element')
<div class="modal fade" id="{{$identity}}" tabindex="-1" role="dialog" aria-labelledby="{{$identity}}" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Detail Product</h5>
				<button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body" style="overflow-x: auto;height: 500px;">
				<table id="{{$tableDetail}}" class="table table-striped table-bordered datatable dataTable no-footer" style="width: 100%;">
					<thead>
						<tr>
							<th>Product</th>
							<th>Unit</th>
							<th>Price</th>
							<th>Qty</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" type="button" data-dismiss="modal">Close</button>
			</div>
		</div>
		<!-- /.modal-content-->
	</div>
	<!-- /.modal-dialog-->
</div>
@endpush
<!-- Datatable CSS -->
@section('datatable-css')
<link href="{{ asset('assets/formbuilder/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style type="text/css">
	.DTFC_LeftBodyLiner{
		overflow-y:unset !important;
		top: -13px !important;
		width: inherit !important;
	}
    .DTFC_RightBodyLiner{
    	overflow-y:unset !important
    }
    table.dataTable.table-striped.DTFC_Cloned tbody tr:nth-of-type(odd) {
    	background-color: #F3F3F3;
	}
	table.dataTable.table-striped.DTFC_Cloned tbody tr:nth-of-type(even) {
	    background-color: white;
	}
	table.dataTable.table-striped.DTFC_Cloned thead tr{
    	background-color: white;
	}
</style>
@endsection
<!-- Datatable JS -->
@section('datatable-js')
	<script src="{{asset('assets/formbuilder/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.fixedColumns.min.js')}}"></script>
@endsection
@prepend('additional-js')
<script type="text/javascript">
	function detailModal(id, ) {
		$('#{{$identity}}').modal('show');
		destroyDatatable();
		initDatatable(id);
	}

	function initDatatable(id) {
		$('#{{$tableDetail}}').DataTable( {
	        "ajax": {
	        	"url": routeGlobal() + "/" + id,
		        "type":"POST",
		    },
	        "columns": [
	            { "data": "product" },
	            { "data": "unit" },
	            { "data": "price" },
	            { "data": "qty" },
	            { "data": "total" },
	        ]
	    } );
	}

	function routeGlobal() {
		return "{{ route($tableRoute) }}";
	}

	function destroyDatatable() {
		$('#{{$tableDetail}}').DataTable().clear();
		$('#{{$tableDetail}}').DataTable().destroy();
	}
</script>
@endprepend