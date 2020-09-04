@php
if (!is_array($attributes)) $attributes = [];
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes);
// $id     = isset($config['elOptions']['id']) ? $config['elOptions']['id'] : preg_replace( array('/[^\w]/','/^\[/','/\]$/'), '', bcrypt($name) );
// $id = $config['elOptions']['id'] = $model::toKey()['snake'].'_'. ( !empty(@$config['elOptions']['id']) ? $config['elOptions']['id'] : $name );
$defaultConfigs = [
					'dialogsInBody' => true,
		    	    'codemirror' => [
				    	'mode' => 'text/html',
				    	'htmlMode' => true,
				    	'lineNumbers' => true,
				    	'theme' => 'monokai'
		    	    ],
		        	'height' => 300,
				];

$newConfigs = array_merge($defaultConfigs,$config['pluginOptions'] ?? []);
@endphp

{{-- <div class="form-group {{ !$errors->has($name) ?: 'has-error' }}"> --}}
<div class="{{ @$config['containerClass'] ?? 'form-group row'}}">
	@if ($config['useLabel'])
	{{-- <div class="row"> --}}
		<div class="{{ $config['labelContainerClass'] }}">
			<label class="col-form-label">
				{!! $config['labelText'] !!}
			</label>
		</div>
		<div class="{{ $config['inputContainerClass'] }}">
	@endif

	{{ Form::textarea($name, $value, $config['elOptions']) }}

	@if($errors->has($name))
	<span id="helpBlock2" class="help-block">{{ $errors->first($name) }}</span>	
	@endif

	@if ($config['useLabel'])
		</div>
	{{-- </div> --}}
	@endif
</div>

@if(isset($config['withPlugins']) && $config['withPlugins'] == true)
	@section('summernote-css')
		<link rel="stylesheet" type="text/css" href="{{asset('assets/formbuilder/summernote/codemirror.css')}}">
		<link rel="stylesheet" type="text/css" href="{{asset('assets/formbuilder/summernote/monokai.css')}}">
	    <link rel="stylesheet" href="{{asset('assets/formbuilder/summernote/summernote-bs4.min.css')}}">
	    <style type="text/css">
	    	.modal-content .modal-body p{
	    		color: #000 !important;
	    	}
	    </style>
	@endsection

	@section('summernote-js')
		<script type="text/javascript" src="{{asset('assets/formbuilder/summernote/codemirror.js')}}"></script>
		<script type="text/javascript" src="{{asset('assets/formbuilder/summernote/xml.js')}}"></script>
		<script type="text/javascript" src="{{asset('assets/formbuilder/summernote/formatting.js')}}"></script>
	    <script src="{{asset('assets/formbuilder/summernote/popper.min.js')}}" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	    <script src="{{asset('assets/formbuilder/summernote/summernote-bs4.min.js')}}"></script>
	@endsection

	@push('additional-js')
	<script>
	    $(document).ready(function() {
	    	$('#{{$config['elOptions']['id']}}').summernote({!! json_encode($newConfigs) !!});
	    });

	    $(document).on("show.bs.modal", '.modal', function (event) {
		    var zIndex = 100000 + (10 * $(".modal:visible").length);
		    $(this).css("z-index", zIndex);
		    setTimeout(function () {
		        $(".modal-backdrop").not(".modal-stack").first().css("z-index", zIndex - 1).addClass("modal-stack");
		    }, 0);
		}).on("hidden.bs.modal", '.modal', function (event) {
		    $(".modal:visible").length && $("body").addClass("modal-open");
		});
	  </script>
	@endpush
@endif