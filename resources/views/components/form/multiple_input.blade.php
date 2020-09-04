@php
if (!is_array($attributes)) $attributes = [];

$config = \FormBuilderHelper::setupDefaultConfig($name, $attributes);

$id = $config['elOptions']['id'];
unset($config['elOptions']['id']);

$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

if ($values === null || $values === []) $values = [''];

$isFirst = true;

$elOptions = '';
foreach($config['elOptions'] as $attr => $attr_value){
    $elOptions .= $attr . "='" . trim($attr_value) . "' ";
}
@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group row' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
        <label for="container{{$id}}" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
		<div id="container{{$id}}" class="col-12 col-md-10">
	@endif
			<div id="{{ $id }}" class="multipleInput_container">
				@foreach ($values as $value)
				<div class="input-group" style="margin-bottom: 10px">
					@if (!empty($config['addonsConfig']) && @$config['addonsConfig']['position'] === 'left')
					<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
					@endif

					<input type="{{ $type }}" name="{{ $name }}[]" class="form-control" value="{{ $value }}"  {!! $elOptions !!}>

					@if (!empty($config['addonsConfig']) && @$config['addonsConfig']['position'] === 'right')
					<span class="input-group-addon addon-middle-side">{{ $config['addonsConfig']['text'] }}</span>
					@endif

					@if ($isFirst)
					<button type="button" class="btn btn-primary addon-right-side multipleInput_addRowBtn-{{ $name }}"><span class="fas fa-plus"></span></button>
					@else
					<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><span class="fa fa-times"></span></button>
					@endif
					@php
						$isFirst = false;
					@endphp
				</div>
				@endforeach

				@if($errors->has($name))
				<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
				@endif

			</div>
	@if ($config['useLabel'])
		</div>
	@endif
</div>

@push('function-js')
<script type="text/javascript">
	$('body').on('click', '.multipleInput_removeRowBtn', function(){
		$(this).closest('div.input-group').remove();
	});

	function clearMultiple{{ $id }}() {
		$("#{{ $id }}").children(".new-multiple").remove();
	}

	$('.multipleInput_addRowBtn-{{  $name  }}').click(function(){
		$(this).closest('.multipleInput_container').append(
			'<div class="input-group new-multiple {{@$config['addonsConfig']['position']}}" style="margin-bottom: 10px">' +
			@if (!empty($config['addonsConfig']) && @$config['addonsConfig']['position'] === 'left')
			'<span class="input-group-addon addon-left-side">{{$config['addonsConfig']['text']}}</span>' + 
			@endif
			'<input type="{{$type}}" name="{{$name}}[]" class="form-control" <?= $config['htmlOptions'] ?>>' + 
			@if (!empty($config['addonsConfig']) && @$config['addonsConfig']['position'] === 'right')
			'<span class="input-group-addon addon-middle-side">{{$config['addonsConfig']['text']}}</span>' + 
			@endif
			'<button type="button" class="btn btn-danger addon-right-side multipleInput_removeRowBtn"><i class="fa fa-times"></i></button>' +
			'</div>');
	});
</script>
@endpush