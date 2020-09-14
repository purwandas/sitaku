@php
if (!is_array($attributes)) $attributes = [];

$attributes = array_merge($attributes, FormBuilderHelper::setupDefaultConfig($name, $attributes, true));

$useLabel = true;
if (isset($attributes['useLabel'])) {
	$useLabel = $attributes['useLabel'];
	unset($attributes['useLabel']);
}

$labelText = isset($attributes['labelText']) ? $attributes['labelText'] : ucwords(implode(' ', explode('_', $name))) . (isset($attributes['required']) ? ' <span class="status-decline">*</span>' : '');

// $formAlignment = 'horizontal';
// if (isset($attributes['formAlignment'])) {
// 	$formAlignment = $attributes['formAlignment'];
// 	unset($attributes['formAlignment']);
// }


$labelContainerClass = $attributes['formAlignment'] === 'vertical' ? 'col-md-12' : 'col-md-2' .' col-form-label text-right';
$inputContainerClass = $attributes['formAlignment'] === 'vertical' ? 'col-md-12' : 'col-md-10';
if ($attributes['formAlignment'] === 'horizontal') {
	if (isset($attributes['labelContainerClass'])) {
		$labelContainerClass = $attributes['labelContainerClass'];
		unset($attributes['labelContainerClass']);
	}
	if (isset($attributes['inputContainerClass'])) {
		$inputContainerClass = $attributes['inputContainerClass'];
		unset($attributes['inputContainerClass']);
	}
}

if(isset($attributes['formAlignment']) && $attributes['formAlignment'] == 'vertical'){
	$labelContainerClass = 'col-form-label text-right';
}

$id	= isset($attributes['elOptions']['id']) ? $attributes['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );

// $attributes = []; //handling error sementara
$configAttributes = array_merge([
	'class' => 'form-control',
], $attributes['elOptions']);



@endphp

<div class="{{ @$attributes['containerClass'] ?? 'form-group row' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($useLabel)
        <label for="container{{$id}}" class="{{$labelContainerClass}}">{!! ucfirst($attributes['labelText'] ?? $name) !!}</label>

		@if(!isset($attributes['formAlignment']) || (isset($attributes['formAlignment']) && $attributes['formAlignment'] == 'horizontal'))
        <div class="{{$config['inputContainerClass'] ?? 'col-md-10' }}">
        @endif
	@endif

			{{ Form::select($name, $options, $value, $configAttributes) }}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($useLabel)
		@if(!isset($attributes['formAlignment']) || (isset($attributes['formAlignment']) && $attributes['formAlignment'] == 'horizontal'))
        	</div>
        @endif
	@endif
</div>