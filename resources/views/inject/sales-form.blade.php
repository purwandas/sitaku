@php
$multipleTable = 'tbl_multiple_detail';
@endphp

@push('inject-view')
<div id="detailTemplate">
	<div class="col-sm-4 offset-sm-2" style="margin-top: 10px;">
	    <div class="card card-outline card-secondary">
			<div class="card-header">
				<h3 class="card-title">Total</h3>
			</div>
			<div class="card-body">
				<div class="form-group row">
					<label for="totalPayment">Payment</label>
					<input id="totalPayment" type="text" class="form-control money text-right" onchange="setChange()" readonly>
					<label for="totalPaid" style="margin-top: 15px;">Paid</label>
					<input id="totalPaid" type="text" class="form-control money text-right" onchange="setChange()" onkeypress="setChange()">
				</div>
			</div>
			<div class="card-footer">
				<label for="totalPaid">Change</label>
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
		var parent    = $(this).closest('tr'),
			price     = parent.find('.price'),
			qty       = parent.find('.qty'),
			subTotal  = parent.find('.sub-total'),
			_subTotal = 0;

		_subTotal = price.autoNumeric('get') * qty.autoNumeric('get');

		subTotal.autoNumeric('set',_subTotal);
		$('#totalPayment').autoNumeric('set', getTotal() );
	});

	$('body #{{$multipleTable}}').on('change', '.get-price', function(e) {
		var parent       = $(this).closest('tr'),
			product      = parent.find('.product'),
			unit         = parent.find('.unit'),
			price        = parent.find('.price'),
			sellingPrice = '';

		(async function() {
			sellingPrice = await getPrice(product.val(), unit.val());
			if ($.isNumeric(sellingPrice))
			price.val(sellingPrice);
		})();
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

    async function getPrice(productId = '-', unitId = '-') {
    	var price = '';

    	if (!isNaN(productId) && !isNaN(unitId))
    	await $.ajax({
            type: 'POST',
            url: "{{route('product-unit.get-price')}}/" + (productId??'-') + "/" + (unitId??'-'),
            success: function (result) {
				var data = result.data;
				price    = data.selling_price
            },
            error: function(xhr, textStatus, errorThrown){
                swal({
                	title: "Gagal melakukan request",
                	text: xhr.responseJSON.msg,
                	type: "error"
                });
            }
        });
    	return price;
    }

	function initNumeric() {
		$('.money').autoNumeric('init',{mDec:7,aPad:0});
		$('.money').css('min-width','130px');
		$('.qty').css('min-width','90px');
		$('.qty').css('width','90px');
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