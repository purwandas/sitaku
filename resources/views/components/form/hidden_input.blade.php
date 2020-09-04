@php
unset($attributes['type']);

$config = FormBuilderHelper::setupDefaultConfig($name, $attributes, true);

$elOptions = '';
if(isset($config['elOptions'])){
	foreach($config['elOptions'] as $attr => $attr_value){
		$elOptions .= $attr . "='" . trim($attr_value) . "' ";
	}
}

@endphp

<input name="{{$name}}" value="{{$value}}" type="hidden" {!! $elOptions !!}>