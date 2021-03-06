<div class="row">

	<div id="formDiv" class="col-md-6">
		<div class="card">
			<div class="card-header text-center">
				<strong>Selection</strong>
			</div>
			<div class="card-body">
				<form id="formTrend" method="get" action="{{ route('trend-moment.go') }}">
					{{
						Form::select2Input('product', $customVariables['product'] ?? old($key), 'product.select2', ['elOptions' => ['required' => 'required']]) 
					}}

					{{
						Form::selectInput('month', $customVariables['month'] ?? old($key), \App\TrendMoment::monthArray(), ['elOptions' => ['required' => 'required']]) 
					}}

					<input id="submitTrend" type="submit" value="Submit" class="btn btn-sm btn-primary float-right mr-3">
				</form>
			</div>
		</div>
	</div>

	<div id="formulaDiv" class="col-md-6">
		<div class="card">
			<div class="card-header text-center">
				<strong>Calculation</strong>
			</div>
			<div class="card-body">
				<table width="100%">
					<tr>
						<th class="text-center">ΣY = n.a + b.ΣX</th>
					</tr>
					<tr>
						<td class="text-center"> <span class="calc-sig-y">ΣY</span> = <span class="calc-">n</span>.a + b.<span class="calc-sig-x">ΣX</span></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="text-center">ΣXY = a.ΣX + b.ΣX²</th>
					</tr>
					<tr>
						<td class="text-center"><span class="calc-sig-x-y">ΣXY</span> = a.<span class="calc-sig-x">ΣX</span> + b. <span class="calc-sig-x-square">ΣX²</span></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="text-center">a = <span class="calc-a">?</span></th>
					</tr>
					<tr>
						<th class="text-center">b = <span class="calc-b">?</span></th>
					</tr>
				</table>
			</div>
		</div>
	</div>

	@if( ! array_key_exists('n', @$customVariables ?? [])  && \Request::is('trend-moment/go*'))
	<div id="errorDiv" class="col-md-12">
		<div class="card">
			<div class="card-header text-center">
				<strong>Something went wrong</strong>
			</div>
			<div class="card-body text-center">
				{{ @$customVariables['message'] }}
			</div>
		</div>
	</div>
	@endif

	@if( array_key_exists('n', @$customVariables ?? []) )
	<div id="predictionDiv" class="col-md-6">
		<div class="card">
			<div class="card-header text-center">
				<strong>Prediction</strong>
			</div>
			<div class="card-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Period</th>
							<th>Prediction</th>
							<th>By Trend Moment</th>
						</tr>
					</thead>
					<tbody>
						@php
							$n = @$customVariables['n'];
							foreach (@$customVariables['next'] as $key => $value) {
								echo "
									<tr>
										<td class='number'>". $value['idx'] ."</td>
										<td class='number'>". $value['prediction'] ."</td>
										<td class='number'>". $value['trend'] ."</td>
									</tr>
								";
								$n++;
							}
						@endphp
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div id="dataDiv" class="col-md-6">
		<div class="card">
			<div class="card-header text-center">
				<strong>Sales Data</strong>
			</div>
			<div class="card-body">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>Month</th>
							<th>Sales (Y)</th>
							<th>Time (X)</th>
							<th>(XY)</th>
							<th>(X²)</th>
						</tr>
					</thead>
					<tbody>
						@php
							$i = 0;
							foreach (@$customVariables['sales'] as $key => $value) {
								echo "
									<tr>
										<td>". $value['month'] ."</td>
										<td class='number'>". $value['y'] ."</td>
										<td class='number'>". $value['x'] ."</td>
										<td class='number'>". $value['xy'] ."</td>
										<td class='number'>". $value['xx'] ."</td>
									</tr>
								";
								$i++;
							}
							echo "
								<tr>
									<th>Total</th>
									<th class='number'>". $customVariables['sigY'] ."</th>
									<th class='number'>". $customVariables['sigX'] ."</th>
									<th class='number'>". $customVariables['sigXY'] ."</th>
									<th class='number'>". $customVariables['sigXSquare'] ."</th>
								</tr>
								<tr>
									<th>Average</th>
									<th>". $customVariables['avg'] ."</th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							";
						@endphp
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif


</div>

@push('additional-js')
<script type="text/javascript">
	$('#formTrend').submit(function(event) {
        event.preventDefault();
        $('#submitTrend').prop('disabled',true);

		var url      = $('#formTrend').attr('action');

		window.location.replace("{{ route('trend-moment.go') }}/" + $('#select2-product').val() + '/' + $('#month').val());

    });
</script>
@endpush