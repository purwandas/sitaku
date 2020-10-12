@php
if (!is_array($attributes)) $attributes = [];
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes);
$id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
$config['format']      = $config['format'] ?? 'yyyy-mm-dd';
$config['view']        = $config['view'] ?? 0;
$config['min_view']    = $config['min_view'] ?? 0;
$config['orientation'] = $config['orientation'] ?? "bottom";
$config['elOptions']['autocomplete'] = 'off';
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
								'id' => $id
							],
							$config['elOptions'], 
							[ 'class' => ' form-control '. ( isset($config['class']) ? $config['class'] : '' ) ] 
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

@section('datepicker-css')
    <link href="{{asset('assets/formbuilder/date/bootstrap-datepicker.min.css')}}" rel="stylesheet">
@endsection

@section('datepicker-js')
    <script src="{{asset('assets/formbuilder/date/bootstrap-datepicker.min.js')}}"></script>
@endsection

@push('function-js')
<script type="text/javascript">
	$("#{{$id}}").datepicker({
		format: "{{$config['format']}}",
		viewMode: "{{$config['view']}}", 
		minViewMode: "{{$config['min_view']}}",
		autoclose: true,
		todayHighlight: true,
       	orientation: "{{$config['orientation']}}"
	});
</script>
@endpush