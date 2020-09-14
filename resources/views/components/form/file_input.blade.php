@php
unset($attributes['type']);

if (!is_array($attributes)) $attributes = [];
$config = FormBuilderHelper::setupDefaultConfig($name, $attributes, $errors);
$config['pluginOptions']['theme'] = "fas";
$config['inputContainerClass'] = ($config['inputContainerClass'] == 'col-md-10' && !$config['useLabel'])  ? 'col-md-12' : $config['inputContainerClass'];
@endphp

{{-- <div class="{{ $config['divContainerClass'] }} {{ !$errors->has($name) ?: 'has-danger' }}"> --}}
<div class="{{ @$config['containerClass'] ?? 'form-group row'}}">
    @if ($config['useLabel'])
    {{-- <div class="row"> --}}
        <div class="{{ $config['labelContainerClass'] }}">
            <label class="col-form-label">
                {!! $config['labelText'] !!}
            </label>
        </div>
    @endif
        <div class="{{ $config['inputContainerClass'] }}">
            @if (!empty($config['addons']))
            <div class="input-group m-input-group">
                @if ($config['addons']['position'] === 'left')
                <span class="{{ $config['addons']['class'] }} addon-left-side">{{ $config['addons']['text'] }}</span>
                @endif
            @endif

            <input type="file" name="{{ $name }}" {!! $config['htmlOptions'] !!}>

            @if (!empty($config['addons']))
                @if ($config['addons']['position'] === 'right')
                <span class="{{ $config['addons']['class'] }} addon-right-side">{{ $config['addons']['text'] }}</span>
                @endif
            </div>
            @endif

            <div class="error-container">
                @if($errors->has($name))
                <div class="form-control-feedback">{{ $errors->first($name) }}</div>
                @endif
            </div>

            {!! @$config['info'] !!}

    {{-- @if ($config['useLabel']) --}}
        </div>
    {{-- </div> --}}
    {{-- @endif --}}
</div>

@section('file-css')
<link href="{{asset('assets/formbuilder/krajee/fileinput.min.css')}}" media="all" rel="stylesheet" type="text/css" />
<!-- if using RTL (Right-To-Left) orientation, load the RTL CSS file after fileinput.css by uncommenting below -->
<!-- link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput-rtl.min.css" media="all" rel="stylesheet" type="text/css" /-->
<!-- the font awesome icon library if using with `fas` theme (or Bootstrap 4.x). Note that default icons used in the plugin are glyphicons that are bundled only with Bootstrap 3.x. -->
<link rel="stylesheet" href="{{asset('assets/formbuilder/krajee/all.css')}}" crossorigin="anonymous">
<style type="text/css">
    .file-preview-thumbnails{
        display: table;
    }
</style>
@endsection

@section('file-js')
<script src="{{asset('assets/formbuilder/krajee/piexif.min.js')}}" type="text/javascript"></script>
<!-- sortable.min.js is only needed if you wish to sort / rearrange files in initial preview. 
    This must be loaded before fileinput.min.js -->
<script src="{{asset('assets/formbuilder/krajee/sortable.min.js')}}" type="text/javascript"></script>
<!-- purify.min.js is only needed if you wish to purify HTML content in your preview for 
    HTML files. This must be loaded before fileinput.min.js -->
<script src="{{asset('assets/formbuilder/krajee/purify.min.js')}}" type="text/javascript"></script>
<!-- popper.min.js below is needed if you use bootstrap 4.x (for popover and tooltips). You can also use the bootstrap js 
   3.3.x versions without popper.min.js. -->
<script src="{{asset('assets/formbuilder/krajee/popper.min.js')}}"></script>
<!-- bootstrap.min.js below is needed if you wish to zoom and preview file content in a detail modal
    dialog. bootstrap 4.x is supported. You can also use the bootstrap js 3.3.x versions. -->
<script src="{{asset('assets/formbuilder/krajee/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
<!-- the main fileinput plugin file -->
<script src="{{asset('assets/formbuilder/krajee/fileinput.min.js')}}"></script>
<!-- following theme script is needed to use the Font Awesome 5.x theme (`fas`) -->
<script src="{{asset('assets/formbuilder/krajee/theme.min.js')}}"></script>
@endsection

@push('function-js')
<script type="text/javascript">
    $(document).ready(function() {
        $("#{{ $config['elOptions']['id'] }}").fileinput({!! json_encode($config['pluginOptions']) !!});
    });

    var pluginOptions_{{ $config['elOptions']['id'] }} = {!! json_encode($config['pluginOptions']) !!};
    var el_{{ $config['elOptions']['id'] }} = $('#{{ $config['elOptions']['id'] }}').fileinput(pluginOptions_{{ $config['elOptions']['id'] }});
    
    function updateInitialPreview_{{ $config['elOptions']['id'] }}(url){
        if (Array.isArray(url)) {
            var preview = [], caption;
            url.forEach(function(item){
                var exp = item.split('/');
                var caption = exp[exp.length-1];
                var tmpCaption = {
                    "caption":caption,
                    "downloadUrl":item,
                    "showRemove":false,
                    "width":"120px",
                    "key":1
                };
                preview.push(tmpCaption);
            });
        } else {
            var exp = url.split('/');
            var caption = exp[exp.length-1];
            var preview = [{
                "caption":caption,
                "downloadUrl":url,
                "showRemove":false,
                "width":"120px",
                "key":1
            }];
            url = [url];
        }

        var newPluginOptions_{{ $config['elOptions']['id'] }} = {
            "allowedFileExtensions":{!! json_encode(@$config['pluginOptions']['allowedFileExtensions']) !!},
            "theme":"fas",
            "showUpload":false,
            "initialPreview":url,
            "initialPreviewAsData":true,
            "showRemove":false,
            "initialPreviewConfig":preview,
            "overwriteInitial":true
        };
        if (el_{{ $config['elOptions']['id'] }}.data('fileinput')) {
            el_{{ $config['elOptions']['id'] }}.fileinput('destroy');
        }
        $("#{{ $config['elOptions']['id'] }}").fileinput(newPluginOptions_{{ $config['elOptions']['id'] }});
    }
    
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