@php
$useModal      = $useModal ?? false;
$custom        = $custom ?? [];
$onEdit        = [];
$onEdit2       = [];
$onClear       = [];
$rules         = $model ? $model::rule() : [];
$name          = $name ?? @$model::toKey()['snake'];
$class         = $class ?? @$model::toKey()['class'];
$exceptForeign = @$exceptForeign ?? [];
@endphp

@if($useModal)
<div class="modal fade" id="modalForm{{$model::toKey()['class']}}" tabindex="-1" role="dialog" aria-hidden="true">
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
	{!! Form::model($model, ['route' => !isset($model->id) ? [$model::toKey()['route'].".create"] : [ $model::toKey()['route'].".edit" ,['id' => $model->id] ] ,'files' => true, 'id' => 'form'.$model::toKey()['class'] ]) !!}
	@else
	{!! Form::open(['route' => $route,'files' => true]) !!}
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
			$onEditKey  = @$custom[$key]['onEdit'] ?? $key;
			$modelRule  = !is_array($value) ? explode('|', $value) : [];
			$type       = $custom[$key]['type'] ?? get_input_type($modelRule);

			$custom[$key]['elOptions']['id'] = $model::toKey()['snake'].'_'. ( !empty(@$custom[$key]['elOptions']['id']) ? $custom[$key]['elOptions']['id'] : $key );

			$foreign = isForeign($key, $exceptForeign);
			if ($foreign['status']) {
				$type  = 'select2';
				$field = ucwords( str_replace('_', ' ', $foreign['column']) );
				$custom[$key]['options'] = str_replace('_', '-', $foreign['column'] ).'.select2';
				$custom[$key]['elOptions']['placeholder'] = "Enter ".$field." here";

				$attributes['labelText'] = $field;
				$attributes['keyTerm']   = $foreign['column'];
				$related                 = getForeignClass($model, $foreign['column']);
				$select2Text             = array_map(function($val) { return "obj.$val"; }, @$related::labelText() ?? ['name']);
				$select2Text             = implode("+' - '+", $select2Text);
				$attributes['text']      = $select2Text;
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
						$onEdit2[] = "onEditInput('".$type."', '".$key."', '".$custom[$key]['elOptions']['id']."', ".$onEditKey.");";
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

	    @if($useModal)
	    </div>
	    @endif

	    <div class="{{$useModal ? 'modal-footer' : 'row' }}">
		    @if(!$useModal)
			    @if(!isset($buttonAlign) || (isset($buttonAlign) && $buttonAlign == 'horizontal'))
		            <div class="{{$custom[$key]['inputContainerClass'] ?? 'col-md-10' }} ml-auto">
		        @endif
        	@endif

        	<div class="btn-group">
	            <button type="submit" class="btn btn-primary" id="submitBtn{{$model::toKey()['class']}}">Submit</button>
	            <button type="button" class="btn btn-danger" {{$useModal ? 'data-dismiss=modal' : ''}}>Cancel</button>
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
            beforeSend: function( xhr ) {
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
                });

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
            },
            error: function(xhr, textStatus, errorThrown){
                swal({
                	title: "Gagal melakukan request",
                	text: "Silahkan hubungi admin",
                	type: "error"
                });
            }
        });
        
        $('#submitBtn{{$class}}').prop('disabled',false);

    });

	function addModal{{$model::toKey()['class']}}() {
        $('#form{{$model::toKey()['class']}}').prop('action','{{ route($model::toKey()['route'].'.create') }}');
        clear{{$model::toKey()['class']}}Input();
        $('#_method').remove();
    }

    @if(!$useModal && !empty($customVariables['id']))

    	$(document).ready(function() {
	        editModal{{$model::toKey()['class']}}('{{ route($model::toKey()['route'].'.edit', ['id' => $customVariables['id']]) }}');
		 });
    	

    @endif

    function editModal{{$model::toKey()['class']}}(url) {
        clear{{$model::toKey()['class']}}Input();
        $('#form{{$model::toKey()['class']}}').prop('action',url);
        $('#form{{$model::toKey()['class']}}').append('<input type="hidden" name="_method" value="PUT" id="_method">');

        $.ajax({
            url   : url,
            type  : 'GET',
            success: function (json) {
            	data = json.data;
            	console.log(json);
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

	function onEditInput(type, name, elementId, value, multiple = false) {
        var countingCheck = 0;

        if (type == 'select2') {
            if (multiple == true) {
                $.each(value, function(key, val){
                    countingCheck++;
                    setSelect2IfPatch($("#"+elementId), val['id'], val['name']);
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
            $("input[name='" + name + "[]'][value='" + value + "']").prop('checked', true);
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
</script>
@endsection

