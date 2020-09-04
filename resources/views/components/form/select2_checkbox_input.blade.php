@php
if ( !is_array($attributes) ) $attributes = [];
$config = App\Components\FormBuilderHelper::setupDefaultConfig($name, $attributes);
// XAMPLE
// Form::select2CheckboxInput('namanya','idnya', 'product-select2',
// 	[
// 	    'width'     => ['10','2'],
// 	    'return_id' => '',
// 	    'filter'    => 'true',
// 	    'multiple'  => 'true',
// 	]
// )
$enableSelect = "";
$select2      = "";
$js           = "";
$label        = ucwords(str_replace('_',' ',$name));
$currentId    = !empty($id) ? $id : (preg_replace(array('/[^\w]/','/^\[/','/\]$/'), '',bcrypt(str_replace(' ','',$label))));
$width        = isset($config['width']) ? $config['width'] : ['9','3'];
$retValue     = (isset($config['return_id']) ? $config['return_id'] : 'obj.id');
$route        = !empty($route) ? $route : strtolower(str_replace(' ','-',$label)).'-select2';
$filter       = isset($config['filter']) ? ($config['filter'] == 'true' ? true : false) : false;
$multi        = (isset($config["multiple"]) ? ($config["multiple"] == "true" ? "multiple='multiple'" : "") : "" );
$elOption     = isset($config['elOption']) ? $config['elOption'] : [];
$elOption     = array_merge([ 'id' => $currentId, 'multiple' => ( !empty($multi) ? 'multiple' : '' ) ], $elOption);
@endphp

<div class="{{ @$config['containerClass'] ?? 'form-group m-bot-15' }} {{ $config['useLabel'] ? '' : 'width-100' }} {{ !$errors->has($name) ?: 'has-error' }}">
	@if ( $config['useLabel'] )
	<div class="row">
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif

			@if ( !empty($config['addonsConfig']) )
			<div class="input-group">
				@if ($config['addonsConfig']['position'] === 'left')
				<span class="input-group-addon addon-left-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			@endif

			@php
                if( $multi != '' ) {
					$inputJsWithCHeck = "
                        $.each($('#$currentId').val(), function( index, value ) {
                            var splitted = value.split('`^');
                            tmpCheck$currentId.push(splitted[0]);
                        });
                        self.selected('".$currentId."', tmpCheck$currentId);
                    ";
                } else {
                    $inputJsWithCHeck = "
                        var splitted = $('#$currentId').val().split('`^');
                        self.selected('".$currentId."', splitted[0]);
                    ";
                }

                $retValue = "obj.id+'`^'+obj.name";
                $select2 =
                "<div class='row-no-margin' style='width: 100%;'>
                    <div class='col-md-$width[0]' style='padding: 0;'>
		                ".
		                Form::select($name. (!empty($multi) ? "[]" : ""), [null], null, $elOption)
		                ."
                    </div>
                    <div class='col-md-$width[1]' style='padding: 4px 0px 0px 5px;text-align: right;'>
                        <label class='css-control css-control-primary css-checkbox' style='font-size: 7pt;color: #227d82'>
                            <input type='checkbox' class='css-control-input' id='".$currentId."_all' name='".$currentId."_all'>
                            <span class='css-control-indicator'></span> All
                        </label>
                    </div>
                </div>";

                $jsCheck = "
                        if('".$currentId."' in tmpCheck$currentId){
                            if(tmpCheck".$currentId."['".$currentId."'] != ''){
                                if (Array.isArray(tmpCheck".$currentId."['".$currentId."']) && tmpCheck".$currentId."['".$currentId."'].length){
                                    console.log(splitted);
                                    console.log('multi');
                                    $.each(tmpCheck".$currentId."['".$currentId."'], function( index, value ) {
                                        var splitted = value.split('`^');
                                        setSelect2IfPatch2($('#$currentId'), splitted[0], splitted[1]);
                                    });
                                }else{
                                    var splitted = tmpCheck".$currentId."['".$currentId."'];
                                    console.log(splitted);
                                    console.log('single');
                                    splitted = splitted.split('`^');
                                    setSelect2IfPatch2($('#$currentId'), splitted[0], splitted[1]);
                                }
                            }
                        }
                    ";

                $js .= "
                    $('#".$currentId."_all').change(function(){
                        var atLeastOneIsChecked = $('#".$currentId."_all:checkbox:checked').length > 0;
                        if (atLeastOneIsChecked) {"
                            .($filter ? "filters['filter_check_all_$name'] = 'true';" : "").
                            "tmpCheck".$currentId."['".$currentId."'] = $('#$currentId').val();
                            select2Reset($('#$currentId'));
                            $('#$currentId').prop('disabled', true);
                        }else{"
                            .($filter ? "filters['filter_check_all_$name'] = 'false';" : "").
                            "$('#$currentId').prop('disabled', false);
                            $jsCheck
                        }
                    });

                    function enable$currentId(){
                        $('#$currentId').prop('disabled', false);
                        var atLeastOneIsChecked = $('#".$currentId."_all:checkbox:checked').length > 0;
                        if (atLeastOneIsChecked) {
                            $('#".$currentId."_all').click();
                        }
                    }
                ";

                $enableSelect = "enable".$currentId."()\n";
				
				echo $select2;
				
				$js      .= "
                    $('#$currentId').select2(setOptions('".route($route)."', 'Select $label', function (params) {
                        return filterData('name', params.term);
                    }, function (data, params) {
                        return {
                            results: $.map(data, function (obj) {                                
                                return {id: ". $retValue .", text: ". (isset($config['return_text']) ? $config['return_text'] : 'obj.name') ."}
                            })
                        }
                    }));
                ";

	            $currentClear = $enableSelect."select2Reset($('#$currentId'));";

	            if($filter){
		            $script = $js."
		                $('#".$currentId."').on('change', function () {
		                    ". (!empty($inputJsWithCHeck) ? $inputJsWithCHeck : "self.selected('filter_".$value['name']."', $('#".$currentId."').val());" ) ."
		                });
		            ";
		        }else{
		            $script = $js;
		        }
			@endphp

			@if ( !empty($config['addonsConfig']) )
				@if ($config['addonsConfig']['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			</div>
			@endif

			@if ( $errors->has($name) )
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ( $config['useLabel'] )
		</div>
	</div>
	@endif
</div>

@push('additional-js')
<script type="text/javascript">
    var tmpCheck{{$currentId}} = [];
	{!!$script!!}	
</script>
@endpush