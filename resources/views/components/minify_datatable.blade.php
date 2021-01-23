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

<table id="{{$name}}_datatable" class="table table-striped table-bordered datatable" style="width:100%; border-collapse: collapse !important;">
    <thead>
	    <tr>
	    	@php
	    		$headerColumns    = [];
				$columnDefs = [];
				$order      = isset($order) ? $order : [];
	    	@endphp
	    	@foreach($columns as $key => $column)
	    		@php
	    			$headerColumns[] = ['data' => $column['name']];
	    			if(isset($column['columnDefs'])){
		    			$columnDefs[] = array_merge($column['columnDefs'],['targets' => $key]);
	    			}
	    		@endphp
		    	<th>{{isset($column['alias']) ? $column['alias'] : ucwords(str_replace('_', ' ', $column['name']))}}</th>
	    	@endforeach
	    </tr>
	</thead>
</table>

<!-- Datatable JS -->
@section('datatable-js')
	<script src="{{asset('assets/formbuilder/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.fixedColumns.min.js')}}"></script>
@endsection

@prepend('additional-js')
	<script type="text/javascript">
		var {{$name}}_tableId           = "{{$name}}_datatable";
		var {{$name}}_tableElement      = $('#'+{{$name}}_tableId);
		var {{$name}}_tableUrl          = "{{ $url ?? route($newRoute. '.datatable')}}";
		var {{$name}}_tableColumns      = {!! json_encode($headerColumns) !!};
		var {{$name}}_useFilter         = {{ $useFilter ?? 'false' }};
		var {{$name}}_tableColumnDefs   = {!! json_encode($columnDefs) !!};
		{{-- var {{$name}}_tableOrder        = {!! json_encode($order) !!}; --}}
		{{-- var {{$name}}_tableLengthMenu   = {!! json_encode($lengthMenu) !!}; --}}
		{{-- var {{$name}}_tableFixedColumns = {!! json_encode($fixedColumns) !!}; --}}

		$(document).ready(function() {
			{{$name}}_callSetupTable();
		});

	    function {{$name}}_reloadTable() {
	        $("#{{$name}}_datatable").DataTable().ajax.reload(null, false );
	    }

	    function {{$name}}_callSetupTable(useFilter = true) {
			{{$name}}_setupTable({{$name}}_tableId, {{$name}}_tableElement, {{$name}}_tableColumns, {{$name}}_tableColumnDefs, {{$name}}_tableUrl, {{$name}}_useFilter);
	    }

		function {{$name}}_setupTable(tableId, tableElement, tableColumns, tableColumnDefs, tableUrl, useFilter = false) {
		    if($.fn.dataTable.isDataTable('#'+tableId)){
		        tableElement.DataTable().clear();
		        tableElement.DataTable().destroy();
		    }

		    var filters = {};
		    if (useFilter) {
		    	var filterForm = $('#filter_form{{$name}}').serializeArray();
		    	$.each(filterForm, function(key, val){
		    		if (val.name != "_token" && val.value != "") {
			    		filters[val.name] = val.value;
		    		}
		    	});
		    }

		    var {{$name}}_table = tableElement.DataTable({
		        processing:     true,
		        serverSide:     true,
		        rowId:          "row_id",
		        searching: 		false,
		        // scrollX:        true,
		        initComplete: function (settings, json) {  
					$("#"+tableId).wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");            
				},
		        scrollCollapse: true,
		        lengthChange: false,
		        // fixedColumns:   tableFixedColumns,
		        // lengthMenu: 	false,
		        // order:          tableOrder,
		        columnDefs:     tableColumnDefs,
		        ordering: false,
		        autoWidth: true,
		        columns:        tableColumns,
		        ajax: {
		            url: tableUrl,
		            type: 'POST',
		            data: filters,
		            dataType: 'json',
		            error: function (data) {
		                swal("Error!", "Failed to load Data!", "error");
		            },
		            dataSrc: function(result){
		                this.data = result.data;
		                return result.data;
		            },
		        }
		    });

		    // new $.fn.dataTable.FixedColumns( {{$name}}_table );

		    $('#{{$name}}_datatable').on('draw.dt', function () {
                $('[data-tooltip="tooltip"]').tooltip();
            });

			$('#{{$name}}_datatable_wrapper .row:eq(2)').css('margin-top', '10px');
			$('#{{$name}}_datatable_wrapper .dataTables_length').css('margin-top', '10px');
			$('.dts_label').html('');
		}

	</script>
@endprepend