@php
if (!is_array($attributes)) $attributes = [];
$config = \App\Components\Helpers\FormBuilderHelper::setupDefaultConfig($name, $attributes);
$id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
@endphp

<div id="{{$id}}" class="{{ @$config['containerClass'] ?? 'form-group' }} {{ $config['useLabel'] ? '' : 'width-100' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ($config['useLabel'])
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif
			@if (!empty($config['addonsConfig']))
			<div class="input-group">
				@if ($config['addonsConfig']['position'] === 'left')
				<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			@endif

				<div class='row-no-margin width-100'>
                    <div class='form-group row-no-margin width-100'>
                        <label class='control-label'>Address</label>
                        {{
							Form::text( 'address', null,
								[ 
									'id'          => 'locpic-address'.$id,
									'placeholder' => 'Please enter address here',
									'class'       => 'form-control',
								]
							) 
						}}
                    </div>
                </div>
                <div class='row-no-margin width-100'>
                    <div class='form-group row-no-margin width-100 m-bot-0'>
                        <div class='form-horizontal width-100'>
                            <div class='form-group' style='display: none'>
                                <label class='col-sm-2 control-label'>Radius:</label>
                                <div class='col-sm-5'>
                                    {{
										Form::text( 'locpic-radius', null,
											[ 
												'id'          => 'locpic-radius'.$id,
												'placeholder' => 'Please enter redius here',
												'class'       => 'form-control',
												'readonly'    => 'readonly',
												'required'    => 'required'
											]
										) 
									}}
                                </div>
                            </div>
                            <div id='locpic{{$id}}' style='width: 100%; height: 400px;'></div>
                            <div class='clearfix'>&nbsp;</div>
                            <div class='m-t-small'>
                            </div>
                            <div class='clearfix'></div>
                        </div>
                    </div>
                </div>
                <div class='row-no-margin width-100'>
                    <div class='form-group width-50 m-bot-0 r-15'>
                        <label>Latitude</label>
                        {{
							Form::text( 'latitude', null,
								[ 
									'id'          => 'locpic-latitude'.$id,
									'placeholder' => 'Please enter latitude here',
									'class'       => 'form-control',
									'readonly'    => 'readonly',
									'required'    => 'required'
								 ]
							) 
						}}
                    </div>
                    <div class='form-group width-50 m-bot-0'>
                        <label>Longitude</label>
						{{
							Form::text( 'longitude', null,
								[ 
									'id'          => 'locpic-longitude'.$id,
									'placeholder' => 'Please enter longitude here',
									'class'       => 'form-control',
									'readonly'    => 'readonly',
									'required'    => 'required'
								 ]
							) 
						}}
                    </div>
                </div>


			@if (!empty($config['addonsConfig']))
				@if ($config['addonsConfig']['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			</div>
			@endif

			@if($errors->has($name))
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ($config['useLabel'])
		</div>
	</div>
	@endif
</div>

@push('function-js')
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCICVFZg9PawAeVO5oH_BRdE7IEu93eG8E&amp;sensor=false&amp;sensor=false&amp;v=3&amp;libraries=places"></script>
<script src="{{ asset('assets/formbuilder/location/locationpicker.jquery.min.js') }}"></script>
<script type="text/javascript">

    initMap{{$id}}([]);

    function initMap{{$id}}(params=[]) {
		latitude  = -6.2241031;
		longitude = 106.92347419999999;
        $('#locpic-address{{$id}}').val('');
    	if (params.length) {
    		if ( isFLoat(params[0]) || isNumber(params[0]) ) {
				latitude  = params[0];
    		}
    		if ( isFLoat(params[1]) || isNumber(params[1]) ) {
				longitude = params[1];
    		}
    		if ( typeof params[2] === 'undefined' || params[2] === null ) {
			    // variable is undefined or null
			} else {
		        $('#locpic-address{{$id}}').val(params[2]);
			}
		}
        $('#locpic-latitude{{$id}}').val(latitude);
		$('#locpic-longitude{{$id}}').val(longitude);
        $('#locpic{{$id}}').locationpicker({
            location:{
                latitude:latitude,
                longitude:longitude
            },
            radius:5,
            inputBinding:{
				latitudeInput:$('#locpic-latitude{{$id}}'),
				longitudeInput:$('#locpic-longitude{{$id}}'),
				radiusInput:$('#locpic-radius{{$id}}'),
				locationNameInput:$('#locpic-address{{$id}}')
            },
            enableAutocomplete:true,
            markerIcon: '{{ asset('assets/formbuilder/location/Map-Marker-PNG-File-70x70.png') }}'
        });
        $('#locpic{{$id}}').locationpicker('autosize');
    }

    function isFloat(n){
	    return Number(n) === n && n % 1 !== 0;
	}
	function isNumber(n){
	    return n % 1 !== 0;
	}
</script>
@endpush