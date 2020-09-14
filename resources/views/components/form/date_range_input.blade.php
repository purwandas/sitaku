@php
if (!is_array($attributes)) $attributes = [];
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes);
$id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
$config['format']   = $config['format'] ?? 'DD-MM-YYYY';
$config['view']     = $config['view'] ?? 0;
$config['min_view'] = $config['min_view'] ?? 0;
@endphp

<div class="{{ @$config['containerClass'] ?? 'row form-group' }} {{ $config['useLabel'] ? '' : 'width-100' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif
			@if (!empty($config['addonsConfig']))
			<div class="input-group">
				@if ($config['addonsConfig']['position'] === 'left')
				<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			@endif

				{{ 
					Form::text( $name, $value, 
						array_merge(
							[ 
								'id'       => $id,
								'readonly' => 'readonly'
							],
							$config['elOptions'], 
							[ 'class' => ' form-control w-readonly '. ( isset($config['class']) ? $config['class'] : '' ) ] 
						)
					) 
				}}

			@if (!empty($config['addonsConfig']))
				@if ($config['addonsConfig']['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			</div>
			@endif

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	@endif
</div>

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

@push('function-js')
<script type="text/javascript">
	$("#{{$id}}").daterangepicker({
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
			"format": "{{$config['format']}}",
			"separator": " ~ ",
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
	$("#{{$id}}").val('');
</script>
@endpush