@yield('modal-area')
@stack('modal-element')

@yield('summernote-css')
@yield('select2-plugin-css')
@yield('datepicker-css')
@yield('daterangepicker-css')
@yield('switch-css')
@yield('datatable-css')
@yield('additional-css')
@stack('additional-css')

@yield('summernote-js')        
@yield('vendor-js')
@yield('select2-plugin-js')
@yield('datepicker-js')
@yield('daterangepicker-js')
@yield('switch-js')
@yield('datatable-js')
@yield('additional-js')
@stack('additional-js')

@yield('function-js')
@stack('function-js')

<!-- Sweet-Alert  -->
<link href="{{asset('assets/formbuilder/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
<script src="{{asset('assets/formbuilder/sweet-alert2/sweetalert2.min.js')}}"></script>

<style type="text/css">
	.mb-m-2 {
		margin-bottom: -2px !important;
	}
	.mt-m-2 {
		margin-top: -2px !important;
	}
</style>
<script>
	$.ajaxSetup({
        headers: {
            'Accept': 'application/json',
            'Authorization': "Bearer {{ session('token') }}",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

	$(document).ajaxError(function( event, jqxhr, settings, thrownError ) {
        if(jqxhr.status == 401) {
        	swal({
	            title: 'Unauthenticated!',
	            text: 'Sesi anda sudah habis. Silahkan login kembali!',
	            type: 'warning',
	            showCancelButton: false,
	            confirmButtonText: 'Login Ulang',
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
                    $('#logout-form').submit();
	            }
	        });
            
        }
    });
</script>
