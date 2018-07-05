<tr>
	<td align="center" valign="middle"><?php _e( 1 + $idx + ($limits['pageIdx'] - 1) * $limits['perPage'] ) ?></td>
	<?php foreach ( $headers as $key => $value): ?>
		<td valign="middle">
			<?php 
				switch ( $key ) {
					case 'address':
						_e( getPropertyAddress( $result ) );
						break;

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
</tr>