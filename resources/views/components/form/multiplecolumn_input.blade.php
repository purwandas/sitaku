@php
	$includeId           = @$attributes['includeId'] ?? false;
	$config              = FormBuilderHelper::setupDefaultConfig($name, @$attributes??[], true);
	$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

	if((isset($config['formAlignment']) && $config['formAlignment'] == 'vertical')){
		$labelContainerClass = 'col-form-label text-right';
	}

	$typeUsed = [];
@endphp
<div id="{{$name}}_container" class="{{ @$config['containerClass'] ?? 'form-group row' }}" style="overflow-x: auto;">
	@if(!isset($attributes['useLabel']) || (isset($attributes['useLabel']) && $attributes['useLabel'] == true))
		<label for="{!! @$elOptions['id'] ?? $name !!}" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
		<div class="col-sm-10" style="overflow-x: auto;">
	@endif
		<table class="table table-bordered" id="tbl_multiple_{{$name}}">
			<thead>
		    	<tr>
		    		<th style="width:50px">Action</th>
		    		@if($includeId)
			    		<th style="display: none">ID</th>
		    		@endif

		    		@foreach($columns as $column)
			    		@if($column['type'] != 'hidden')
				    		@php
				    			$columnLabel = $column['options']['labelText'] ?? ucwords(str_replace("_", " ", $column['name']));	
				    		@endphp

				    		<th>{{$columnLabel}}</th>
			    		@endif
			        @endforeach
		    	</tr>
			</thead>
			<tbody>
		    	
			</tbody>
	    </table>
	@if(!isset($attributes['useLabel']) || (isset($attributes['useLabel']) && $attributes['useLabel'] == true))
		</div>
	@endif
</div>

@push('additional-js')
<script type="text/javascript">
	function generateRow(row = 1,value = {}) {
		var newRow = '<tr id="multipleInput_row_'+row+'">';
		var dataDelete = '';
	    if(value.hasOwnProperty('delete') && value['delete']){
	    	dataDelete = 'data-delete="' + value.delete + '"';
		}
		@if($includeId)
			newRow += '<td style="display:none;"><input type="hidden" class="form-control" name="{{$name}}['+row+'][id]" id="{{$name.'_id'}}_'+row+'" value="'+row+'"></td>';
		@endif
		var nextRow = row+1;
		if(row==1){
			newRow += '<td id="actionBtn"><button type="button" id="addRowBtn" class="btn btn-primary" onclick="generateRow('+nextRow+')">{!!str_replace("'", '"', getSvgIcon('fa-plus','mt-m-2'))!!}</button></td>';
		}else{
			$('#multipleInput_row_1 #addRowBtn').attr('onclick','generateRow('+nextRow+')');
			newRow += '<td id="actionBtn"><button type="button" class="btn btn-danger btn-delete"'+ (dataDelete != '' ? dataDelete : ' onclick="removeRow(\''+row+'\')"') +'>{!!str_replace("'", '"', getSvgIcon('fa-times','mt-m-2'))!!}</button></td>';
		}
		@foreach($columns as $column)
			var v = value.{{$column['name']}};
			
			//Select2 Purpose
			if(Array.isArray(v)){
				var option = '<option value="'+v[0]+'" selected="selected">'+v[1]+'</option>';
			}
			//================

			if(typeof value.{{$column['name']}} === 'undefined'){
				v = '';
				option = '';
			}

			newRow += '<td{{ ($column['type'] == 'hidden') ? ' style=display:none;' : '' }}>';			
			@php
				$id = $name.'_'.$column['name'];
				$labelTextInput = ucwords(str_replace('_', ' ', $column['name']));
				$elOptions = arrayToHtml($column['options']['elOptions'] ?? []);
			@endphp
			@if($column['type'] == 'select2')
				@php
					$column['elOptions']['class'] = ($column['elOptions']['class'] ?? '') . ' select2 form-control';
					$elOptions = arrayToHtml($column['elOptions'] ?? []);
				@endphp
				newRow += '<select name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" style="width: 100%" {!! $elOptions !!}>'+option+'</select>';
			@elseif($column['type'] == 'multipleswitch')
				newRow += '<div class="row col-sm-12">';
				@foreach ($column['columns'] as $col)
					var checked = v.{{$col['name']}} == 1 ? 'checked' : '';
					newRow += '<input type="checkbox" name="{{$name}}['+row+'][{{$column['name']}}][{{$col['name']}}]" id="{{$id}}_{{$col['name']}}_'+row+'" '+checked+' data-bootstrap-switch value="1" {!! $elOptions !!}> <div style="margin-top: 7px;margin-right: 10px;margin-left: 10px;">{{ucfirst($col['name'])}}</div>';
				@endforeach
				newRow += '</div>';
			@elseif($column['type'] == 'file')
				@php
					$accept = "";
					if(isset($column['options']['pluginOptions']['allowedFileExtensions'])){
						$output = array_map(function($val) { return ".".$val; }, $column['options']['pluginOptions']['allowedFileExtensions']);
						$accept = implode(',', $output);
					}	
				@endphp
				newRow += '<div class="row col-sm-12"><input type="file" accept="{{$accept}}" class="file" name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" {!! $elOptions !!}></div>';
			@elseif($column['type'] == 'textarea')
				newRow += '<div class="row col-sm-12"><textarea class="form-control" placeholder="Please input {{$labelTextInput}} here" name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" {!! $elOptions !!}>'+v+'</textarea></div>';
			@elseif($column['type'] == 'label')
				newRow += '<div class="multiple-label" style="min-width:230px;">'+v+'</div>';
			@elseif($column['type'] == 'date-range')
				newRow += '<div class="row col-sm-12 multiplecolumn-daterange input-group">'+
					'<input type="text" class="form-control {{$id}}_row" placeholder="Please select Begin here" name="{{$name}}['+row+'][{{$column['name']}}][0]" id="{{$id}}_0_'+row+'" {!! $elOptions !!} value="'+v+'" style="min-width:100px;">'+
					'<div class="input-group-addon">&nbsp;{{ $column['separatorText'] ?? 'to' }}&nbsp;</div>'+
					'<input type="text" class="form-control {{$id}}_row" placeholder="Please select End time" name="{{$name}}['+row+'][{{$column['name']}}][1]" id="{{$id}}_1_'+row+'" {!! $elOptions !!} value="'+v+'" style="min-width:100px;">'+
					'</div>';
			@elseif($column['type'] == 'daterange')
				newRow += '<div class="row col-sm-12 multiplecolumn-daterange input-group input-daterange"><input type="text" class="form-control" placeholder="Please select {{$labelTextInput}} here" name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" {!! $elOptions !!} value="'+v+'" style="min-width:170px;"></div>';
			@else
				newRow += '<div class="row col-sm-12"><input type="{{$column['type']}}" {!! empty(@$column['options']['elOptions']['class']) ? 'class="form-control"' : '' !!} {!! empty(@$column['options']['elOptions']['placeholder']) ? 'placeholder="Please input '.$labelTextInput.' here"' : ''!!} name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" {!! $elOptions !!} value="'+v+'" style="min-width:70px;"></div>';
			@endif
			newRow += '</td>';
		@endforeach
		newRow += '</tr>';
		$('#tbl_multiple_{{$name}} tbody').append(newRow);
		@foreach($columns as $column)
			@php
				$id = $name.'_'.$column['name'];
				array_push($typeUsed, $column['type'])
			@endphp
			@if($column['type'] == 'select2')
				@php
					$column['text'] = @$column['text'] ?? 'obj.name';
					$column['key']  = @$column['key'] ? "obj.".$column['key'] : 'obj.id';
					$placeholder    = $column['elOptions']['placeholder'] ?? 'Select '.ucwords( strtolower($column['options']['labelText']  ?? $column['name']) ).'...';
				@endphp
				$('#{{$id}}_'+row).select2({
				    placeholder: "{{ $placeholder }}",
				    allowClear: {{isset($column['pluginOptions']['allowClear']) ? ($column['pluginOptions']['allowClear'] ? 'true' : 'false') : 'true'}},
				    ajax: {
				    	url: "{{route($column['options'])}}",
				    	method: 'POST',
				    	dataType: 'json',
				    	delay: 250,
				    	@if (isset($column['keyTerm']))
				    	data: function(params) {
							var data = {
								{{$column['keyTerm']}}: params.term,
								page: params.page
							}
							return data;
						},
						@endif
				    	processResults: function (data) {
				        	return {
				          		results:  $.map(data, function (obj) {
				                    return {id: {{ $column['key'] }}, text: {!! $column['text'] !!} }
				         		})
				        	};
				      	},
				      	cache: true
				    }
				});
			@elseif($column['type'] == 'date-range')
				$(".{{$id}}_row").datepicker({
					format: "{{$column['format'] ?? 'yyyy-mm-dd' }}",
					viewMode: {{$column['view'] ?? '0' }}, 
					minViewMode: {{$column['min_view'] ?? '0' }},
					autoclose: true,
					todayHighlight: true,
			       	orientation: "{{$column['orientation'] ?? 'bottom' }}",
				});
			@elseif($column['type'] == 'daterange')
				$("#{{$id}}_"+row).daterangepicker({
				    "autoApply": true,
				    ranges: {
				        'Today': [moment(), moment()],
				        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				        'This Month': [moment().startOf('month'), moment().endOf('month')],
				        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				    },
				    "locale": {
						"format": "{{$column['format']}}",
						"separator": " to ",
						"applyLabel": "Apply",
						"cancelLabel": "Cancel",
						"fromLabel": "From",
						"toLabel": "To",
						"customRangeLabel": "Custom",
						"weekLabel": "W",
						"daysOfWeek": [
				            "Su",
				            "Mo",
				            "Tu",
				            "We",
				            "Th",
				            "Fr",
				            "Sa"
				        ],
				        "monthNames": [
				            "January",
				            "February",
				            "March",
				            "April",
				            "May",
				            "June",
				            "July",
				            "August",
				            "September",
				            "October",
				            "November",
				            "December"
				        ],
				        "firstDay": 1
				    },
				    "startDate": moment(),
				    "endDate": moment()
				});
				$("#{{$id}}_"+row).val('');
			@elseif($column['type'] == 'multipleswitch')
				@foreach ($column['columns'] as $col)
					$('#{{$id}}_{{$col['name']}}_'+row).bootstrapSwitch();
				@endforeach
			@elseif($column['type'] == 'file')
				var v = value.{{$column['name']}};
				if(typeof value.{{$column['name']}} === 'undefined'){
					v = '';
				}
				var exp = v.split('/');
				var caption = exp[exp.length-1];
				$('#{{$id}}_'+row).fileinput({
					theme: 'fas',
					showPreview: false,
					showUpload: false,
					@isset($column['options']['pluginOptions']['allowedFileExtensions'])
					allowedFileExtensions: {!! json_encode($column['options']['pluginOptions']['allowedFileExtensions']) !!},
					@endisset
					initialCaption: caption
				});
				$('#{{$id}}_'+row).closest('.file-input').css('width','100%');
			@else

			@endif
		@endforeach
	}

	function removeRow(row) {
		$('#multipleInput_row_'+row).remove();
	}
	
	$(document).on("show.bs.modal", '.modal', function (event) {
		var zIndex = 100000 + (10 * $(".modal:visible").length);
		$(this).css("z-index", zIndex);
		setTimeout(function () {
			$(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
		}, 0);
	}).on("hidden.bs.modal", '.modal', function (event) {
		$(".modal:visible").length && $("body").addClass("modal-open");
	});

	$(document).ready(function(){
		@if($value !== null && count($value) > 0)
			@foreach($value as $key => $v)
				@if($includeId)
					generateRow({{$v->id}},{!!json_encode($v)!!})
				@else
					generateRow({{$key+1}},{!!json_encode($v)!!})
				@endif
			@endforeach
		@else
			generateRow();
		@endif
	});
</script>
@endpush

@php
	array_unique($typeUsed);
@endphp

@if(in_array('switch', $typeUsed))
	@section('switch-css')
		<link rel="stylesheet" type="text/css" href="{{asset('assets/formbuilder/switch/bootstrap-switch.min.css')}}">
	@endsection

	@section('switch-js')
		<!-- Bootstrap Switch -->
		<script src="{{asset('assets/formbuilder/switch/bootstrap-switch.min.js')}}"></script>
	@endsection

	@section('additional-css')
		<style type="text/css">
			.bootstrap-switch{
				margin-top: 5px;
			}
		</style>
	@endsection
@endif

@if(in_array('select2', $typeUsed))
	@section('select2-plugin-js')
	    <script src="{{asset('assets/formbuilder/select2/select2.min.js')}}"></script>
	@endsection

	@section('select2-plugin-css')
	    <link href="{{asset('assets/formbuilder/select2/select2.min.css')}}" rel="stylesheet" type="text/css" />
	    <style type="text/css">
	    	.select2-search__field {
	    		width: 100% !important;
	    		height: 27px;
			    padding-left: 7px !important;
	    	}
	    	.select2-container--default .select2-container--focus .select2-selection--multiple {
	    		border-color: #e9ecef !important;
	    	}
	    	.select2-container--default .select2-selection--multiple {
	    		border-color: #e9ecef !important;
	    	}
	    	.select2-selection .select2-selection--multiple {
	    		height: 38px;
	    	}
	    	.select2-search .select2-search--inline {
	    		height: 38px;
	    	}
	    	.select2-container--default .select2-selection--multiple .select2-selection__choice {
		    	border-color: #d2d1d1;
	    	}
	    	.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
	    		margin-right: 0px;
	    		width: 13px;
	    	}
	    </style>
	@endsection
@endif

@if(in_array('file', $typeUsed))
	@section('file-css')
		<link href="{{asset('assets/formbuilder/krajee/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />
		<!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
		<!-- link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput-rtl.min.css" media="all" rel="stylesheet" type="text/css" /-->
		<!-- the font awesome icon library if using with `fas` theme (or Bootstrap 4.x). Note that default icons used in the plugin are glyphicons that are bundled only with Bootstrap 3.x. -->
		<link rel="stylesheet" href="{{asset('assets/formbuilder/krajee/all.css')}}" crossorigin="anonymous">
		<style type="text/css">
		    .file-preview-thumbnails{
		        display: table;
		    }
		</style>
	@endsection

	@section('file-js')
		<script src="{{asset('assets/formbuilder/krajee/piexif.min.js')}}" type="text/javascript"></script>
		<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview. 
		    This must be loaded before fileinput.min.js -->
		<script src="{{asset('assets/formbuilder/krajee/sortable.min.js')}}" type="text/javascript"></script>
		<!-- purify.min.js is only needed if you wish to purify HTML content in your preview for 
		    HTML files. This must be loaded before fileinput.min.js -->
		<script src="{{asset('assets/formbuilder/krajee/purify.min.js')}}" type="text/javascript"></script>
		<!-- popper.min.js below is needed if you use bootstrap 4.x (for popover and tooltips). You can also use the bootstrap js 
		   3.3.x versions without popper.min.js. -->
		<script src="{{asset('assets/formbuilder/krajee/popper.min.js')}}"></script>
		<!-- bootstrap.min.js below is needed if you wish to zoom and preview file content in a detail modal
		    dialog. bootstrap 4.x is supported. You can also use the bootstrap js 3.3.x versions. -->
		<script src="{{asset('assets/formbuilder/krajee/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
		<!-- the main fileinput plugin file -->
		<script src="{{asset('assets/formbuilder/krajee/fileinput.min.js')}}"></script>
		<!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`) -->
		<script src="{{asset('assets/formbuilder/krajee/theme.min.js')}}"></script>
	@endsection
@endif

@if(in_array('daterange', $typeUsed))
	@section('daterangepicker-css')
		<link rel="stylesheet" type="text/css" href="{{asset('assets/formbuilder/date/daterangepicker.css')}}" />
		<style type="text/css">
			.w-readonly {
				background-color: #ffffff !important;
			}
			.daterangepicker {
				z-index: 100001;
			}
		</style>
	@endsection

	@section('daterangepicker-js')
		<script type="text/javascript" src="{{asset('assets/formbuilder/date/moment.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('assets/formbuilder/date/daterangepicker.min.js')}}"></script>
	@endsection
@endif

@if(in_array('date', $typeUsed) || in_array('date-range', $typeUsed))
	@section('datepicker-css')
	    <link href="{{asset('assets/formbuilder/date/bootstrap-datepicker.min.css')}}" rel="stylesheet">
	@endsection

	@section('datepicker-js')
	    <script src="{{asset('assets/formbuilder/date/bootstrap-datepicker.min.js')}}"></script>
	@endsection
@endif