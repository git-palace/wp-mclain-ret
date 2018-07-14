<?php
$sandicor = [];

if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
	$sandicor = SI()->getDataFromLocalDB( ['perPage' => 1, 'pageIdx' => 1], ['id' => $_GET['id']] );
	$sandicor = count( $sandicor ) ? $sandicor[0] : $sandicor;
}
$fields = SI()->getExcludedHeaders( 'property', ['Resource', 'Address', 'Inclusions', 'Photos Count'] );
?>

<div class="wrap">
	<form id="sandicor-update">
		<input type="hidden" name="action" value="sandicor_update">
		<input type="hidden" name="sandicor[resource]" id="resource_type" value="property">
		<input type="hidden" name="sandicor[created_by]" value="manual">
		<input type="hidden" name="sandicor[id]">

		<h1 class="wp-heading-inline">Add New Property</h1>
		<hr class="wp-header-end">

		<table class="form-table">
			<tbody>
				<?php foreach ( $fields as $key => $field) : if ( $key == 'picture_count' ) continue;?>
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

								<select name="sandicor[<?php _e( $key ); ?>]">
									<option value="For Sale">For Sale</option>
									<option value="For Rent">For Rent</option>
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

								<select name="sandicor[<?php _e( $key ); ?>]">
									<?php foreach ( $data as $val => $txt ) : ?>
										<option value="<?php _e( $txt ); ?>" <?php _e( getValidatedValue( $sandicor, $key ) == $txt ? 'selected' : '' ) ?>><?php _e( $txt ); ?></option>
									<?php endforeach ?>
								</select>
								
							<?php elseif ( $key == 'pictures' ): ?>
								<ul id="property_pictures" class="d-flex flex-wrap">

									<?php foreach( json_decode( getValidatedValue( $sandicor, $key ) ) as $idx => $picture ) : ?>

										<li class="picture d-flex flex-column">
											<a class="remove-picture d-none text-center"><span class="m-auto">&times;</span></a>
											<img class="img-fluid" src="<?php _e( $picture->url ); ?>" />
											<div class="summary d-flex align-items-center">
												<label class="mr-auto" for="sandicor[<?php _e( $key ) ?>][<?php _e( $idx ) ?>]">Description : </label>
												<input class="ml-auto" type="text" name="sandicor[<?php _e( $key ) ?>][<?php _e( $idx ) ?>]" value="<?php _e( $picture->desc ); ?>" />
											</div>
										</li>

									<?php endforeach; ?>

								</ul>

							<?php else: ?>

								<input type="text" name="sandicor[<?php _e( $key ); ?>]" class="regular-text" placeholder="<?php _e( getPlaceholder( $key ) ); ?>" value="<?php _e( getValidatedValue( $sandicor, $key ) ); ?>">

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