@php
if (!is_array($attributes)) $attributes = [];
$isDataRequestByAjax = is_array($options) ? false : true;
$url    = $isDataRequestByAjax ? $options : '';
$url    = !empty($url) ? route($url) : '';
$method = $isDataRequestByAjax ? ( isset($attributes['elOptions']['route']) ? $attributes['elOptions']['route'] : 'POST') : 'POST';

// SET DEFAULT CLASS
$attributes['elOptions']['class'] =  ($attributes['elOptions']['class'] ?? '').' select2 form-control';
$attributes['elOptions']['style'] = 'width:100%';

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

$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

if((isset($config['formAlignment']) && $config['formAlignment'] == 'vertical')){
	$labelContainerClass = 'col-form-label text-right';
}

$elOptions = '';
if(isset($config['elOptions'])){
	foreach($config['elOptions'] as $attr => $attr_value){
		$elOptions .= $attr . "='" . trim($attr_value) . "' ";
	}
}

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
		<label for="{!! @$elOptions['id'] ?? $name !!}" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
        @if(!isset($config['formAlignment']) || (isset($config['formAlignment']) && $config['formAlignment'] == 'horizontal'))
        <div class="{{$config['inputContainerClass'] ?? 'col-md-10' }}">
        @endif
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
        @if(!isset($config['formAlignment']) || (isset($config['formAlignment']) && $config['formAlignment'] == 'horizontal'))
        </div>
        @endif
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
    	.select2-container .select2-selection--single {
			height: 34px !important;
			padding-top: 0.115rem !important;
			padding-bottom: 0.115rem !important;
			padding-left: 4px !important;
		}

		.select2-selection--single:focus {
		    border-color: #8ad4ee;
		    outline: 0;
		    box-shadow: 0 0 0 0.2rem rgba(50, 31, 219, 0.25);
		}
		.select2-container--default.select2-container--focus .select2-selection--multiple {
		    border-color: #8ad4ee !important;
		    outline: 0 !important;
		    box-shadow: 0 0 0 0.2rem rgba(50, 31, 219, 0.25) !important;
		}
		.select2-container--default .select2-selection--single .select2-selection__placeholder {
			color : #8b949b !important;
		}
		select {
			cursor: pointer;
		}
		.select2-selection__rendered {
			padding-top: 2px;
		}
		.select2-dropdown {
			z-index: 100010;
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

	$(document).ready(function() {
		// IF THE SELECT2 IS REQUEST DATA BY AJAX
		@if ($isDataRequestByAjax)
		select2Options_{{$diffId}}.ajax = {
			url: "{{ $url }}",
			method: "{{ $method }}",
			data: function(params) {
				var data = {
					{{ @$config['keyTerm'] ?? '_name' }}: params.term,
					page: params.page
				}
				@foreach ($config['ajaxParams'] as $key => $val)
				data.{{$key}} = {!! is_array($val) ? json_encode($val) : $val !!};
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