<?php
$headers = MCR()->getExcludedHeaders( 
	'property',
	[
		'Resource', 
		'Address Num', 
		'Address St',
		'Address 2',
		'System Price',
		'Low Price', 
		'Sold Price',
		'Supplement', 
		'View Type',
		'Bedrooms',
		'Bathrooms',
		'Photos Count', 
		'Interior Sqft',
		'Lot size Sqft',
		'Parking Garage', 
		'Parking Total', 
		'Days on Market', 
		'Inclusions'
	] 
);
$limits = ['perPage' => 20, 'pageIdx' => 1];
$where = ['resource' => 'property'];
$results = MCR()->getDataFromLocal( $limits, $where );
$total = MCR()->getTotalNumberByResource( 'property' );
?>

<div class="wrap">
	<h1 class="wp-heading-inline">Properties in McLain</h1>
	<hr class="wp-header-end">

	<div class="tablenav top">
		<div class="tablenav-pages">
			<span class="displaying-num"><?php _e( number_format( $total ) ); ?> items</span>
			<span class="pagination-links">
				<a class="first-page" href="http://localhost/wp-admin/edit.php?post_type=page">
					<span class="screen-reader-text">First page</span>
					<span aria-hidden="true">«</span>
				</a>
				<span class="tablenav-pages-navspan" aria-hidden="true">«</span>

				<a class="prev-page" href="http://localhost/wp-admin/edit.php?post_type=page&amp;paged=5">
					<span class="screen-reader-text">Previous page</span>
					<span aria-hidden="true">‹</span>
				</a>
				<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>

				<span class="paging-input">
					<label for="current-page-selector" class="screen-reader-text">Current Page</label>
					<input class="current-page" id="current-page-selector" name="paged" value="1" size="1" aria-describedby="table-paging" type="text">
					<span class="tablenav-paging-text"> of <span class="total-pages"><?php _e( number_format( ceil( $total / $limits['perPage'] ) ) ); ?></span></span>
				</span>

				<a class="next-page">
					<span class="screen-reader-text">Next page</span>
					<span aria-hidden="true">›</span>
				</a>
				<span class="tablenav-pages-navspan" aria-hidden="true">›</span>

				<a class="last-page" href="http://localhost/wp-admin/edit.php?post_type=page&amp;paged=6">
					<span class="screen-reader-text">Last page</span>
					<span aria-hidden="true">»</span>
				</a>
				<span class="tablenav-pages-navspan" aria-hidden="true">»</span>
			</span>
		</div>

		<br class="clear">
	</div>

	<table class="widefat striped">
		<thead>
			<tr>
				<th><b>No</b></th>
				<?php foreach ( $headers as $className ) : ?>
					<th><b><?php _e( $className ) ?></b></th>
				<?php endforeach; ?>
				<th><b>Action</b></th>
			</tr>
		</thead>

		<tfoot>		
			<tr>
				<th><b>No</b></th>
				<?php foreach ( $headers as $className ) : ?>
					<th><b><?php _e( $className ) ?></b></th>
				<?php endforeach; ?>
				<th><b>Action</b></th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach ( $results as $idx=> $result ) include( 'manage-properties-row-tpl.php' ); ?>
		</tbody>
	</table>
</div>