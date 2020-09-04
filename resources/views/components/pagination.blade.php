@php
	$targetElement = @$targetElement ?? '';
	$preventDefault = @$preventDefault ?? false;
@endphp

<span id="pagination-section" style="display: none;">
	<nav aria-label="Page navigation" class="pagination-section">
	   	<ul class="pagination custom-pagination-2 justify-content-center">
	   	</ul>
	</nav>
</span>


@push('additional-js')
<script type="text/javascript">
	var currentPagePagination = 1;
	var currentPathPagination = '';

	@if($preventDefault)
	$(document.body).delegate('.pagination-section .pagination a','click', function(event) {
        event.preventDefault();
        currentPathPagination = $(this).attr('href');
        currentPagePagination = $(this).data('page');
        loadData(currentPathPagination);
	});
	@endif


	function setupPagination(totalPage, currentPage, pathUrl, targetElement = '{{ $targetElement }}', paginationLength = 10) {
		$('#pagination-section .pagination').html( setPagination(totalPage, currentPage, pathUrl, paginationLength) );
		$(targetElement).html($('#pagination-section').html());
	}

	function setPagination(totalPage, currentPage, pathUrl = '', paginationLength) {
		var el = '', len = 3;
		pathUrl = pathUrl + '?paginate=' + paginationLength + '&page=';
		// pathUrl = '#';

		el += 
			// '<li class="page-item '+ (currentPage == 1 ? "disabled" : "") +'">'+
	      	// 	'<a class="page-link" data-page="'+i+'" href="'+ (pathUrl) +'1">First</a>'+
	      	// '</li>'+
	      	'<li class="page-item '+ (currentPage == 1 ? "disabled" : "") +'">'+
	      		'<a class="page-link" data-page="'+ (currentPage == 1 ? "1" : currentPage - 1) +'" href="'+ (pathUrl) + (currentPage == 1 ? "1" : currentPage - 1) +'">«</a>'+
	      	'</li>';

	      	if (currentPage > (len + 2) ) {
	      		el += 
			      	'<li class="page-item">'+
			      		'<a class="page-link" data-page="1" href="'+ (pathUrl) +'1">1</a>'+
			      	'</li>'+
	      			'<li class="page-item disabled">'+
			      		'<a class="page-link" data-page="#" href="#">..</a>'+
			      	'</li>';
	      	} else if (currentPage == (len + 2) ) {
	      		el += '<li class="page-item">'+
			      		'<a class="page-link" data-page="1" href="'+ (pathUrl) +'1">1</a>'+
			      	'</li>';
	      	}


	      	for (var i = ( (currentPage - len) < 1 ? 1 : (currentPage - len) ); i < currentPage; i++) {
		      	el += '<li class="page-item">'+
			      		'<a class="page-link" data-page="'+i+'" href="'+ (pathUrl) + i +'">'+ i +'</a>'+
			      	'</li>';
	      	}
		    el += '<li class="page-item disabled">'+
		      		'<a class="page-link bg-white text-danger" data-page="'+ currentPage +'" href="'+ (pathUrl) + currentPage +'">'+ currentPage +'</a>'+
		      	'</li>';
		    for (var i = (currentPage + 1); i < ( (currentPage + 1 + len) < totalPage ? (currentPage + 1 + len) : totalPage ); i++) {
		      	el += '<li class="page-item">'+
			      		'<a class="page-link" data-page="'+i+'" href="'+ (pathUrl) + i +'">'+ i +'</a>'+
			      	'</li>';
	      	}

	      	if ( (currentPage + len) < (totalPage - 1) ) {
	      		el += '<li class="page-item disabled">'+
			      		'<a class="page-link" data-page="#" href="#">..</a>'+
			      	'</li>'+
			      	'<li class="page-item">'+
			      		'<a class="page-link" data-page="'+ totalPage +'" href="'+ (pathUrl) + totalPage +'">'+ totalPage +'</a>'+
			      	'</li>';
	      	} else if (currentPage != totalPage) {
	      		el += '<li class="page-item">'+
			      		'<a class="page-link" data-page="'+ totalPage +'" href="'+ (pathUrl) + totalPage +'">'+ totalPage +'</a>'+
			      	'</li>';
	      	}

	      	if (totalPage > 15) {
	      		el += 
			    	'<li class="page-item" style="background-color: #e9ecef;">'+
				    	'<input type="number" name="pagination" placeholder="   ??" min="1" class="form-control" style="margin: 5px 2px 0 1px; width: 38px; height: 24px; border: 1px solid #b7b9bb">'+
				    '</li>';
	      	}


	    el += 
	    	'<li class="page-item '+ (currentPage == totalPage ? "disabled" : "") +'">'+
	      		'<a class="page-link" data-page="'+ (currentPage == totalPage ? totalPage : currentPage + 1) +'" href="'+ (pathUrl) + (currentPage == totalPage ? totalPage : currentPage + 1) +'">»</a>'+
	      	'</li>'
	      	// +
	      	// '<li class="page-item '+ (currentPage == totalPage ? "disabled" : "") +'">'+
	      	// 	'<a class="page-link" data-page="'+i+'" href="'+ (pathUrl) + totalPage +'">Last</a>'+
	      	// '</li>'
	      	;

	    return el;
	}
</script>
@endpush