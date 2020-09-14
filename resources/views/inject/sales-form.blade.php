<script src="{{asset('assets/formbuilder/auto-numeric/autoNumeric.js')}}"></script>
<script type="text/javascript">
	var tableHeaderElement = $('#tbl_multiple_multiplecolumn thead tr th'),
		tableHeaderLength = tableHeaderElement.length;
	tableHeaderElement.each(function(key, value){
        var that = $(this);
		if (key % 3 == 2 && key > 1 && key < (tableHeaderLength - 1)) {
			that.attr('colspan',3).addClass('text-center');
		} else {
			if (key > 1 && key < (tableHeaderLength -1)) {
				that.remove();
			} else {
				that.attr('rowspan',2).addClass('align-middle text-center');
			}
		}
    });

	var tableHeaderElement = $('#tbl_multiple_multiplecolumn thead'),
		appendHeaderElement = '',
		defaultHeaderElement = tableHeaderElement.html(),
		loopCount = ((tableHeaderLength -3) / 3);

	for (var i = loopCount; i > 0; i--) {
		appendHeaderElement += '<th>Qty</th><th>Price</th><th>Total</th>';
	}

	appendHeaderElement = "<tr>"+ appendHeaderElement +"</tr>";
	tableHeaderElement.html(defaultHeaderElement + appendHeaderElement);

	$(document).ready(function() {
		initNumeric();
	});

	(function($) {
        var origAppend = $.fn.append;

        $.fn.append = function () {
            return origAppend.apply(this, arguments).trigger("append");
        };
    })(jQuery);

    $('#tbl_multiple_multiplecolumn tbody').bind("append", function() { 
		initNumeric();
     });

	function initNumeric() {
		$('.money').autoNumeric('init');
		$('.money').css('min-width','130px');
	}

	// $('#multiplecolumn_unit_total_1_2').autoNumeric('set',10000);

</script>
