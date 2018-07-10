<?php
$fields = SI()->getExcludedHeaders( 'property', ['Resource', 'Address', 'Inclusions', 'Photos Count'] );
?>

<div class="wrap">
	<form id="sandicor-config">
		<input type="hidden" name="action" value="sandicor_update">
		<input type="hidden" name="sandicor['id']">

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

							<?php elseif( in_array( $key, ['v_type', 'c_parking', 'status'] ) ) : ?>
								<?php
									$data = [];
									switch ( $key ) {
										case 'v_type':
											$data = SI()->getAllViewTypeList();
											break;

										case 'c_parking':
											$data = SI()->getAllCoveredParkingList();
											break;

										case 'status':
											$data = SI()->getAllStatusList();
											break;
										
										default:
											$data = [];
											break;
									}
								?>

								<select name="sandicor['<?php _e( $key ); ?>']">
									<?php foreach ( $data as $val => $txt ) : ?>
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

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( $_GET['action'] == 'new' ? 'Add New' : 'Save Changes' ); ?>">
		</p>
	</form>
</div>