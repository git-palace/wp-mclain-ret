<?php
$headers = SI()->getExcludedHeaders( 
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
global $limits;
$where = ['resource' => 'property'];
$results = SI()->getDataFromLocalDB( $limits, $where );
$total_count = SI()->getTotalCountByResource( 'property' );
$totalPage = ceil( $total_count / $limits['perPage'] );
?>

<div class="wrap sandicor-properties">
	<h1 class="wp-heading-inline">All Properties</h1>
	<hr class="wp-header-end">

	<div class="tablenav top">
		<div class="alignleft actions">
			<select class="per-page" type="<?php _e( $where['resource']); ?>">
				<?php
					global $perPages;
					foreach ( $perPages as $perPage ) _e( sprintf( "<option value='%s' %s>%s</option>", $perPage, $perPage == $limits['perPage'] ? 'selected' : '', $perPage ) );
				?>
			</select>

			<label style="float: left; line-height: 28px; height: 28px;">itmes per page</label>
		</div>

		<div class="tablenav-pages">
			<span class="displaying-num"><?php _e( number_format( $total_count ) ); ?> items</span>
			<span class="pagination-links">
				<?php if ( $limits['pageIdx'] > 1 && $totalPage > 2 ) : ?>
					<a class="first-page" href="/wp-admin/admin.php?page=sandicor<?php if( $limits['perPage'] != 10 ) _e( '&perPage=' . $limits['perPage'] ); ?>&pageIdx=1">
						<span class="screen-reader-text">First page</span>
						<span aria-hidden="true">«</span>
					</a>
				<?php else: ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">«</span>
				<?php endif; ?>

				<?php if ( $limits['pageIdx'] > 1 && $totalPage > 1 ) : ?>
					<a class="prev-page" href="/wp-admin/admin.php?page=sandicor<?php if( $limits['perPage'] != 10 ) _e( '&perPage=' . $limits['perPage'] ); ?>&pageIdx=<?php _e( $limits['pageIdx'] - 1 ) ?>">
						<span class="screen-reader-text">Previous page</span>
						<span aria-hidden="true">‹</span>
					</a>
				<?php else: ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
				<?php endif; ?>

				<span class="paging-input">
					<label for="current-page-selector" class="screen-reader-text">Current Page</label>
					<input class="current-page" id="current-page-selector" name="pageIdx" value="<?php _e( $limits['pageIdx'] ); ?>" size="1" aria-describedby="table-paging" type="text">
					<span class="tablenav-paging-text"> of <span class="total-pages"><?php _e( number_format( $totalPage ) ); ?></span></span>
				</span>

				<?php if ( $limits['pageIdx'] < $totalPage && $totalPage > 1 ) : ?>
					<a class="next-page" href="/wp-admin/admin.php?page=sandicor<?php if( $limits['perPage'] != 10 ) _e( '&perPage=' . $limits['perPage'] ); ?>&pageIdx=<?php _e( $limits['pageIdx'] + 1 ) ?>">
						<span class="screen-reader-text">Next page</span>
						<span aria-hidden="true">›</span>
					</a>
				<?php else: ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">›</span>
				<?php endif; ?>

				<?php if ( $limits['pageIdx'] < $totalPage && $totalPage > 2 ) : ?>
					<a class="last-page" href="/wp-admin/admin.php?page=sandicor<?php if( $limits['perPage'] != 10 ) _e( '&perPage=' . $limits['perPage'] ); ?>&pageIdx=<?php _e( $totalPage ) ?>">
						<span class="screen-reader-text">Last page</span>
						<span aria-hidden="true">»</span>
					</a>
				<?php else: ?>
					<span class="tablenav-pages-navspan" aria-hidden="true">»</span>
				<?php endif; ?>
			</span>
		</div>

		<br class="clear">
	</div>

	<table class="widefat striped">
		<thead>
			<tr>
				<th align="center"><b>No</b></th>
				<?php foreach ( $headers as $className ) : ?>
					<th><b><?php _e( $className ) ?></b></th>
				<?php endforeach; ?>
				<th align="center"><b>Actions</b></th>
			</tr>
		</thead>

		<tfoot>		
			<tr>
				<th align="center"><b>No</b></th>
				<?php foreach ( $headers as $className ) : ?>
					<th><b><?php _e( $className ) ?></b></th>
				<?php endforeach; ?>
				<th align="center"><b>Actions</b></th>
			</tr>
		</tfoot>

		<tbody>
			<?php foreach ( $results as $idx=> $result ) include( 'properties-row-tpl.php' ); ?>
		</tbody>
	</table>
</div>