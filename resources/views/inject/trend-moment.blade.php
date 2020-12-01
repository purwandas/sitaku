<div class="row">
	<div class="col-md-6">
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
							foreach ($customVariables['sales'] as $key => $value) {
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

	<div class="col-md-6">
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
						<td class="text-center">ΣY = n.a + b.ΣX</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="text-center">ΣXY = a.ΣX + b.ΣX²</th>
					</tr>
					<tr>
						<td class="text-center">ΣXY = a.ΣX + b.ΣX²</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<th class="text-center">a = ?</th>
					</tr>
					<tr>
						<th class="text-center">b = ?</th>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="col-md-6">
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
							foreach ($customVariables['next'] as $key => $value) {
								echo "
									<tr>
										<td class='number'>". $value['idx'] ."</td>
										<td class='number'>". $value['prediction'] ."</td>
										<td class='number'>". $value['trend'] ."</td>
									</tr>
								";
								$i++;
							}
						@endphp
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>