<tr>
	<td align="center" valign="middle"><?php _e( 1 + $idx + ($limits['pageIdx'] - 1) * $limits['perPage'] ) ?></td>
	<?php foreach ( $headers as $key => $value): ?>
		<td valign="middle">
			<?php 
				switch ( $key ) {
					case 'list_price':
					case 'system_price':
					case 'sold_price':
					case 'low_price':
						if ( !empty( $result->$key ) )
							_e( '$ ' . number_format( floatval( $result->$key ), 2 ) );
						break;
					
					default:
						_e( $result->$key );
						break;
				}
			?>
		</td>
	<?php endforeach; ?>
	<td align="center">
		<a href="/wp-admin/admin.php?page=sandicor-single-property&id=<?php _e( $result->ID ); ?>&action=edit">Edit</a>
		<span>&nbsp;|&nbsp;</span>
		<a href="#<?php _e( $result->ID ); ?>">Delete</a>
		<span>&nbsp;|&nbsp;</span>
		<a href="/wp-admin/admin.php?page=sandicor-single-property&id=<?php _e( $result->ID ); ?>&action=details">View</a>
	</td>
</tr>