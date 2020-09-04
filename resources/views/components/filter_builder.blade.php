@php
$useModal      = true;
$custom        = $custom ?? [];
$filterId      = [];
$name          = $name ?? @$model::toKey()['snake'];
$mRoute        = $model::toKey()['route'];
$exceptForeign = @$exceptForeign ?? [];
$rules         = $model::rule();
$except        = $exceptFilter ?? [];
$custom        = $customFilter ?? [];
$except        = is_array($except) ? $except : [];
removeArrayByKey($rules, $except);
removeArrayByKey($rules, array_keys($custom));

$rules = array_merge($rules, $custom);
@endphp

@if($useModal)
<button type="button" data-toggle="modal" data-target="#filter-modal" class="btn btn-warning btn-sm act-btn display-hide">{!! getSvgIcon('fa-filter','mt-m-2') !!} Filter</button>

<div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Advanced Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

@push('additional-css')
	<style type="text/css">
		.act-btn{
	        transform: rotate(-90deg);
	        -webkit-transform: rotate(-90deg);
	        -moz-transform: rotate(-90deg);
	        display: block;
	        font-size: 15px;
	        font-weight: bold;
	        text-decoration: none;
	        transition: ease all 0.3s;
	        position: fixed;
	        top: 50%;
	        right: -20px;
	        z-index: 9999;
	    }
		.select2-container{
			width: 100% !important;
		}
		.mb-15{
			margin-bottom: 15px;
		}
	</style>
@endpush

@endif

<div class="col-lg-12">
	{{ Form::model($model, ['route' => !isset($model->id) ? [$mRoute.".create"] : [ $mRoute.".edit" ,['id' => $model->id] ] , 'enctype' => 'multipart/form-data', 'id' => 'filter_form'.$name ]) }}

	    @if($useModal)
	    <div class="modal-body" style="padding-bottom: 0;">
	    	<div class="row"> 
	    @endif

		@foreach($rules as $key => $value)
			@php

			$attributes = $custom[$key] ?? [];
			$onEditKey  = @$custom[$key]['onEdit'] ?? $key;
			$modelRule  = explode('|', $value);
			$type       = $custom[$key]['type'] ?? get_input_type($modelRule);

			$custom[$key]['elOptions']['id'] = 'filter_'.$name.'_'. ( !empty(@$custom[$key]['elOptions']['id']) ? $custom[$key]['elOptions']['id'] : $key );

			$foreign = isForeign($key, $exceptForeign);
			if ($foreign['status'] && !empty($model)) {
				$type                                     = 'select2';
				$field                                    = ucwords( str_replace('_', ' ', $foreign['column']) );
				$custom[$key]['options']                  = str_replace('_', '-', $foreign['column'] ).'.select2';
				$custom[$key]['elOptions']['placeholder'] = "Select ".$field." here";

				$attributes['labelText'] = $field;
				$attributes['keyTerm']   = $foreign['column'];
				$related                 = getForeignClass($model, $foreign['column']);
				$select2Text             = array_map(function($val) { return "obj.$val"; }, @$related::labelText() ?? ['name']);
				$select2Text             = implode("+' - '+", $select2Text);
				$attributes['text']      = $select2Text;

			} elseif ($type == 'date' || $type == 'datetime') {
				$type                                     = 'daterange';
				$field                                    = ucwords( str_replace('_', ' ', $key) );
				$attributes['labelText']                  = $field;
				$custom[$key]['elOptions']['placeholder'] = "Select ".$field." here";

			} elseif ($type == 'hidden') {
				$type                                     = 'hidden';
				$field                                    = ucwords( str_replace('_', ' ', $key) );
			} else {
				$type                                     = 'select2';
				$field                                    = ucwords( str_replace('_', ' ', $key) );
				$custom[$key]['options']                  = $mRoute.'.select2';
				$custom[$key]['elOptions']['placeholder'] = "Select ".$field." here";
				$attributes['labelText']                  = $field;
				$attributes['key']                        = 'obj.'.$key;
				$attributes['text']                       = 'obj.'.$key;
				$attributes['keyTerm']                    = '_'.$key;
				$attributes['ajaxParams']                 = ['groupBy' => "'".$key."'"];

			}

            $filterId[] = "'#".$custom[$key]['elOptions']['id']."'";
			$elOptions  = collect(($custom[$key]['elOptions']) ?? []);

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
				$elOptions->put('placeholder',"Enter $key here");
			}

			$attributes['elOptions'] = $elOptions->toArray();
			
			$inputType = $type.'Input';
			@endphp
				@isset($custom[$key]['options'])

				{{ Form::$inputType($key,$custom[$key]['value'] ?? old($key) ,$custom[$key]['options'] ?? [],array_merge($attributes,[ 'pluginOptions' => ['multiple'=>true], 'containerClass'=>'col-md-3 mb-15', 'useLabel' => false])) }}

				@else
				
				{{ Form::$inputType($key,$custom[$key]['value'] ?? old($key) ,array_merge($attributes,['containerClass'=>'col-md-3 mb-15', 'useLabel' => false])) }}

				@endisset
	    @endforeach

	    @php
		$diff = array_diff_key($custom,$model::rule());
		@endphp

	    @foreach($diff as $key => $attributes)
			
			@php
			$inputType = $attributes['type'].'Input';
			@endphp
				@isset($attributes['options'])

				{{ Form::$inputType($key,$attributes['value'] ?? old($key) ,$attributes['options'] ?? [],array_merge($attributes,['containerClass'=>'col-md-3 mb-15'])) }}

				@else
				
				{{ Form::$inputType($key,$attributes['value'] ?? old($key) ,array_merge($attributes,['containerClass'=>'col-md-3 mb-15'])) }}

				@endisset
	    @endforeach
	    
	    {{-- {{ @$slot }} --}}

	    @if($useModal)
			</div>
	    </div>
	    @endif

	    <div class="{{$useModal ? 'modal-footer' : 'row' }}">
		    @if(!$useModal)
			    @if(!isset($buttonAlign) || (isset($buttonAlign) && $buttonAlign == 'horizontal'))
		            <div class="{{$custom[$key]['inputContainerClass'] ?? 'col-md-10' }} ml-auto">
		        @endif
        	@endif

        	<div class="btn-group" style="padding-left: 10px;">
                <button type="button" id="submitFilterButton" class="btn btn-primary" data-dismiss="modal" onclick="filteringReport({{$name}}_paramFilter,{{ $timeout ?? '1.5' }})"><i class="fa fa-filter"></i> Submit Filter</button>
                <button type="button" id="resetFilterButton" class="btn btn-danger" data-dismiss="modal" onclick="triggerReset({{$name}}_paramReset)"><i class="fas fa-sync"></i> Reset Filter</button>
            </div>

		    @if(!$useModal)
		        @if(!isset($buttonAlign) || (isset($buttonAlign) && $buttonAlign == 'horizontal'))
		            </div>
		        @endif
		    @endif
        </div>

    {{Form::close()}}

</div>

@if($useModal)
		</div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
@endif

@push('additional-js')
<script type="text/javascript">
	var {{$name}}_filterId    = [{!! implode(', ',$filterId) !!}];
	var {{$name}}_paramFilter = [{{$name}}_tableId, $('#'+{{$name}}_tableId), {{$name}}_tableUrl, {{$name}}_tableColumns, {{$name}}_tableColumnDefs, {{$name}}_tableOrder, '#exportIdXampleSoon'];
	var {{$name}}_paramReset  = [{{$name}}_filterId, {{$name}}_tableId, $('#'+{{$name}}_tableId), {{$name}}_tableUrl, {{$name}}_tableColumns, {{$name}}_tableColumnDefs, {{$name}}_tableOrder, '#exportIdXampleSoon'];

	function adjustTableDisplay(timeout = '') {
	    setTimeout(function(){
	        var width = $('.dataTables_scrollHeadInner').width(), width2 = 0;
	        $('.dataTable').css('width','100%');
	        $('.dataTables_scrollHeadInner').css('width','100%');
	        width2 = $('.dataTables_scrollHeadInner').width();
	        if (width > width2) {
	            $('.dataTables_scrollHeadInner').css('width',width);
	        }
	    }, (timeout != '' ? timeout * 1000 : 1500) );
	}

	// Filtering data with action callback
	function filteringReport(arrayOfData, timeout = '') {
		var table        = arrayOfData[0];
		var newElement   = $('#'+table);
		var url          = arrayOfData[2];
		var tableColumns = arrayOfData[3];
		var columnDefs   = arrayOfData[4];
		var order        = arrayOfData[5];

	    $(document).ready(function () {
	        {{$name}}_setupTable(table, newElement, order, columnDefs, tableColumns, url, true);
	        adjustTableDisplay(timeout);
	    });
	}

	function triggerReset(arrayOfData) {
		var data         = arrayOfData[0];
		var table        = arrayOfData[1];
		var newElement   = $('#'+table);
		var url          = arrayOfData[3];
		var tableColumns = arrayOfData[4];
		var columnDefs   = arrayOfData[5];
		var order        = arrayOfData[6];

	    data.map((id) => {
	        $(id).prop('disabled', false);
	        if ( $(id).is(':checkbox') ) {
	            $(id).prop('checked',false);
	        }else{
	            $(id).val('').trigger('change');
	            if($(id).hasClass('default-select')){$(id).prop("selectedIndex", 0).val();}
	        }
	    });

	    this.filters = {};

	    {{$name}}_setupTable(table, newElement, order, columnDefs, tableColumns, url, false);

	}
</script>
@endpush