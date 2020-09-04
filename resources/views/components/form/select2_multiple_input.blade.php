@php
if (!is_array($attributes)) $attributes = [];

// SET DEFAULT CLASS
$attributes['elOptions']['class'] = 'select2 form-control';

// SET DEFAULT ID
$attributes['elOptions']['id'] = $attributes['elOptions']['id'] ?? 'select2-' . $name;

// CALLING SETUP DEFAULT CONFIG
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes, true);
$config['pluginOptions'] = $attributes['pluginOptions'] ?? [];

$id       = $attributes['elOptions']['id'];
$value    = is_array($value) ? $value : [];
$width100 = $config['width100'] ? 'width-100 row-no-margin' : '';
$label    = ucwords( str_replace('_', ' ', $name) );
@endphp

<div class='{{ @$config['containerClass'] ?? 'form-group' }} {{$width100.' '.$id}} {{ !$errors->has($name) ?: 'has-error' }}'>
	@if ($config['useLabel'])
	<div class='row {{$width100}}'>
		<div class='{{ $config['labelContainerClass'] }}'>
			<label class='col-form-label'>
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class='{{ $config['inputContainerClass'] }} {{$width100}}'>
	@endif

			<div class='{{empty($width100)?'row':''}} {{$width100}}'>
				<div class='{{empty($width100)?'col-md-12':''}} {{$width100}}'>
					<div class='input-group {{$width100}}' style='margin-bottom: 10px'>
						<div style='width:90%;'>
							{{ Form::select2Input($name, $value, $options, array_merge($config, ['useLabel' => false])) }}
						</div>
						<div style='width:10%'>
							<span class='input-group-append'>
								<button class='btn btn-primary btn-block' type='button' onclick='addSelectedVal_{{$id}}()' style='border-radius: 0 4px 4px 0 !important;font-size: 9.5pt;'>
									Add
								</button>
							</span>
						</div>
					</div>
				</div>
				<div class='{{empty($width100)?'col-md-12':''}} {{$width100}}'>
					<table class='table {{'table-select2-' . $id}}'>
						<thead>
							<tr>
								<th>#</th>
								<th>{{$label}} Name</th>
								<th style='text-align: right;'>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan='3' align='center'>{{$label}} is Empty.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			@if($errors->has($name))
			<span id='helpBlock2' class='help-block'>{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('vendor-css')
<style type="text/css">
	.select2-container--default .select2-selection--multiple{
		border-top-right-radius: 0 !important; 
	}
</style>
@endpush

@push('function-js')
<script type="text/javascript">
	var select2val_{{$id}} = {!! json_encode($value) !!};

	function addSelectedVal_{{$id}}(){
		$.each($('#{{$config['elOptions']["id"]}}').select2('data'), function(k, v){
			if (isSelectedDataExist_{{$id}}(v.id)) {
				toastr.options = {
				  "closeButton": true,
				  "debug": false,
				  "newestOnTop": false,
				  "progressBar": true,
				  "positionClass": "toast-bottom-right",
				  "preventDuplicates": false,
				  "onclick": null,
				  "showDuration": "300",
				  "hideDuration": "1000",
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "slideDown",
				  "hideMethod": "slideUp"
				};

				toastr.warning('{{$label}} '+ v.text +' has been selected.', "Warning!");
			} else {
				select2val_{{$id}}.push({
					id: v.id,
					name: v.text
				});
				generateTable_{{$id}}();
			    $('#{{$config['elOptions']["id"]}}').val({}).trigger('change');
			}
		});
	}

	function generateTable_{{$id}}() {
		resetTable_{{$id}}();

		if (select2val_{{$id}}.length > 0) {
			$.each(select2val_{{$id}}, function(k, v) {
				var number = k+1;
	            $(".{{'table-select2-' . $id}} tbody").append(
	            	'<tr>' +
	            		'<td>' + number + '</td>' +
	            		'<td>' + v.name + '</td>' +
	            		'<td align="right">' + 
	            		 	'<button class="btn btn-danger btn-sm removeSelectedDataBtn_{{$id}}" type="button" data-id="'+ v.id +'" title="Remove this {{$label}}" data-toggle="tooltip"><i class="fa fa-times"></i></button>' +  
	            		 '</td>' +
	            		 '<td style="display:none">' +
	            			'<input type="hidden" value="'+ v.id +'" name="{{$label}}[]">' +
	            		 '</td>' +
	            	'</tr>' 
	            );
	        });
		} else {
			$(".{{'table-select2-' . $id}} tbody").append(
	        	'<tr>' +
	        		'<td colspan="3" align="center">{{$label}} is Empty.</td>' +
	        	'</tr>' 
	        );
		}
	}

	// Remove Selected Data
	$(".{{'table-select2-' . $id}} tbody").on('click', '.removeSelectedDataBtn_{{$id}}', function(){
		getSelectedVal_{{$id}}($(this).attr('data-id'), true);
		$(this).closest('tr').remove();
		generateTable_{{$id}}();
	});

	function getSelectedVal_{{$id}}(id, update = false) {
		var foundVal = $.grep(select2val_{{$id}}, function(v) {
			if(update){
				return v.id !== id
			}
		    return v.id === id;
		});

		if(update) {
			select2val_{{$id}} = foundVal;
			return;
		}

		return foundVal;
	}

	function isSelectedDataExist_{{$id}}(id) {
		return getSelectedVal_{{$id}}(id).length > 0;
	}

	function resetTable_{{$id}}() {
		$(".{{'table-select2-' . $id}} tbody").html('')
	}

	$(document).ready(function(){
		generateTable_{{$id}}();
	});
</script>
@endpush