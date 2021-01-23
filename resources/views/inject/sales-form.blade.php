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
					<input id="totalPayment" name="total_payment" type="text" class="form-control money text-right calc-change totalPayment" readonly>
					<label for="totalPaid" style="margin-top: 15px;">Paid</label>
					<input id="totalPaid" name="total_paid" type="text" class="form-control money text-right calc-change totalPaid">
				</div>
			</div>
			<div class="card-footer">
				<label for="totalPaid">Change</label>
				<input id="totalChange" name="total_change" type="text" class="form-control money text-right totalChange" readonly>
			</div>
	    </div>
	</div>
	<!-- /.card -->
</div>
@endpush

@push('additional-css')
<style type="text/css">
	#submitBtnSales{
		display: none;
	}
</style>
@endpush

@push('additional-js')
<script src="{{asset('assets/formbuilder/auto-numeric/autoNumeric.js')}}"></script>
<script type="text/javascript">

	var detailTemplate = '';

	$('body').on('keyup', '.calc-change', function(e) {
		setChange();
	});

	$('body').on('change', '.calc-change', function(e) {
		setChange();
	});

	$('body #{{$multipleTable}}').on('keyup', '.calc', function(e) {
		var parent    = $(this).closest('tr'),
			price     = parent.find('.price'),
			qty       = parent.find('.qty'),
			subTotal  = parent.find('.sub-total'),
			_subTotal = 0;

		_subTotal = price.autoNumeric('get') * qty.autoNumeric('get');

		subTotal.autoNumeric('set',_subTotal);
		$('#totalPayment').autoNumeric('set', getTotal());
		setChange();
	});

	$('body #{{$multipleTable}}').on('change', '.get-price', function(e) {
		var parent       = $(this).closest('tr'),
			product      = parent.find('.product'),
			price        = parent.find('.price'),
			stock        = parent.find('.stock'),
			qty        = parent.find('.qty'),
			subTotal        = parent.find('.sub-total'),
			sellingPrice = '';

		if($(this).val() !== null){
			price.removeAttr('disabled');
			qty.removeAttr('disabled');
			(async function() {
				var data = await getDataProduct(product.val());
				sellingPrice = data.selling_price;
				productStock = data.stock;
				if ($.isNumeric(sellingPrice))
				price.autoNumeric('set',sellingPrice);
				if ($.isNumeric(productStock))
				stock.autoNumeric('set',productStock);
			})();
		}
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

    async function getDataProduct(productId = '-') {
    	var product = '';

    	if (!isNaN(productId))

    	await $.post('{{route('product.get-data-product')}}',{
		    		product_id: productId,
		    	},function(result){
		    		product = result.data;
		    	});

    	return product;
    }

	function initNumeric() {
		$('.money').autoNumeric('init',{aDec: ',',aSep: '.',mDec:7,aPad:0,vMin:-9999999999,vMax:9999999999});
		$('.money').css('min-width','130px');
		$('.qty').css('min-width','90px');
		$('.qty').css('width','90px');
	}

	function getTotal() {
		var ek = $('.sub-total').map((_,el) => el.value.split('.').join('').replace(',','.')).get();
		// setChange();
		return ek.reduce((a, b) => Number(a) + Number(b), 0);
	}

	function setChange() {
		var change = $('.totalPaid').autoNumeric('get') - $('.totalPayment').autoNumeric('get');
		console.log(change)
		if(change >= 0){
			$('.totalChange').autoNumeric('set', change);
			$('#submitBtnSales').show('fast');
		}else{
			$('#submitBtnSales').hide('fast');
			$('.totalChange').val('')
		}
	}
</script>
@endpush