@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-body">
		{{-- <h4 class="mt-0 header-title">Create User</h4> --}}
		{{-- @include('components.datatable_builder',['name' => 'user_table']) --}}
		@if(isset($setupFilterBuilder))
			@include('components.filter_builder',$setupFilterBuilder)
		@endif
		@if(isset($setupFormBuilder))
			@include('components.form_builder',$setupFormBuilder)
		@endif
		@if(isset($setupDatatableBuilder))
			@include('components.datatable_builder',$setupDatatableBuilder)
		@endif
	</div>
</div>
@endsection

@push('modal-element')
<div class="modal fade" id="modalReject" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Reject Reason</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <input type="hidden" id="modalStatusInput">
            </div>
		            <div class="modal-body">
		            	<div class="col-md-12">
						{!! Form::open(['id' => 'reject_form']) !!}
							{{Form::textareaInput('reason',null,['useLabel' => false])}}
					    {!! Form::close() !!}
		            	</div> 
		            </div>
		            <div class="modal-footer">
		            	<div class="btn-group">
			            <button type="button" class="btn btn-danger" id="rejectBtn">Reject</button>
			            <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
		            </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('additional-js')
<script type="text/javascript">
	function approveData(url){
		swal({
            title: 'Approve this data?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, approve it!',
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
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function (data) {
		                swal({
		                    title: 'Success!',
		                    text: data.msg,
		                    type: data.type,
		                });
		                {{$setupDatatableBuilder['name'] ?? @$model::toKey()['snake']}}_reloadTable();
                    },
                    error: function(xhr, textStatus, errorThrown){
                        swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal('Cancelled', 'Your data is safe :)', 'error');
            }
        });
	}

	function rejectData(url){
		console.log(url);
		$('#reject_form').removeAttr('action');
		$('#reject_form').attr('action',url);
		$('#reason').val('');
        $('#modalReject .modal-title').html('Reject Reason');
        $('#rejectBtn').html('Reject');
        $('#modalStatusInput').val('Reject');
		$("#modalReject").modal('show');
	}

	$('#rejectBtn').click(function(){
        var status = $('#modalStatusInput').val();
        var label = '';
        var text = '';
        if (status == 'Unblock') {
            label = 'unblock';
            text = '';
        } else {
            label = 'reject';
            text = 'You will not be able to recover this data!';
        }

		swal({
            title: 'Are you sure?',
            text: text,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, '+label+' it!',
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
                $.ajax({
                    url: $('#reject_form').attr('action'),
                    type: 'POST',
                    data: $('#reject_form').serialize(),
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
						$("#modalReject").modal('hide');
		                {{$setupDatatableBuilder['name'] ?? @$model::toKey()['snake']}}_reloadTable();
                    },
                    error: function(xhr, textStatus, errorThrown){
                        swal("Gagal melakukan request", "Silahkan hubungi admin", "error");
                    }
                });
            } else if (result.dismiss === 'cancel') {
                swal('Cancelled', 'Your data is safe :)', 'error');
            }
        });
	});

    function unblockData(url){
        $('#reject_form').removeAttr('action');
        $('#reject_form').attr('action',url);
        $('#reason').val('');
        $('#modalReject .modal-title').html('Unblock Reason');
        $('#rejectBtn').html('Unblock');
        $('#modalStatusInput').val('Unblock');
        $("#modalReject").modal('show');
    }
</script>
@endpush