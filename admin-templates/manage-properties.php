<?php
	$headers = MCR()->getVisibleHeaders( ['Type', 'Supplement', 'Bedrooms', 'Bathrooms', 'Parking Garage', 'Parking Total', 'Days on Market', 'Inclusions'] );
	$limits = ['perPage' => 20, 'pageIdx' => 1];
	$where = ['type' => 'property'];
	$results = MCR()->getDataFromLocal( $limits, $where );
?>

<div class="wrap">
	<h1 class="wp-heading-inline">Properties in McLain</h1>
	<hr class="wp-header-end">

	<table class="widefat striped">
		<thead>
			<tr>
				<th><b>No</b></th>
				<?php
					foreach ( $headers as $className ) {
						echo "<th><b>" . $className . "</b></th>";
					}
				?>
			</tr>
		</thead>

		<tfoot>		
			<tr>
				<th></th>
				<?php
					foreach ( $headers as $className ) {
						echo "<th>" . $className . "</th>";
					}
				?>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach ( $results as $idx=> $result ): ?>
				<tr>
					<td align="center"><?php _e( 1 + $idx + ($limits['pageIdx'] - 1) * $limits['perPage'] ) ?></td>
					<?php foreach ( $headers as $key => $value): ?>
						<td><?php _e( $result->$key ); ?></td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</div>