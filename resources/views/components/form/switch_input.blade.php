@php
	$id = $attributes['options']['id'] ?? $name;
	unset($attributes['options']['id']);
	$labelText = $attributes['labelText'] ?? ucwords(str_replace('_', ' ', $name));
	$elOptions = arrayToHtml($attributes['options'] ?? []);

	$config              = FormBuilderHelper::setupDefaultConfig($name, $attributes, true);
	$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

	if((isset($config['formAlignment']) && $config['formAlignment'] == 'vertical')){
		$labelContainerClass = 'col-form-label text-right';
	}
@endphp
<div class="{{ @$config['containerClass'] ?? 'form-group row' }}">
	@if(!isset($attributes['useLabel']) || (isset($attributes['useLabel']) && $attributes['useLabel'] == true))
		<label for="{{$id}}" class="col-sm-2 col-form-label text-right" style="font-weight: unset !important">{{$labelText}}</label>
		<div class="col-sm-10">
	@endif
        <input type="checkbox" name="{{$name}}" id="{{$id}}" {{$value=='1' ? 'checked' : ''}} data-bootstrap-switch value="1" {!! $elOptions !!}>
	@if(!isset($attributes['useLabel']) || (isset($attributes['useLabel']) && $attributes['useLabel'] == true))
		</div>
	@endif
</div>

@section('switch-css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/formbuilder/switch/bootstrap-switch.min.css')}}">
@endsection

@section('switch-js')
<!-- Bootstrap Switch -->
<script src="{{asset('assets/formbuilder/switch/bootstrap-switch.min.js')}}"></script>
@endsection

@push('additional-js')
<script type="text/javascript">
	$('#{{$id}}').bootstrapSwitch();
</script>
@endpush

@section('additional-css')
	<style type="text/css">
		.bootstrap-switch{
			margin-top: 5px;
		}
	</style>
@endsection