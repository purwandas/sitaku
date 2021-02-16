@php
$useModal      = $useModal ?? false;
$custom        = $custom ?? [];
$tmpCustom 	   = $custom;
$onEdit        = [];
$onEdit2       = [];
$onClear       = [];
$rules         = $model ? $model::rule() : [];
$name          = $name ?? @$model::toKey()['snake'];
$class         = $class ?? @$model::toKey()['class'];
$exceptForeign = @$exceptForeign ?? [];
$classRow      = ['class'=>'row'];
@endphp

@if($useModal)
<?php $classRow = []; ?>
<div class="modal fade" id="modalForm{{$model::toKey()['class']}}" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">{{@$title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

@push('additional-css')
	<style type="text/css">
		.select2-container{
			width: 100% !important;
		}
	    .swal2-container.swal2-shown {
	        z-index: 111111;
	    }
	</style>
@endpush

@endif

<div class="col-lg-12">

	@if(!isset($standalone) || !$standalone)
	{!! Form::model($model, array_merge(['route' => !isset($model->id) ? [$model::toKey()['route'].".create"] : [ $model::toKey()['route'].".edit" ,['id' => $model->id] ] ,'files' => true, 'id' => 'form'.$model::toKey()['class']], $classRow)) !!}
	@else
	{!! Form::open(array_merge(['route' => $route,'files' => true], $classRow)) !!}
	@endif

	    @if($useModal)
	    <div class="modal-body">
	    @endif

	    @php	
	    $except = $except ?? [];
		$except = is_array($except) ? $except : [];
		removeArrayByKey($rules, $except);
		removeArrayByKey($rules, array_keys($custom));

		$rules = array_merge($rules, $custom);
	    @endphp
		
		@foreach($rules as $key => $value)
			@php
			$attributes = $custom[$key] ?? [];
			$attributes = array_merge(isset($formAlignment) ? ['formAlignment' => $formAlignment] : [], $attributes);
			$onEditKey  = @$custom[$key]['onEdit'] ?? $key;
			$modelRule  = !is_array($value) ? explode('|', $value) : [];
			$type       = $custom[$key]['type'] ?? get_input_type($modelRule);

			$custom[$key]['elOptions']['id'] = $model::toKey()['snake'].'_'. ( !empty(@$custom[$key]['elOptions']['id']) ? $custom[$key]['elOptions']['id'] : $key );

			$foreign = isForeign($key, $exceptForeign);
			if ($foreign['status'] && !array_key_exists($key, $tmpCustom)) {
				$type  = 'select2';
				$field = ucwords( str_replace('_', ' ', $foreign['column']) );
				$custom[$key]['options'] = str_replace('_', '-', $foreign['column'] ).'.select2';
				$custom[$key]['elOptions']['placeholder'] = "Enter ".$field." here";

				if (!array_key_exists('text', $attributes)) {
					$related                 = getForeignClass($model, $foreign['column']);
					$select2Text             = array_map(function($val) { return "obj.$val"; }, @$related::labelText() ?? ['name']);
					$select2Text             = implode("+' | '+", $select2Text);
					$attributes['text']      = $select2Text;
				}
				if (!array_key_exists('labelText', $attributes)) {
					$attributes['labelText'] = $field;
				}
				if (!array_key_exists('keyTerm', $attributes)) {
					$attributes['keyTerm']   = @$related::labelText()[0] ? '_'.$related::labelText()[0] : '_name';
				}
			} elseif ($type == 'select2') {
				$foreign = isForeign($key);
				if (!array_key_exists('text', $attributes) && $foreign['status']) {
					$related                 = getForeignClass($model, $foreign['column']);
					$select2Text             = array_map(function($val) { return "obj.$val"; }, @$related::labelText() ?? ['name']);
					$select2Text             = implode("+' | '+", $select2Text);
					$attributes['text']      = $select2Text;
				}
				if (!array_key_exists('keyTerm', $attributes) && $foreign['status']) {
					$attributes['keyTerm']   = $foreign['column'];
				}
				if (!array_key_exists('labelText', $attributes)) {
					$field = ucwords( str_replace('_', ' ', $foreign['column']) );
					$attributes['labelText'] = $field;
				}
			}

			$elOptions = collect(($custom[$key]['elOptions']) ?? []);

			//Get Min/Max Attribute
			$result = array_filter(
			    $modelRule,
			    function( $row ){
			        return (strpos( $row, ':' ) !== False);
			    }
			);  

			if(count($result) > 0){
				foreach($result as $res){
					$expRes = explode(':', $res);
					$elOptions->put($expRes[0]."length",$expRes[1]);
				}
			}

			if(in_array('required', $modelRule)) $elOptions->put('required','required');

			if(!isset($custom[$key]['elOptions']['placeholder'])){
				$elOptions->put('placeholder',"Enter ".ucwords( str_replace('_', ' ', $key) )." here");
			}

			$attributes['elOptions'] = $elOptions->toArray();
			
			$inputType = $type.'Input';
			

			// Form Section START
				switch ($type) {
					case 'file':
						// Next Update
						break;

					case 'password':
						$onEdit[]  = "$('#".$custom[$key]['elOptions']['id']."').val('');";
						$onClear[] = "$('#".$custom[$key]['elOptions']['id']."').val('');";
						break;

					case 'hidden':
						$onEdit[]  = "$('#".$custom[$key]['elOptions']['id']."').val(data.".$key.");";
						// No Action
						break;

					case 'switch':
						// Next Update
						break;

					case 'location':
						$onEditKey = !is_array($onEditKey) ? $onEditKey : ['latitude','longitude','address'];
						$onEdit[]  = "onEditInput('$value[type]','$value[name]','".$custom[$key]['elOptions']['id']."Input".$custom[$key]['elOptions']['id']."',{'latitude':json.$onEditKey[0],'longitude':json.$onEditKey[1]});";
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', {'latitude':data.$onEditKey[0], 'longitude':data.$onEditKey[1], 'address':data.$onEditKey[2]});";
			            $onClear[] = "initMap".$custom[$key]['elOptions']['id']."([]);";
						break;

					case 'multiplecolumn':
						$custom[$key]['options'] = $custom[$key]['columns'];
						// Next Update
						// $onEdit2[] = "onEditInput('".$type."', '".$key."', '', ".$onEditKey.");";
						// $onClear[] = "clearMultiple".$custom[$key]['elOptions']['id']."();";
						break;
					case 'multipleColumn':
						// Next Update
						break;

					case 'select2Checkbox':
						$onEditKey = toArrayEdit($key, $onEditKey);
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', [".$onEditKey."]);";
			            $onClear[] = "select2Reset($('#".$custom[$key]['elOptions']['id']."'));";
						break;

					case 'selectTree':
						$onEditKey = toArrayEdit($key, $onEditKey);
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', [".$onEditKey."]);";
						$onClear[] = "reset'".$custom[$key]['elOptions']['id']."'Tree();";
						break;

					case 'multiple':
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '', ".$onEditKey.");";
						$onClear[] = "clearMultiple".$custom[$key]['elOptions']['id']."();";
						$custom[$key]['options'] = [];
						break;

					case 'select2Multiple':
						$onEditKey = toArrayEdit($key, $onEditKey);
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', [".$onEditKey."], true);";
			            $onClear[] = "select2val_".$custom[$key]['elOptions']['id']." = [];generateTable_".$custom[$key]['elOptions']['id']."();";
						break;

					case 'select2':
						$multi     = !empty(@$custom[$key]['elOptions']['multiple']) ? 'true' : 'false';
						$onEditKey = toArrayEdit($key, $onEditKey);
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', [".$onEditKey."], ".$multi.");";
			            $onClear[] = "select2Reset($('#".$custom[$key]['elOptions']['id']."'));";
						break;

					case 'checkbox':
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', ".$onEditKey.");";
						$onClear[] = "$(\"input[name='".$key."[]']\").prop('checked', false);";
						break;

					case 'radio':
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', data.".$key.");";
						$onClear[] = "$(\"input[name='".$key."[]']\").prop('checked', false);";
						break;

					case 'textarea':
						if((isset($custom[$key]['withPlugins']) && $custom[$key]['withPlugins'])){
							$onEdit[]  = "$('#".$custom[$key]['elOptions']['id']."').summernote('code',data.".$key.");";
							$onClear[] = "$('#".$custom[$key]['elOptions']['id']."').summernote('code', '');";
						}else{
							$onEdit[]  = "$('#".$custom[$key]['elOptions']['id']."').val(data.".$key.");";
							$onClear[] = "$('#".$custom[$key]['elOptions']['id']."').val('');";
						}
						break;

					default:
						$onEdit[]  = "$('#".$custom[$key]['elOptions']['id']."').val(data.".$key.");";
						$onClear[] = "$('#".$custom[$key]['elOptions']['id']."').val('');";
						break;
				}
			// Form Section END
			@endphp

			@isset($custom[$key]['options'])

			{{ Form::$inputType($key,$custom[$key]['value'] ?? old($key) ,$custom[$key]['options'] ?? [],$attributes) }}

			@else
			
			{{ Form::$inputType($key,$custom[$key]['value'] ?? old($key) ,$attributes) }}

			@endisset

	    @endforeach

	    @php
		$onEdit  = implode('
			', $onEdit);
		$onEdit2 = implode('
			', $onEdit2);
		$onClear = implode('
			', $onClear);
	    @endphp

	    {{-- {{ @$slot }} --}}
	    <div class="form-group progress progressForm" style="display:none;width: 100%;">
			<div class="progressBarForm progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
				<span class="sr-only">0%</span>
			</div>
		</div>

	    @if($useModal)
	    </div>
	    @endif

	    <div class="{{$useModal ? 'modal-footer' : 'col-md-12 row' }}">
		    @if(!$useModal)
			    @if(!isset($buttonAlign) || (isset($buttonAlign) && $buttonAlign == 'horizontal'))
		            <div class="{{$custom[$key]['inputContainerClass'] ?? 'col-md-10' }} ml-auto">
		        @endif
        	@endif

        	<div class="btn-group">
	            <button type="submit" class="btn btn-sm btn-primary" id="submitBtn{{$model::toKey()['class']}}" style="padding: 0.375rem 0.75rem !important;">Submit</button>
	            <button type="button" class="btn btn-sm btn-danger" {{$useModal ? 'data-dismiss=modal' : 'onclick=backToIndex()'}} style="padding: 0.375rem 0.75rem !important;">Cancel</button>
            </div>

		    @if(!$useModal)
		        @if(!isset($buttonAlign) || (isset($buttonAlign) && $buttonAlign == 'horizontal'))
		            </div>
		        @endif
		    @endif
        </div>

    {!! Form::close() !!}
</div>

@if($useModal)

		</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@endif

@push('additional-js')
<script type="text/javascript">
	var pb = $('.progressForm').html();
	$('#form{{$class}}').submit(function(event) {
        event.preventDefault();
        $('#submitBtn{{$class}}').prop('disabled',true);

        // Next Update
        //validate{{$class}}();

        // Get form
        var form = $('#form{{$class}}')[0];

       // Create an FormData object 
        var formData = new FormData(form);

		var url      = $('#form{{$class}}').attr('action');

        $.ajax({
            url   : url,
            type  : 'POST',
            data  : formData,
			cache: 			false,
			contentType: 	false,
			processData: 	false,
			xhr : function() {
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener('progress', function(e){
					(async function() {
						if(e.lengthComputable){
							var percent = await Math.round((e.loaded / e.total) * 100);
							
							await $('.progressForm .progressBarForm').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
						}
					})();
				});
				return xhr;
			},
            beforeSend: function( xhr ) {
            	initProgressBar();
				$('.progressForm').show();
			    // xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
			},
			success: function (data) {
                if (!data.status) {
                    console.log('there was something error');
                    swal({
	                	title: "Gagal melakukan request",
	                	text: "Silahkan hubungi admin",
	                	type: "error"
	                });
                    return;
                }
                swal({
                    title: data.title,
                    text: data.msg,
                    type: data.type,
                }).then(function(result){
		            if (result.value) {
		                @if(!$useModal)
		                	window.location.href = '{{ route($model::toKey()['route'].'.index') }}';
		                @else
			                @if(empty($customVariables['id']) || !isset($customVariables['id']))

				                try {
					                {{$name}}_reloadTable();
								}
								catch(err) {
								  // alert("no datatable");
								  	try {
						                loadData();
									}
									catch(err) {
									  alert("no data loaded");
									}
								}
					            $('#modalForm{{$class}}').modal('toggle');
					            $('body').removeClass('modal-open');
								$('.modal-backdrop').remove();

							@endif
		                @endif
		            }
		        });
				$('.progressForm').hide();
            },
            error: function(xhr, textStatus, errorThrown){
                swal({
                	title: "Gagal melakukan request",
                	text: xhr.responseJSON.msg ?? "Silahkan hubungi admin",
                	type: "error"
                });
				$('.progressForm').hide();
            }
        });
        
        $('#submitBtn{{$class}}').prop('disabled',false);

    });

    function initProgressBar() {
    	$('.progressForm').html(pb);
    }

    @if(!$useModal)
    	function backToIndex() {
	    	swal({
	            title: 'Are you sure?',
	            text: 'Your data will not be saved!',
	            type: 'warning',
	            showCancelButton: true,
	            confirmButtonText: 'Yes',
	            cancelButtonText: 'No',
	            html: false,
	            preConfirm: function() {
	                return new Promise(function (resolve) {
	                    setTimeout(function () {
	                        resolve();
	                    }, 50);
	                });
	            }
	        }).then(function(result){
	            if (result.value) {
	                window.location.href = '{{ route($model::toKey()['route'].'.index') }}';
	            }
	        });
	    }

	    @if (!empty($customVariables['id']) && empty(@$dontEdit))
    	$(document).ready(function() {
	        editModal{{$model::toKey()['class']}}('{{ route($model::toKey()['route'].'.edit', ['id' => $customVariables['id']]) }}');
		});
		@endif
    @endif

    @if (empty(@$dontEdit))
	function addModal{{$model::toKey()['class']}}() {
        $('#form{{$model::toKey()['class']}}').prop('action','{{ route($model::toKey()['route'].'.create') }}');
        clear{{$model::toKey()['class']}}Input();
        $('#_method').remove();
    }

    function editModal{{$model::toKey()['class']}}(url) {
        @if (empty($customVariables['id']))
        clear{{$model::toKey()['class']}}Input();
        @endif

        $('#form{{$model::toKey()['class']}}').prop('action',url);
        $('#form{{$model::toKey()['class']}}').append('<input type="hidden" name="_method" value="PUT" id="_method">');

        $.ajax({
            url   : url,
            type  : 'GET',
            success: function (json) {
            	data = json.data;
                if (!data.id) {
                    console.log('there was something error');
                    swal({
	                	title: "Gagal melakukan request",
	                	text: "Silahkan hubungi admin",
	                	type: "error"
	                });
                    return;
                }
                {!!@$onEdit!!}
		        setTimeout(function() {
			        {!!@$onEdit2!!}
		        }, 500);
		        if (typeof dataEdit !== "undefined") {
		        	dataEdit = data;
		        }
		        if (typeof setupInjector === "function") {
		        	setupInjector();
		        }
		    },
            error: function(xhr, textStatus, errorThrown){
                swal({
                	title: "Gagal melakukan request",
                	text: "Silahkan hubungi admin",
                	type: "error"
                });
            }
        });
    }

    function clear{{$model::toKey()['class']}}Input() {
        {!!@$onClear!!}
    }
    @endif

</script>
@endpush

@section('function-js')
<script type="text/javascript">
	@if (!empty(@$onEdit2))
	function setSelect2IfPatch(element, id, text){

	    element.select2("trigger", "select", {
	        data: { id: id, text: text }
	    });

	    // Remove validation of success
	    element.closest('.form-group').removeClass('has-success');

	    var span = element.parent('.input-group').children('.input-group-addon');
	    span.addClass('display-hide');

	    // Remove focus from selection
	    element.next().removeClass('select2-container--focus');

	}

	function select2Reset(element){

	    element.select2('val','All');

	    // Remove validation of success
	    element.closest('.form-group').removeClass('has-success');

	    var span = element.parent('.input-group').children('.input-group-addon');
	    span.addClass('display-hide');

	    // Remove focus from selection
	    element.next().removeClass('select2-container--focus');

	}
	@endif

    @if (empty(@$dontEdit))
	function onEditInput(type, name, elementId, value, multiple = false) {
        var countingCheck = 0;

        if (type == 'select2') {
            if (multiple == true) {
                $.each(value, function(key, val){
                    countingCheck++;
                    if (val) {
                    	setSelect2IfPatch($("#"+elementId), val[key]['id'], val[key]['name']);
                    }
                });
            }else{
                setSelect2IfPatch($("#"+elementId), value[0], value[1]);
            }
        } else if (type == 'select2Checkbox') {
            $.each(value, function(key, val){
                countingCheck++;
                setSelect2IfPatch($("#"+elementId), val['id'], val['name']);
            });
            if (countingCheck == 0) {
                var checkId = "#"+elementId+"_all:checkbox:checked";
                var atLeastOneIsChecked = $(checkId).length > 0;
                if (!atLeastOneIsChecked) {
                    $("#"+elementId+"_all").click();
                }
            }
        } else if (type == 'location') {
            var fnName = "initMap" + elementId;
            var params = [ parseFloat(value[0]) , parseFloat(value[1]) , value[2] ];

            window[fnName](params);
        } else if (type == 'checkbox') {
            $.each(value, function(i, val){
               $("input[name='" + name + "[]'][value='" + val + "']").prop('checked', true);
            });
        } else if (type == 'radio') {
            $("input[name='" + name + "'][value='" + value + "']").prop('checked', true);
        } else if (type == 'select-multi') {
            var varName = "select2val_"+ elementId;
            window[varName] = [];
            $.each(value, function( i, v ) {
                window[varName].push({id:"'"+v['id']+"'",name:"'"+v['name']+"'"});
            });

            window["generateTable_"+ elementId]();
        } else if (type == 'select3') {
            setSelect2IfPatch2( $("#"+$('#div'+elementId).next().find('select')[1].id), value['id'], value['name'] );
        } else if (type == 'multiple') {
        	$.each(value, function( i, v ) {
	        	$(".multipleInput_addRowBtn-"+elementId).closest('.multipleInput_container').append("<div class='input-group ' style='margin-bottom: 10px'><input type='text' name='"+name+"[]' value='"+v+"' class='form-control' placeholder='Please enter "+name+" here'><button type='button' class='btn btn-danger addon-right-side multipleInput_removeRowBtn'><i class='fa fa-times'></i></button></div>");
            });
        } else {
            $("#"+elementId).val(value);
        }

    }
	@endif
</script>
@endsection

