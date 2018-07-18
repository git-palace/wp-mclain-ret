<section class="listing-summary">
	<div class="container">
		<div class="row">
			<div class="col-sm-8">
				<span class="county text-uppercase"><?php _e( getValidatedValue( $property, 'county' ) ); ?></span>
				<h1 class="address">
					<?php _e( getValidatedValue( $property, 'address' ) ); ?>
					<!-- <i class="property-like fa fa-heart-o" data-id="201810187" aria-hidden="true"></i> -->
				</h1>
				<span class="city"><?php _e( getValidatedValue( $property, 'city' ) ); ?></span>
			</div>

			<div class="col-sm-4 text-right text-left-xs">
				<span class="listing-id">MLS# <?php _e( getValidatedValue( $property, 'listingID' ) ); ?></span>
				<h1 class="price"><?php _e( '$' . number_format( floatval( getValidatedValue( $property, 'list_price', 0 ) ), 2 ) ); ?></h1>
				<span class="bb-count text-uppercase">
					<?php _e( getValidatedValue( $property, 'beds_num', 'No' ) ); ?> Bed(s)&nbsp;∙&nbsp;
					<?php _e( getValidatedValue( $property, 'baths_num', 'No' ) ); ?> Bath(s)
				</span>
			</div>
		</div>

		<div class="row status">
			<div class="col-xs-12">
				<span class="pull-left"><?php _e( getValidatedValue( $property, 'status', 'Unknown' ) ); ?></span>
				<span class="pull-right">DAYS ON MARKET: <?php _e( getValidatedValue( $property, 'domls' ) ); ?></span>
			 </div>
		</div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="property-slide">
				<?php $pictures = json_decode( $property->pictures ); ?>
				<?php foreach( $pictures as $picture ): ?>
					<div class="col-xs-4">
						<div class="col-xs-12 img" style="background-image: url( <?php _e( $picture->url ); ?> )">
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<section>
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-7 col-lg-8">
			</div>

			<div class="col-sm-6 col-md-5 col-lg-4">
				<span class="module-icon">
					<img src="<?php _e( plugins_url( 'assets/images/home.svg', __FILE__ ) ); ?>" alt="">
				</span>

				<table class="table table-condensed">
					<tbody>
						<tr>
							<td>Price :</td>
							<td><?php _e( '$' . number_format( floatval( getValidatedValue( $property, 'list_price', 0 ) ), 2 ) ); ?></td>
						</tr>

						<tr>
							<td>Beds :</td>
							<td><?php _e( getValidatedValue( $property, 'beds_num' ) ); ?></td>
						</tr>

						<tr>
							<td>Full Baths :</td>
							<td><?php _e( getValidatedValue( $property, 'baths_num' ) ); ?></td>
						</tr>

						<tr>
							<td>Interior (SQFT) :</td>
							<td><?php _e( number_format( floatval( getValidatedValue( $property, 'inter_sqft', 0 ) ) ) ); ?></td>
						</tr>

						<tr>
							<td>Lot Size (SQFT) :</td>
							<td>
								<?php 
									$lot_size = getValidatedValue( $property, 'lotsize_sqft', 0 );
									_e( $lot_size ? number_format( floatval( $lot_size ) ) : 'Unknown' );
								?>
									
							</td>
						</tr>

						<tr>
							<td>Year Built :</td>
							<td><?php _e( getValidatedValue( $property, 'year_built' ) ); ?></td>
						</tr>

						<tr>
							<td>Inclusions :</td>
							<td><?php _e( getValidatedValue( $property, 'inclusions' ) ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<section></section>

<section></section>