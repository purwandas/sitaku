@php
	$id = $attributes['options']['id'] ?? $name;
	unset($attributes['options']['id']);
	$labelText = $attributes['labelText'] ?? ucwords(str_replace('_', ' ', $name));
	$elOptions = arrayToHtml($attributes['options'] ?? []);
@endphp
<div class="form-group row">
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<label for="{{$id}}" class="col-sm-2 col-form-label text-right" style="font-weight: unset !important">{{$labelText}}</label>
	@endif
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<div class="col-sm-10">
	@else
		<div class="col-sm-12">
	@endif
        <input type="checkbox" name="{{$name}}" id="{{$id}}" {{$value=='1' ? 'checked' : ''}} data-bootstrap-switch value="1" {!! $elOptions !!}>
	</div>
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