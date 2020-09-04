@php
	$labelText = ucwords(str_replace('_', ' ', $name));
	$includeId = $includeId ?? false;
@endphp
<div class="form-group row" style="margin-bottom: 0px;" id="{{$name}}_container">
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<label for="{{$name}}" class="col-sm-2 col-form-label" style="font-weight: unset !important">{{$labelText}}</label>
	@endif
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<div class="col-sm-10">
	@else
		<div class="col-sm-12">
	@endif
		<table class="table table-bordered" id="tbl_multiple_{{$name}}">
			<thead>
		    	<tr>
		    		@if($includeId)
			    		<th style="display: none">ID</th>
		    		@endif

		    		@foreach($columns as $column)
			    		@php
			    			$columnLabel = $column['options']['labelText'] ?? ucwords($column['name']);	
			    		@endphp

			    		<th>{{$columnLabel}}</th>
			        @endforeach
		    		<th style="width:50px">Action</th>
		    	</tr>
			</thead>
			<tbody>
		    	
			</tbody>
	    </table>
	</div>
</div>

@push('additional-js')
<script type="text/javascript">
	function generateRow(row = 1,value = {}) {
		var newRow = '<tr id="multipleInput_row_'+row+'">';
		@if($includeId)
			newRow += '<td style="display:none;"><input type="hidden" class="form-control" name="{{$name}}['+row+'][id]" id="{{$name.'_id'}}_'+row+'" value="'+row+'"></td>';
		@endif

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

			newRow += '<td>';			
			@php
				$id = $name.'_'.$column['name'];
				$labelTextInput = ucwords(str_replace('_', ' ', $column['name']));
				$elOptions = arrayToHtml($column['options']['options'] ?? []);
			@endphp
			@if($column['type'] == 'select2')
				newRow += '<select class="select2 form-control" name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" style="width: 100%" {!! $elOptions !!}>'+option+'</select>';
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
			@else
				newRow += '<div class="row col-sm-12"><input type="{{$column['type']}}" class="form-control" placeholder="Please input {{$labelTextInput}} here" name="{{$name}}['+row+'][{{$column['name']}}]" id="{{$id}}_'+row+'" {!! $elOptions !!} value="'+v+'"></div>';
			@endif
			newRow += '</td>';
		@endforeach
		var nextRow = row+1;
		if(row==1){
			newRow += '<td id="actionBtn"><button type="button" id="addRowBtn" class="btn btn-primary" onclick="generateRow('+nextRow+')"><i class="fas fa-plus"></i></button></td>';
		}else{
			$('#multipleInput_row_1 #addRowBtn').attr('onclick','generateRow('+nextRow+')');
			newRow += '<td id="actionBtn"><button type="button" class="btn btn-danger" onclick="removeRow('+row+')"><i class="fas fa-times"></i></button></td>';
		}
		newRow += '</tr>';
		$('#tbl_multiple_{{$name}} tbody').append(newRow);
		@foreach($columns as $column)
			@php
				$id = $name.'_'.$column['name'];
			@endphp
			@if($column['type'] == 'select2')
				@php
					$column['options']['text'] = $column['options']['text'] ?? 'item.name';
					$column['options']['key'] = isset($column['options']['key']) ? "item.".$column['options']['key'] : 'item.id';	
				@endphp
				$('#{{$id}}_'+row).select2({
				    placeholder: 'Select {{strtolower($column['options']['labelText'] ?? $column['name'])}}...',
				    allowClear: true,
				    ajax: {
				    	url: "{{route($column['source'])}}",
				    	dataType: 'json',
				    	delay: 250,
				    	processResults: function (data) {
				        	return {
				          		results:  $.map(data, function (item) {
				                    return {id: {{ $column['options']['key'] }}, text: {!! $column['options']['text'] !!} }
				         		})
				        	};
				      	},
				      	cache: true
				    }
				});
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

@section('select2-css')
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('assets/admin/plugins/select2/css/select2.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/admin/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" /> --}}
@endsection

@section('select2-js')
<!-- Select2 -->
<script src="{{asset('assets/admin/plugins/select2/js/select2.full.min.js')}}"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script> --}}
<script type="text/javascript">
	function setSelect2IfPatchModal(element, id, text){

	    element.select2("trigger", "select", {
	        data: { id: id, text: text }
	    });

	    // Remove validation of success
	    element.closest('.form-group').removeClass('has-success');

	    var span = element.parent('.input-group').children('.input-group-addon');
	    span.addClass('display-hide');

	    // Remove focus from selection
	    element.next().removeClass('select2-container--focus');

	}
</script>
@endsection

@section('fileinput-css')
<link href="{{asset('assets/formbuilder/krajee/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="{{asset('assets/formbuilder/krajee/all.css')}}" crossorigin="anonymous">
<style type="text/css">
	.file-preview-thumbnails{
		display: table;
	}
</style>
@endsection

@section('fileinput-js')
<script src="{{asset('assets/formbuilder/krajee/piexif.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/formbuilder/krajee/sortable.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/formbuilder/krajee/purify.min.js')}}" type="text/javascript"></script>
<script src="{{asset('assets/formbuilder/krajee/popper.min.js')}}"></script>
<script src="{{asset('assets/formbuilder/krajee/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('assets/formbuilder/krajee/fileinput.min.js')}}"></script>
<script src="{{asset('assets/formbuilder/krajee/theme.min.js')}}"></script>
@endsection