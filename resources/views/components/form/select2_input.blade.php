@php
if (!is_array($attributes)) $attributes = [];
$isDataRequestByAjax = is_array($options) ? false : true;
$url    = $isDataRequestByAjax ? $options : '';
$url    = !empty($url) ? route($url) : '';
$method = $isDataRequestByAjax ? ( isset($attributes['elOptions']['route']) ? $attributes['elOptions']['route'] : 'POST') : 'POST';

// SET DEFAULT CLASS
$attributes['elOptions']['class'] = 'select2 form-control';

// SET DEFAULT ID
$attributes['elOptions']['id'] = $attributes['elOptions']['id'] ?? 'select2-' . $name;
$diffId = str_replace("-", "_", $attributes['elOptions']['id']);

// SET DEFAULT FOR FORMATTED SELECT2 DATA FORMAT
$attributes['text'] = $attributes['text'] ?? 'obj.name';
$attributes['key']  = $attributes['key'] ?? 'obj.id';

// CALLING SETUP DEFAULT CONFIG
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes, true);
$config['pluginOptions'] = $attributes['pluginOptions'] ?? [];
$config['ajaxParams']    = $attributes['ajaxParams'] ?? [];

// FORMATTING TEXT BY TEMPLATE 
// if (is_array($config['text'])) {
// 	$text = null;
// 	foreach ($config['text']['field'] as $field) {
// 		$text = str_replace("<<$field>>", "'+ obj.$field +'", $text ?? $config['text']['template']);
// 	}
// str_replace_array('<<field>>', $config['text']['field'], $config['text']['template']); // Laravel str helper method 
// 	$config['text'] = "'" . $text . "'";
// }
@endphp

{{-- <div class='form-group {{ $config['useLabel'] ? '' : 'width-100' }} {{ !$errors->has($name) ?: 'has-error' }}'> --}}
<div class='{{ @$config['containerClass'] ?? 'form-group row' }}'>
	@if ($config['useLabel'])
	{{-- <div class='row'> --}}
		<div class='{{ $config['labelContainerClass'] }}'>
			<label class='{{ @$config['labelClass'] }}'>
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class='{{ $config['inputContainerClass'] }}'>
	@endif
			<select {{ isset($config['pluginOptions']['multiple']) && $config['pluginOptions']['multiple'] ? "name=".$name ."[] multiple" : "name=".$name."" }} <?= $config['htmlOptions'] ?>>
				@if (!$isDataRequestByAjax)
					@foreach ($options as $key => $option)
		                <option></option>
		                <option value='{{ $key }}'>{{ $option }}</option>
					@endforeach
				@endif
            </select>

            {!! @$config['info'] !!}

			@if($errors->has($name))
			<span id='helpBlock2' class='help-block'>{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	{{-- </div> --}}
	@endif
</div>

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

@push('function-js')
<script type="text/javascript">
	var select2Options_{{$diffId}} = Object.assign({
			placeholder: "{{ $config['elOptions']['placeholder'] }}",
	    	allowClear: true,//
		}, {!! json_encode($config['pluginOptions']) !!}),
		select2val_{{$name}} = {!! !is_array($value) ? json_encode([$value]) : json_encode($value) !!};

	$('.select2').on('select2:open',function(){
	    $('.select2-dropdown').css("z-index", 100000);
	});

	$(document).ready(function() {
		// IF THE SELECT2 IS REQUEST DATA BY AJAX
		@if ($isDataRequestByAjax)
		select2Options_{{$diffId}}.ajax = {
			url: "{{ $url }}",
			method: "{{ $method }}",
			data: function(params) {
				var data = {
					{{ @$config['keyTerm'] ?? 'name' }}: params.term,
					page: params.page
				}
				@foreach ($config['ajaxParams'] as $key => $val)
				data.{{$key}} = {!! $val !!};
				@endforeach
				return data;
			},
			processResults: function (data) {
				var result = {},
					isPaginate = data.hasOwnProperty('data'),
					isSimplePaginate = !data.hasOwnProperty('last_page');

	                result.results = $.map(isPaginate ? data.data : data, function (obj) {
	                    return {id: {!! $config['key'] !!}, text: {!! $config['text'] !!} }
	                })

	                if (isPaginate) {
	                	result.pagination = {              
		                	more: isSimplePaginate ? data.next_page_url !== null : data.current_page < data.last_page
		                }
	                }

				return result;
			}
		}
		@endif

		// FOR SELECT2 DROPDOWNPARENT
		@if (isset($config['pluginOptions']['dropdownParent']))
		select2Options_{{$diffId}}.dropdownParent = $('<?= $config['pluginOptions']["dropdownParent"] ?>');
		@endif

		$('#{{ $config['elOptions']['id'] }}').select2(select2Options_{{$diffId}});
		@if (!empty($value))
		$('#{{ $config['elOptions']['id'] }}').select2("trigger", "select", {
			data: { id: "{{ $value[0]}}", text: "{{ $value[1] }}" }
		});

        // scroll top
        setTimeout(function() {
	        window.scrollTo(0, 0);
	        $('html, body').scrollTop();
        }, 200);
	    @endif
	});
</script>
@endpush