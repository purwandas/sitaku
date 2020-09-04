@php
//################################
// name -> purpose of main view, for example menu name, use underscore, example: product_focus
// model -> model name, example: App\ProductFocus
// buttonId -> id of button that click to show the modal, example: upload-status
//################################
$route  = str_replace('_','-',$name);
$title  = ucwords(str_replace('_',' ',$name));
$module = @$module ?? explode('\\',$model);
$module = is_array($module) ? end($module) : $module;
$module = ucwords(str_replace('-', ' ', Str::kebab($module)));
@endphp

@push('modal-element')
<div class="modal fade" id="jobTraceModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Job Status For {{ @$title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="col-lg-12"> 
                <div style="overflow-x: auto; margin: 5px;">
                    <table class="table table-striped table-vcenter js-dataTable-full table-hover table-bordered" id="traceTable" style="white-space: nowrap;width: 100% !important">
                        <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>Log</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <div class="btn-group">
                    {{-- <button type="button" class="btn btn-danger" data-dismiss=modal>close</button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('additional-css')
<style type="text/css">
    @media (min-width: 768px) {
      .modal-xl {
        width: 90%;
       max-width:1200px;
      }
    }
</style>
@endpush

@push('additional-js')
<script type="text/javascript">
    $('#{{$buttonId}}').attr('data-toggle','modal');
    $('#{{$buttonId}}').attr('data-target','#jobTraceModal');

    function showJobStatus() {
        var tableId = "#traceTable";
        if($.fn.dataTable.isDataTable(tableId)){
            $(tableId).DataTable().clear();
            $(tableId).DataTable().destroy();
        }
        $(tableId).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{ route('job-trace.data', ['module' => $module]) }}",
                type: 'POST',
            },
            "rowId": "id",
            "columns": [
                { data: 'id',           name: 'id', visible: false},
                { data: 'title',        name: 'title'},
                { data: 'status',       name: 'status', searchable: false, sortable: false},
                { data: 'action',       name: 'action', searchable: false, sortable: false},
                { data: 'user_name',    name: 'user_name'},
                { data: 'log',          name: 'log', searchable: false, sortable: false},
            ],
            "columnDefs": [
                {"className": "text-center", "targets": [0, 1, 3, 5]}
            ],
            "order": [ [0, 'desc'] ],
            // "searching": false,
            "pageLength": 5,
        });
    }

    $('#traceTable').on('click', 'tr td button.errorLog', function () {
        var log = $(this).val();            
            
        $("#exp-title").html("Upload {{$title}}");
        $("#exp-context").html(log);

        $("#explanation-modal").modal('show');
    });

    $('#traceTable').on('click', 'tr td button.deleteButton', function () {
        var id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover data!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it",
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function (isConfirm) {
                if (isConfirm) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    $.ajax({
                        type: "POST",
                        url:  "{{ asset('job-trace/delete') }}/" + id,
                        success: function (data) {
                            $("#traceTable #"+id).remove();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                    swal("Deleted!", "Data has been deleted.", "success");
                } else {
                    swal("Cancelled", "Data is safe ", "success");
                }
            });
    });
</script>
@endpush