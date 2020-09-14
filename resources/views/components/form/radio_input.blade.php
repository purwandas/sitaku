@php
if (!is_array($attributes)) $attributes = [];
$attributes['orientation'] = $attributes['orientation'] ?? 'horizontal'; 
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes);
// $id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
@endphp

<div class="{{ @$config['containerClass'] ?? 'row form-group' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif
			<div class="col-md-12 {{ strtolower($config['orientation']) == 'horizontal' ? 'row' : '' }}">
			@if (strtolower($config['orientation']) == 'horizontal')
				@foreach ($options as $key => $option)
				@if (@$config['col'])
				<div class="col">
				@endif
				<div class="custom-control custom-radio m-radio-inline r-15">
					@php 
					$id = str_replace(' ', '', $option)
					@endphp
					{{ Form::radio($name, $key, $key == $value, ['class'=>'custom-control-input', 'id'=>$id]) }}
	              	<label class="custom-control-label" for="{{$id}}">{{ ucwords($option) }}</label>
	            </div>
	            @if (@$config['col'])
				</div>
				@endif
				@endforeach

			@elseif (strtolower($config['orientation']) == 'vertical')
				@foreach ($options as $key => $option)
	            <div class="custom-control custom-radio mb-5 r-15">
					@php 
					$id = str_replace(' ', '', $option)
					@endphp
					{{ Form::radio($name, $key, $key == $value, ['class'=>'custom-control-input', 'id'=>$id]) }}
	              	<label class="custom-control-label" for="{{$id}}">{{ ucwords($option) }}</label>
	            </div>
				@endforeach
			@endif
			</div>

            {!! $config['info'] !!}

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	@endif
</div>