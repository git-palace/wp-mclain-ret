<?php if ( !MCR()->login() ) : ?>
<script type="text/javascript">
	window.location.href = "/wp-admin/admin.php?page=mclain-rets-config";
</script>
<?php else: ?>
<?php
	MCR()->populateDB();
?>
<?php endif; ?>