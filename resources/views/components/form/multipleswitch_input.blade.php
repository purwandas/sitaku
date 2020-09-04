@php
	$labelText = ucwords(str_replace('_', ' ', $name));
@endphp
<div class="form-group row" id="{{$name}}" style="margin-bottom: 0px;">
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<label for="{{$name}}" class="col-sm-2 col-form-label" style="font-weight: unset !important">{{$labelText}}</label>
	@endif
	@if(!isset($attributes['withLabel']) || (isset($attributes['withLabel']) && $attributes['withLabel'] == true))
		<div class="col-sm-10">
	@else
		<div class="col-sm-12">
	@endif
        <div class="row col-sm-12">
        	@foreach($columns as $column)
        		@php
	        	$column['options']['withLabel'] = false;
	        	$column['options']['options']['id'] = $name.'_'.$column['name'];
	        	@endphp
        		{{Form::switchInput($name.'['.$column['name'].']',$value[$column['name']] ?? old($column['name']),$column['options'] ?? [])}}
        		<div style="margin-top: 7px;margin-right: 10px;margin-left: 10px;">{{ucfirst($column['options']['labelText'] ?? $column['name'])}}</div>
	        @endforeach
	    </div>
	</div>
</div>