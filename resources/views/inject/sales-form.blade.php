@php
$multipleTable = 'tbl_multiple_multiplecolumn2';
@endphp

@push('inject-view')
<div id="detailTemplate">
	<div class="col-sm-4 offset-sm-2" style="margin-top: 10px;">
	    <div class="card card-outline card-secondary">
			<div class="card-header">
				<h3 class="card-title">Info Card Example</h3>
			</div>
			<div class="card-body">
				<div class="form-group row">
					<label for="totalPayment">Total Payment</label>
					<input id="totalPayment" type="text" class="form-control money text-right" onchange="setChange()" readonly>
					<label for="totalPaid">Total Paid</label>
					<input id="totalPaid" type="text" class="form-control money text-right" onchange="setChange()" onkeypress="setChange()">
				</div>
			</div>
			<div class="card-footer">
				<label for="totalPaid">Total Change</label>
				<input id="totalChange" type="text" class="form-control money text-right" readonly>
			</div>
	    </div>
	</div>
	<!-- /.card -->
</div>
@endpush

@include('inject/table-total')

@push('additional-js')
<script src="{{asset('assets/formbuilder/auto-numeric/autoNumeric.js')}}"></script>
<script type="text/javascript">

	var detailTemplate = '';

	$('body #{{$multipleTable}}').on('change', '.calc', function(e) {
		var parent = $(this).closest('tr'),
			price = parent.find('.price'),
			qty = parent.find('.qty'),
			subTotal = parent.find('.sub-total'),
			_subTotal = 0;

		_subTotal = price.autoNumeric('get') * qty.autoNumeric('get');

		subTotal.autoNumeric('set',_subTotal);
		$('#totalPayment').autoNumeric('set', getTotal() );
	});

	$(document).ready(function() {
		setTimeout(function() {
			$('#menuToggle').click()
			initNumeric();
		}, 10);


		detailTemplate = $('#detailTemplate').html();
		$('#detailTemplate').html('');

	    $('#{{$multipleTable}}').parent().parent().append(detailTemplate);
	});

	(function($) {
        var origAppend = $.fn.append;

        $.fn.append = function () {
            return origAppend.apply(this, arguments).trigger("append");
        };
    })(jQuery);

    $('#{{$multipleTable}} tbody').bind("append", function() { 
		initNumeric();
    });

	function initNumeric() {
		$('.money').autoNumeric('init',{mDec:7,aPad:0});
		$('.money').css('min-width','130px');
	}

	function getTotal() {
		var ek = $('.sub-total').map((_,el) => el.value.split(',').join('')).get();
		return ek.reduce((a, b) => Number(a) + Number(b), 0);
	}

	function setChange() {
		var change = $('#totalPaid').autoNumeric('get') - $('#totalPayment').autoNumeric('get');
		if (change > 0)
		$('#totalChange').autoNumeric('set', change  );
	}
</script>
@endpush