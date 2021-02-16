@php
$useModal      = $useModal ?? false;
$disableInfo   = $disableInfo ?? false;
$name          = $name ?? @$model::toKey()['snake'];
$class         = $class ?? @$model::toKey()['class'];
$cRoute        = $cRoute ?? @$model::toKey()['route'];
$orderC        = $orderColumn ?? [];
$exceptForeign = @$exceptForeign ?? [];
@endphp

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

<!-- Datatable Element -->
<table id="{{$name}}_datatable" class="table table-striped table-bordered datatable" style="width: 100%; border-collapse: collapse !important;">
    <thead>
	    <tr>

	    	<!-- From Model -->
	    	@php	
			$except = $except ?? [];
			$except = is_array($except) ? $except : [];

			$additional = $additional ?? [];
			$arr = [];
			foreach($additional as $add){
				$arr[$add] = '';
			}

			$columns    = [];
			$custom     = @$custom ?? [];
			$button     = isset($button) ? $button : [];
			$order      = isset($order) ? $order : [];
			$newRoute   = $route ?? $cRoute;
			$columnDefs = isset($columnDefs) ? $columnDefs : [];
			$merged     = array_merge(@$useDatatableAction ? ['action'=>'', 'id'=>'ID'] : [], $model::rule());
			removeArrayByKey($merged, $except);

			$merged = array_merge($merged, $arr);
			$merged = array_keys($merged);

			foreach ($orderC as $key => $value) {
				moveArrayElement($merged, array_search($value, $merged), $key);
			}

	    	foreach($merged as $key){
				$column    = $key;

				$foreign   = isForeign($key, $exceptForeign);
				if ($foreign['status']) {
					$column    = $foreign['column'];
					$related   = getForeignClass($model, $foreign['column']);
					$label     = @$related::labelText()[0] ?? ['name'];
					$columns[] = ['data' => $column.'_'.$label];
				} elseif ($key == 'id') {
					$columns[] = ['data' => $key, 'visible' => false];
				} else {
					$columns[] = ['data' => $key];
				}

				if (array_key_exists($key, $custom)) {
					$column    = $custom[$key];
				} else {
					$column    = ucwords( str_replace("_", " ", $column) );
				}
				echo "<th>". $column ."</th>";
		    }
		    @endphp
		    <!-- End From Model -->


	    </tr>
    </thead>
</table>

<br><br>

<!-- Define Datatable Button -->
<div id="{{$name}}_datatableButton" style="display: none;">
	@if(@$setupDatatableBuilder['button-left'])
	{!! @$setupDatatableBuilder['button-left'] !!}
	@endif
	@if(@$setupDatatableBuilder['useDatatableAction'])
		@if(@$setupDatatableBuilder['creatable'])
			@if($setupDatatableBuilder['formPage'])
			<a href="{{route($newRoute. '.form')}}" class="btn btn-sm btn-primary">{!! getSvgIcon('fa-plus','mt-m-2') !!} Create</a>
			@elseif($useModal)
			<button class="btn btn-sm btn-primary" onclick="addModal{{$class}}()" data-toggle="modal" data-target="#modalForm{{$class}}">{!! getSvgIcon('fa-plus','mt-m-2') !!} Create</button>
			@else
			<a href="{{route($newRoute. '.create')}}" class="btn btn-sm btn-primary">{!! getSvgIcon('fa-plus','mt-m-2') !!} Create</a>
			@endif
		@endif
	@endif
	<div class="btn-group">
		@if( in_array('import', $button) || ($useUtilities && count($button) == 0) )
			@include('components.fn_upload', ['name' => $name, 'form_url' => route($cRoute.'.import'), 'template_url' => route($cRoute.'.import-template')])
		@endif
		@if($useUtilities)
			<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{!! getSvgIcon('fa-download','mt-m-2') !!} Export</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				@if( in_array('export-xls', $button) || ($useUtilities && count($button) == 0) )
					@include('components.fn_download', ['url' => $cRoute.'.export-xls', 'type' => 'xls', 'icon' => 'fas fa-file-excel'])
				@endif
				@if( in_array('export-pdf', $button) || ($useUtilities && count($button) == 0) )
					@include('components.fn_download', ['url' => $cRoute.'.export-pdf', 'type' => 'pdf', 'icon' => 'fas fa-file-pdf'])
				@endif
		  	</div>
	  	@endif
		@if( in_array('job-status', $button) || ($useUtilities && count($button) == 0) )
			@include('components.job_trace_modal', ['buttonId' => 'jobTrace', 'name' => $class, 'module' => @$module])
			<button class="btn btn-sm btn-danger" id="jobTrace" onclick="showJobStatus()">
				{!! getSvgIcon('fa-list','mt-m-2') !!} Queue Status
			</button>
		@endif
	</div>
	@if(@$setupDatatableBuilder['button-right'])
	{!! @$setupDatatableBuilder['button-right'] !!}
	@endif
</div>
<!-- End Define-->

<!-- Modal Element -->
@push('modal-element')
	<div class="modal fade" id="modalDetailContent" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog modal-lg" role="document" style="min-width: 80%;margin-left: 11%">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title mt-0">Content</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">×</span>
	                </button>
	            </div>
	            <div class="modal-body">
	            	<div style="overflow:auto">
		            	<div id="detailContent"></div>
		            </div>
	            </div>
	            <div class="modal-footer">
		            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="modal fade" id="modalFileViewer" tabindex="-1" role="dialog" aria-hidden="true">
	    <div class="modal-dialog modal-lg" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title mt-0">File Viewer</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">×</span>
	                </button>
	            </div>
	            <div class="modal-body">
	            	<div id="fileViewer" class="carousel slide" data-ride="carousel" data-pause="hover" data-interval="false">
						
					</div>
	            </div>
	            <div class="modal-footer">
		            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>
@endpush

<!-- Datatable JS -->
@section('datatable-js')
	<script src="{{asset('assets/formbuilder/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.bootstrap4.min.js')}}"></script>
	<script src="{{asset('assets/formbuilder/datatables/dataTables.fixedColumns.min.js')}}"></script>
@endsection

@push('additional-js')
	<script type="text/javascript">
		function viewDetailContent(url,column){
			$.ajax({
	            url   : url,
	            type  : 'GET',
	            success: function (data) {
	                if (!data.status) {
	                    console.log('there was something error');
	                    swal({
		                	title: "Gagal melakukan request",
		                	text: "Silahkan hubungi admin",
		                	type: "error"
		                });
	                    return;
	                }
	                $('#detailContent').html('');
					$('#detailContent').html(data.data[column]);
					$('#modalDetailContent').modal('show');
	            },
	            error: function(xhr, textStatus, errorThrown){
	                swal({
	                	title: "Gagal melakukan request",
	                	text: "Silahkan hubungi admin",
	                	type: "error"
	                });
	            }
	        });
		}

		function showFileViewer(url,column = 'default'){
			$.ajax({
	            url   : url,
	            type  : 'GET',
	            success: function (data) {
	                if (!data.status) {
	                    console.log('there was something error');
	                    swal({
		                	title: "Gagal melakukan request",
		                	text: "Silahkan hubungi admin",
		                	type: "error"
		                });
	                    return;
	                }

	                var files;
	                if(column === 'default'){
		                if(!Array.isArray(files)){
		                	files = [data.data];
		                }
	                }else{
	                	files = data.data[column];
	                	if(!Array.isArray(files)){
		                	files = [files];
		                }
	                }
	                
	                var imageType = ['jpg','JPG','jpeg','JPEG','png','PNG'];
	                var videoType = ['mp4','MP4','ogg','OGG','flv','FLV'];
	                var el = '';
	                el += '<ol class="carousel-indicators">';
	                $.each(files,function(k,v){
	                	if(k==0){
		                	el += '<li data-target="#fileViewer" data-slide-to="'+k+'" class="active"></li>';
	                	}else{
		                	el += '<li data-target="#fileViewer" data-slide-to="'+k+'"></li>';
	                	}
	                });
	                el += '</ol>';
	                el += '<div class="carousel-inner">';
	                $.each(files,function(k,v){
	                	if(k==0){
		                	el += '<div class="carousel-item active">';
	                	}else{
		                	el += '<div class="carousel-item">';
	                	}

	                	if(imageType.indexOf(v.type) >= 0){
		                	el += '<img class="d-block w-100" src="'+v.path+'">';
	                	}

	                	if(videoType.indexOf(v.type) >= 0){
	                		el += '<video class="d-block w-100" controls>'+
	                            '<source src="'+v.path+'" />'+
	                        '</video>';
	                	}

	                	el += '</div>';
	                });
	                el += '</div>';
				  	el += '<a class="carousel-control-prev" href="#fileViewer" role="button" data-slide="prev">'+
				    	'<span class="carousel-control-prev-icon" aria-hidden="true"></span>'+
				    	'<span class="sr-only">Previous</span>'+
				  	'</a>'+
				  	'<a class="carousel-control-next" href="#fileViewer" role="button" data-slide="next">'+
				    	'<span class="carousel-control-next-icon" aria-hidden="true"></span>'+
				    	'<span class="sr-only">Next</span>'+
				  	'</a>';

	                $('#fileViewer').html('');
					$('#fileViewer').html(el);
					$('#modalFileViewer').modal('show');
	            },
	            error: function(xhr, textStatus, errorThrown){
	                swal({
	                	title: "Gagal melakukan request",
	                	text: "Silahkan hubungi admin",
	                	type: "error"
	                });
	            }
	        });
		}
	</script>
@endpush

@prepend('additional-js')
	<script type="text/javascript">
		var {{$name}}_tableId         = "{{$name}}_datatable";
		var {{$name}}_tableElement    = $('#'+{{$name}}_tableId);
		var {{$name}}_tableUrl        = "{{ $customDatatableUrl ?? route($newRoute. '.datatable')}}";
		var {{$name}}_tableOrder      = {!! json_encode($order) !!};
		var {{$name}}_tableColumnDefs = {!! json_encode($columnDefs) !!};
		var {{$name}}_tableColumns    = {!! json_encode($columns) !!};
		var {{$name}}_useFilter    	  = {{ @$useFilter ? $useFilter : 'false' }};
		var {{$name}}_tableLengthMenu = {!! json_encode($lengthMenu) !!};
		var {{$name}}_tableFixedColumns = {!! json_encode($fixedColumns) !!};

		$(document).ready(function() {
			{{$name}}_setupTable({{$name}}_tableId, {{$name}}_tableElement, {{$name}}_tableOrder, {{$name}}_tableColumnDefs, {{$name}}_tableColumns, {{$name}}_tableUrl, {{$name}}_tableLengthMenu, {{$name}}_tableFixedColumns, {{$name}}_useFilter);
		});

	    $('#{{$name}}_datatable').on('click', '.js-swal-delete', function () {
			var deleteUrl = $(this).data("url");
	        swal({
	            title: 'Are you sure?',
	            text: 'You will not be able to recover this data!',
	            type: 'warning',
	            showCancelButton: true,
	            confirmButtonText: 'Yes, delete it!',
	            html: false,
	            preConfirm: function() {
	                return new Promise(function (resolve) {
	                    setTimeout(function () {
	                        resolve();
	                    }, 50);
	                });
	            }
	        }).then(function(result){
	        	if (result.value) {
	                $.ajax({
	                    url: deleteUrl,
	                    type: 'DELETE',
	                    success: function (data) {
	                    	if(data.status){                	
			                	{{$name}}_reloadTable();
			                }else{
			                	swal("Gagal melakukan request", data.msg, "error");
			                }
	                    },
	                    error: function(xhr, textStatus, errorThrown){
	                        swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
	                    }
	                });
	            } else if (result.dismiss === 'cancel') {
	                swal('Cancelled', 'Your data is safe :)', 'error');
	            }
	        });
	    });

	    function {{$name}}_reloadTable() {
	        $("#{{$name}}_datatable").DataTable().ajax.reload(null, false );
	    }

		function {{$name}}_setupTable(tableId, tableElement, tableOrder, tableColumnDefs, tableColumns, tableUrl, tableLengthMenu, tableFixedColumns, useFilter = false) {
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
		        scrollX:        true,
		        scrollCollapse: true,
		        fixedColumns:   tableFixedColumns,
		        lengthMenu: 	tableLengthMenu,
		        order:          tableOrder,
		        columnDefs:     tableColumnDefs,
		        columns:        tableColumns,
		        @if ($disableInfo)
			        bPaginate: false,
				    bLengthChange: false,
				    bFilter: true,
				    bInfo: false,
			    @endif
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

		    $('#{{$name}}_datatableButton').clone().appendTo('#{{$name}}_datatable_wrapper .col-md-6:eq(1)');
			$('#{{$name}}_datatableButton').css('display','block').css('float','right');
			$('#{{$name}}_datatableButton .dropdown-toggle').click();
			$('#{{$name}}_datatable_wrapper .row:eq(2)').css('margin-top', '10px');
			//$('#{{$name}}_datatable_wrapper .dataTables_length').css('margin-top', '10px');
			$('.dts_label').html('');
		}

	</script>
@endprepend


