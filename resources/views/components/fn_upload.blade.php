@php
$title = ucwords(str_replace('_',' ',$name));
$id    = str_replace(' ','',$title);
$sample_data = @$sample_data ?? [];

if (count(@$sample_data) > 0) {
    $tableHeader  = "";
    $tableContent = "";
    foreach(array_keys($sample_data) as $key => $value){
        $child = $sample_data[$value];
        $tableHeader .= "<th>".strtoupper(str_replace('_',' ',$value))."</th>";
    }
    foreach(array_keys($child) as $key => $value){
        $tableContent .= '<tr>';
        foreach(array_keys($sample_data) as $key2 => $value2){
            $tableContent .= '<td>'.$sample_data[$value2][$key].'</td>';
        }
        $tableContent .= '</tr>';
    }
}
@endphp

<button id="upload-button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#upload-modal" onclick="$('.fileinput-remove-button').click();">
  {!! getSvgIcon('fa-upload','mt-m-2') !!}
  Import
</button>

@push('modal-element')
<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Upload Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <a id="linkGenerator" target="_blank"></a>
            <form id="uploadForm{{ $id }}" method="post" enctype="multipart/form-data" action="{{ $form_url }}">
                {{ csrf_field() }}
                <div class="modal-body">
                    <a href="{{ $template_url }}" target="_blank" class="btn btn-sm btn-info" style="margin-bottom: 10px;">
                        {!! getSvgIcon('fa-download','mt-m-2') !!}
                        Download Format
                    </a>
                    {{-- <button class="btn btn-sm btn-info" onclick="downloadTemplate('{{$id}}TemplateButton','{{ $template_url }}')" id="{{$id}}TemplateButton" style="margin-bottom: 10px;">
                        {!! getSvgIcon('fa-download','mt-m-2') !!}
                        {{ 'Download Format' }}
                    </button> --}}
                    {{ Form::fileInput('file',null,['useLabel' => false,'inputContainerClass' => 'col-md-12','pluginOptions' => [
                        'allowedFileExtensions' => ["xls","xlsx",'csv','odt'],
                    ]]) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@prepend('additional-js')
<script type="text/javascript">
    
    $('#uploadForm{{ $id }}').submit(function(event) {
        event.preventDefault();

        var form     = '#uploadForm{{ $id }}';
        var formData = new FormData($(form)[0]);

        $.ajax({
            url   : $(form).attr('action'),
            type  : $(form).attr('method'),
            data  : formData,
            dataType: 'json',
            processData: false,
            contentType: false,
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
                $('#upload-modal').modal('toggle');
                setTimeout(function() {
                    {{$name}}_reloadTable();
                }, 4000);
            },
            error: function(xhr, textStatus, errorThrown){
                swal({
                    title: "Gagal melakukan request",
                    text: "Silahkan hubungi admin",
                    type: "error"
                });
            }
        });
    });

    // function downloadTemplate(elementId, url) {
    //     var element = $("#"+elementId);
    //     var icon = $("#"+elementId+"Icon");
    //     if (element.attr('disabled') != 'disabled') {
    //         var thisClass = icon.attr('class');

    //         var filters = {};
    //         var filterForm = $('#filter_form{{$name}}').serializeArray();
    //         $.each(filterForm, function(key, val){
    //             if (val.name != "_token" && val.value != "") {
    //                 filters[val.name] = val.value;
    //             }
    //         });

    //         $.ajax({
    //             type: 'GET',
    //             url: url,
    //             data: filters,
    //             beforeSend: function()
    //             {
    //                 element.attr('disabled', 'disabled');
    //                 icon.attr('class', 'fa fa-spinner fa-spin');
    //             },
    //             success: function (data) {
    //                 element.removeAttr('disabled');
    //                 icon.attr('class', thisClass);
    //                 var a = $('#linkGenerator');
    //                 a.attr('href',data.url);
    //                 document.getElementById('linkGenerator').click();
    //                 a.attr('href','#');
    //             },
    //             error: function(xhr, textStatus, errorThrown){
    //                 element.removeAttr('disabled');
    //                 icon.attr('class', thisClass);
    //                 swal({
    //                     title: "Gagal melakukan request",
    //                     text: "Silahkan hubungi admin",
    //                     type: "error"
    //                 });
    //             }
    //         });
    //     }
    // }

</script>
@endprepend