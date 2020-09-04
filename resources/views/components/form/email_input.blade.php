@php
unset($attributes['type']);

$config = FormBuilderHelper::setupDefaultConfig($name, $attributes, true);

$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

if((isset($config['formAlignment']) && $config['formAlignment'] == 'vertical')){
	$labelContainerClass = 'col-form-label text-right';
}

$elOptions = '';
if(isset($config['elOptions'])){
	foreach($config['elOptions'] as $attr => $attr_value){
        $elOptions .= $attr . "='" . trim($attr_value) . "' ";
	}
}

@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group row'}}">
    @if ($config['useLabel'])
        <label for="example-text-input" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
        @if(!isset($config['formAlignment']) || (isset($config['formAlignment']) && $config['formAlignment'] == 'horizontal'))
        <div class="{{$config['inputContainerClass'] ?? 'col-md-10' }}">
        @endif
	@endif

	{{-- Input here --}}
    <input name="{{$name}}" value="{{$value}}" class="form-control" type="email" {!! $elOptions !!}>
    @error($name)
    	<div style="margin-top: 5px;" class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="mdi mdi-close"></i></span>
            </button>
            {{ $message }}
        </div>
    @enderror
    
    @if ($config['useLabel'])
        @if(!isset($config['formAlignment']) || (isset($config['formAlignment']) && $config['formAlignment'] == 'horizontal'))
        </div>
        @endif
    @endif
</div>