@php
if (!is_array($attributes)) $attributes = [];
$attributes['orientation'] = $attributes['orientation'] ?? 'horizontal'; 
$config    = FormBuilderHelper::setupDefaultConfig($name, $attributes);
$id        = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif
			<div class="col-md-12 {{ strtolower($config['orientation']) == 'horizontal' ? 'row' : '' }}">
				@foreach ($options as $key => $option)
				<div class="custom-control custom-checkbox r-15  {{ strtolower($config['orientation']) == 'horizontal' ? 'm-checkbox-inline' : 'mb-5' }}">
					@php
						$elOptions = [ 'class' => 'custom-control-input' ];
						$elOptions = isset($config['elOptions']) ? array_merge($elOptions, $config['elOptions'], ['id'=> ( isset($config['elOptions']['id']) ? $config['elOptions']['id'] : $id ).$key ]) : [];
					@endphp
					{{ Form::checkbox($name.'[]', $key, $key == $value, $elOptions) }}
	              	<label class="custom-control-label" for="{{ $elOptions['id'] }}">{{ ucwords($option) }}</label>
	            </div>
				@endforeach
			</div>

            {!! $config['info'] !!}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>