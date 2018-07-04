<?php if ( !MCR()->login() ) : ?>

	<script type="text/javascript">
		window.location.href = "/wp-admin/admin.php?page=mclain-rets-config";
	</script>

<?php else: ?>

<?php

	/*$timestamp = wp_next_scheduled( 'MCRETSCronJob' );
	$date = new DateTime();
	$date->setTimestamp( $timestamp );
	echo $date->format('U = Y-m-d H:i:s') . "\n";*/

	$table_headers = [ "Listing ID", "Location", "Supplement", "Sale/Rent", "Lotsize Sqft", "Beds", "Baths" ];
	$class = "RE_1";
	$perPage = 10;
	$offset = 0;
?>
<?php endif; ?>