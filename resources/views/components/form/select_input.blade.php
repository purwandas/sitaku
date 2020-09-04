@php
if (!is_array($attributes)) $attributes = [];


$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');

$formAlignment = 'horizontal';
if (isset($attributes['formAlignment'])) {
	$formAlignment = $attributes['formAlignment'];
	unset($attributes['formAlignment']);
}


$labelContainerClass = $formAlignment === 'vertical' ? 'col-md-12' : 'col-md-2' .' col-form-label text-right';
$inputContainerClass = $formAlignment === 'vertical' ? 'col-md-12' : 'col-md-10';
if ($formAlignment === 'horizontal') {
	if (isset($attributes['labelContainerClass'])) {
		$labelContainerClass = $attributes['labelContainerClass'];
		unset($attributes['labelContainerClass']);
	}
	if (isset($attributes['inputContainerClass'])) {
		$inputContainerClass = $attributes['inputContainerClass'];
		unset($attributes['inputContainerClass']);
	}
}

$id	= isset($attributes['elOptions']['id']) ? $attributes['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );

$attributes = []; //handling error sementara
$configAttributes = array_merge([
	'class' => 'form-control',
], $attributes);
@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group row' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($useLabel)
        <label for="container{{$id}}" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
		<div id="container{{$id}}" class="{{ $inputContainerClass }}">
	@endif

			{{ Form::select($name, $options, $value, $configAttributes) }}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		</div>
	@endif
</div>