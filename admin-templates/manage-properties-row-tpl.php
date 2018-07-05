<tr>
	<td valign="middle"><?php _e( 1 + $idx + ($limits['pageIdx'] - 1) * $limits['perPage'] ) ?></td>
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
	<td>
		<a href="/wp-admin/admin.php?page=mclain-single-property&id=<?php _e( $result->ID ); ?>&action=details">Details</a>
	</td>
</tr>