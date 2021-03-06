<tr>
	<td class="text-center valign-middle"><?php _e( 1 + $idx + ($limits['pageIdx'] - 1) * $limits['perPage'] ) ?></td>
	<?php foreach ( $headers as $key => $value): ?>
		<td class="valign-middle">
			<?php 
				switch ( $key ) {
					case 'list_price':
					case 'system_price':
					case 'sold_price':
					case 'low_price':
						if ( !empty( $result->$key ) )
							_e( '$' . number_format( floatval( $result->$key )) );
						break;
					
					default:
						_e( $result->$key );
						break;
				}
			?>
		</td>
	<?php endforeach; ?>
	<td align="center">
		<a href="/wp-admin/admin.php?page=add-sandicor&id=<?php _e( $result->ID ); ?>&resource=property&action=edit">Edit</a>
		<span>&nbsp;|&nbsp;</span>
		<a href="/wp-admin/admin.php?page=sandicor&action=delete&id=<?php _e( $result->ID ); ?>">Delete</a>
		<span>&nbsp;|&nbsp;</span>
		<a href="/single-property/<?php _e( $result->listingID ); ?>" target="_blank">View</a>
	</td>
</tr>