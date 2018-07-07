<?php
$fields = SI()->getExcludedHeaders( 'property', ['Resource', 'Address', 'Inclusions'] );
?>

<div class="wrap">
	<form id="sandicor-config">
		<h1 class="wp-heading-inline">Add New Property</h1>
		<hr class="wp-header-end">

		<table class="form-table">
			<tbody>
				<?php foreach ( $fields as $key => $field) : ?>
					<tr>
						<th scope="row">
							<label for="<?php _e( $key ); ?>">
								<?php 
									_e( $field );

									if ( strpos( $key, 'price' ) !== false ) 
										_e(' ($) ') 
								?>
							</label>
						</th>
						<td>
							<?php if ( $key == 'sr_type' ) : ?>
								<select name="sandicor['<?php _e( $key ); ?>']">
									<option value="S">For Sale</option>
									<option value="R">For Rent</option>
								</select>
							<?php elseif( $key == 'v_type' ) : ?>
								<select name="sandicor['<?php _e( $key ); ?>']">
									<?php foreach ( SI()->getAllViewTypeList() as $val => $txt ) : ?>
										<option value="<?php _e( $val ); ?>"><?php _e( $txt ); ?></option>
									<?php endforeach ?>
								</select>
							<?php else: ?>
								<input type="text" name="sandicor['<?php _e( $key ); ?>']" class="regular-text" placeholder="<?php _e( getPlaceholder( $key ) ); ?>">
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</form>
</div>