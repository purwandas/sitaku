@php
unset($attributes['type']);

$config = FormBuilderHelper::setupDefaultConfig($name, $attributes, true);

$labelContainerClass = ($config['labelContainerClass'] ?? 'col-md-2').' col-form-label text-right';

if((isset($config['formAlignment']) && $config['formAlignment'] == 'vertical')){
    $labelContainerClass = 'col-form-label text-right';
}

$config['elOptions']['id'] = $config['elOptions']['id'] ?? 'text-' . $name;

$elOptions = '';
if(isset($config['elOptions'])){
    foreach($config['elOptions'] as $attr => $attr_value){
        $elOptions .= $attr . "='" . trim($attr_value) . "' ";
    }
}

if (array_key_exists('autoNumeric', $config)) {
    if (!is_array($config['autoNumeric'])) {
        $config['autoNumeric'] = [
            // 'allowDecimalPadding' => false,
            // 'decimalPlaces'       => 7,
            'decimalCharacter'    => ',',
            'digitGroupSeparator' => '.'
        ];
    }
}

@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group row' }}">
    @if ($config['useLabel'])
        <label for="{!! @$elOptions['id'] ?? $name !!}" class="{{$labelContainerClass}}">{!! ucfirst($config['labelText'] ?? $name) !!}</label>
        @if(!isset($config['formAlignment']) || (isset($config['formAlignment']) && $config['formAlignment'] == 'horizontal'))
        <div class="{{$config['inputContainerClass'] ?? 'col-md-10' }}">
        @endif
    @endif

    {{-- Input here --}}
    @if(@$config['autoNumeric'])
        <input type="text" id="{{$config['elOptions']['id']}}1" class="form-control" type="text" {!! $elOptions !!}>
        <input name="{{$name}}" value="{{$value}}" class="form-control" type="hidden" {!! $elOptions !!}>
    @else
        <input name="{{$name}}" value="{{$value}}" class="form-control" type="text" {!! $elOptions !!}>
    @endif

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

@if(@$config['autoNumeric'] || is_array(@$config['autoNumeric']))
@section('auto-numeric-plugin-js')
<script src="{{asset('assets/formbuilder/auto-numeric/autonumeric@4.6.0.js')}}"></script>
@endsection

@push('auto-numeric-plugin-js')
<script type="text/javascript">
    @if( is_array($config['autoNumeric']) )
    var parse = {!! json_encode($config['autoNumeric'], JSON_FORCE_OBJECT) !!};
        const autonumeric_{{$config['elOptions']['id']}} = new AutoNumeric('#{{$config['elOptions']['id']}}1', parse);
    @else
        const autonumeric_{{$config['elOptions']['id']}} = new AutoNumeric('#{{$config['elOptions']['id']}}1');
    @endif

    @if ($value)
        AutoNumeric.set('#{{$config['elOptions']['id']}}1', {{$value}});
    @endif

    $("#{{$config['elOptions']['id']}}1").change(function(){
        $("#{{$config['elOptions']['id']}}").val(AutoNumeric.getNumericString('#{{$config['elOptions']['id']}}1'));
    });
    $("#{{$config['elOptions']['id']}}").change(function(){
        AutoNumeric.set('#{{$config['elOptions']['id']}}1',$(this).val());
    });
</script>
@endpush
@endif