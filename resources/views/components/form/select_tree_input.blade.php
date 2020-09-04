@php
if (!is_array($attributes)) $attributes = [];
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes);

$script        = '';
$padding       = '';
$prevValue     = '';
$prevTitle     = '';
$nextValue     = '';
$nextTitle     = '';
$nextSelector  = '';
$closure       = '</div>';
$treeId        = !empty(@$config['treeId']) ? ucfirst($config['treeId']) : '';
$left          = !empty(@$config['left']) ? $config['left'] : '8';
$right         = !empty(@$config['right']) ? $config['right'] : '4';
// $route         = !empty(@$config['routes']) ? $config['routes'] : '';
$firstSelector = $treeId.str_replace(' ','', ucwords(str_replace('_',' ',$input[0])));
$prevSelector  = $treeId.'Parent';
@endphp

<div class="width-100 {{ @$config['containerClass'] ?? 'form-group' }} {{ !$errors->has($name) ?: 'has-error' }}">
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

			@php
				echo 
				  	"
				    <div style='display: none;'>
				      <select>
				        <option value=''>trigger for required popup</option>
				      </select>
				    </div>
				  	";

				foreach ( $input as $key => $value ) {

					$title        =  ucwords(str_replace('_',' ',$value));
				    $route        =  isset($routes[$key]) ? $routes[$key] : str_replace('_','-',$value).'.select2';
				    $selector     =  $treeId.str_replace(' ','',$title);
				    $nextValue    = !empty($input[$key + 1]) ? $input[$key + 1] : '';
					$nextTitle    = !empty($nextValue) ? ucwords(str_replace('_',' ',$nextValue)) : '';
					$nextSelector = $treeId.str_replace(' ','',$nextTitle);
					$required     = ($key == 0) ? ['required'    => 'required'] : [];

				    $last     = ( count($input) == ($key+1) ? 1 : 0 );
				    $script   .= "
				      	var select".$selector." = '';
				      	$('#".$selector."Select').select2(setOptions('". route($route) ."', 'Please select ".strtolower($title)." here', function (params) {
				        	return filterData('name', params.term);
				      	}, function (data, params) {
				        	return {
					          	results: $.map(data, function (obj) {                                
					            	return {id: obj.id+'`^'+obj.name, text: obj.name}
					          	})
				        	}
				      	}));
				      	$('#new".$selector."Checkbox').change(function(){
				        	var atLeastOneIsChecked = $('#new".$selector."Checkbox:checkbox:checked').length > 0;
				        	if (atLeastOneIsChecked) {
					          	select".$selector." = $('#".$selector."Select').val();
					          	select2Reset($('#".$selector."Select'));
					          	$('#".$selector."Select').prop('disabled', true);
					          	$('#new_".$selector."').prop('required',true);
					          	$('#".$selector."Select').prop('required',false);
					          	$('#new".$selector."Content').css('display','block');
					          	".
					          	(!empty($nextValue) ? "$('#".$nextSelector."Select').prop('required',true);":"")
					          	."
				        	}else{
					          	$('#new".$selector."Content').css('display','none');
					          	$('#".$selector."Select').prop('disabled', false);
					          	$('#".$selector."Select').prop('required',true);
					          	$('#new_".$selector."').prop('required',false);
					          	".
					          	(!empty($nextValue) ? "$('#".$nextSelector."Select').prop('required',false);":"")
					          	."
					          	if (select".$selector.") {
					            	var splitted = select".$selector.".split('`^');
					            	setSelect2IfPatch2($('#".$selector."Select'), splitted[0], splitted[1]);
					          	}
				        	}
				      	});
				      	";
					$closure .= "</div>";
					$padding = ($key > 0) ? 'padding-left: 10px;display: none;' : '';
					echo "<div id='new".$prevSelector."Content' class='col-md-12 col-sm-12' style='padding: 0;".$padding."'>";
				          	if ($prevValue != '') {
				          		echo "<div class='col-md-12 col-sm-12' style='padding-right: 0;padding-left: 0;margin-bottom: 5px;'>";
								echo Form::text("new_".$prevValue, null, ['class'=>'form-control', 'id'=>'new_'.$prevSelector, 'placeholder'=>'Please enter New '.strtolower($prevTitle).' here'] );
						        echo "</div>";
				          	}
				          	echo
				          	"
				          	<div class='input-group mb-3 col-sm-12 col-md-12' style='padding: 0;margin-bottom: 5px !important;'>
				            	<div class='col-md-". $left ." col-sm-12' style='padding: 0'>
				            ";
					            echo Form::select("s_".$value, [null], null,
									array_merge([ 
										'class'       => 'form-control', 
										'id'          => $selector.'Select', 
										'placeholder' => 'Please select '.strtolower($prevTitle).' here',
										'style'       => 'width: 100%', 
									], $required)
								);
				            echo
				            "
				            	</div>
				            	<div class='input-group-append col-md-". $right ." col-sm-12 padding-r-0'>
				              	<label class='css-control css-control-primary css-switch pos-abs-r' style='font-size: 7pt;color: #227d82;'>
				                  	<input type='checkbox' class='css-control-input' id='new".$selector."Checkbox' name='new".$selector."Checkbox'>
				                  	<span class='css-control-indicator'></span> New
				              	</label>
				            	</div>
				          	</div>
				      	";
				    if ( $last == 1 ) {
				      	echo 
				        "
				          <div id='new".$selector."Content' class='input-group col-sm-12 col-md-12' style='padding-right: 0;display:none;".$padding."'>
				            <div class='col-md-12 col-sm-12' style='padding-right: 0;padding-left: 0;'>
				        ";
						echo Form::text("new_".$value, null, ['class'=>'form-control', 'id'=>'new_'.$selector, 'placeholder'=>'Please enter New '.strtolower($prevTitle).' here']);
				        echo
				        "
				            </div>
				        ";
				    }
				    $prevValue    = $value;
				    $prevTitle    = $title;
				    $prevSelector = $selector;
				}

				echo $closure;
			@endphp



			@if ( !empty($config['addonsConfig']) )
				@if ($config['addonsConfig']['position'] === 'right')
				<span class="input-group-addon addon-right-side">{{ $config['addonsConfig']['text'] }}</span>
				@endif
			</div>
			@endif

			@if( $errors->has($name) )
			<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
			@endif

	@if ( $config['useLabel'] )
		</div>
	</div>
	@endif
</div>

@push('function-js')
<script type="text/javascript">
  	{!!$script!!}
	
  	function reset{{$treeId}}Tree() {
    	@php
    	foreach ( $input as $key => $value ) {
			$selector =  $treeId.str_replace(' ','',ucwords(str_replace('_',' ',$value)));
      		echo
        	"
	        	var checked = $('#new".$selector."Checkbox:checkbox:checked').length > 0;
	        	if (checked) {
		          	$('#new".$selector."Checkbox').click();
	        	}
	        	$('#new_".$selector."').val('');
	        	select2Reset($('#".$selector."Select'));
        	";
    	}
    	@endphp
  	}
	
  	function set{{$treeId}}SelectTree(id, name){
    	setSelect2IfPatch2($("#{{$firstSelector}}Select"), id+'`^'+name, name);
  	}
</script>
@endpush