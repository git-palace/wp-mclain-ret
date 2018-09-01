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
				<h1 class="price"><?php _e( '$' . number_format( floatval( getValidatedValue( $property, 'list_price', 0 ) ) ) ); ?></h1>
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

<section class="listing-details">
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-md-7 col-lg-8">
				<h1>Property Description</h1>
				<p class="supplement"><?php _e( getValidatedValue( $property, 'supplement' ) ); ?></p>
			</div>

			<div class="col-sm-6 col-md-5 col-lg-4 table-container">
				<span class="house-icon">
					<img src="<?php _e( plugins_url( 'assets/images/home.svg', __FILE__ ) ); ?>" alt="">
				</span>

				<table class="table table-condensed">

					<tbody>
						<tr>
							<td>Price :</td>
							<td><b><?php _e( '$' . number_format( floatval( getValidatedValue( $property, 'list_price', 0 ) ) ) ); ?></b></td>
						</tr>

						<tr>
							<td>Beds :</td>
							<td><b><?php _e( getValidatedValue( $property, 'beds_num' ) ); ?></b></td>
						</tr>

						<tr>
							<td>Full Baths :</td>
							<td><b><?php _e( getValidatedValue( $property, 'baths_num' ) ); ?></b></td>
						</tr>

						<tr>
							<td>Interior (SQFT) :</td>
							<td><b><?php _e( number_format( floatval( getValidatedValue( $property, 'inter_sqft', 0 ) ) ) ); ?></b></td>
						</tr>

						<tr>
							<td>Lot Size (SQFT) :</td>
							<td>
								<b>
									<?php 
										$lot_size = getValidatedValue( $property, 'lotsize_sqft', 0 );
										_e( $lot_size ? number_format( floatval( $lot_size ) ) : 'Unknown' );
									?>
								</b>								
							</td>
						</tr>

						<tr>
							<td>Year Built :</td>
							<td><b><?php _e( getValidatedValue( $property, 'year_built' ) ); ?></b></td>
						</tr>

						<tr>
							<td colspan="2">
								View : <br>
								<small><?php _e( getValidatedValue( $property, 'v_type' ) ); ?></small>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								Inclusions :<br/>
								<small><?php _e( getValidatedValue( $property, 'inclusions' ) ); ?></small>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</section>

<section class="listing-map">
	<div class="container">
		<div class="row">
			<div class="col-md-6 property">
				<iframe width ="100%" height="350" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/search?key=<?php _e( SI()->getGoogleAPIKey() ); ?>&q=US+<?php _e( str_replace( ' ', '+', getValidatedValue( $property, 'area') )) ?>&zoom=13" allowfullscreen>
				</iframe>
			</div>

			<div class="col-md-6">
				<h2><?php _e( getValidatedValue( $property, 'city' ) ) ?></h2>
				<p></p>
				<a href="/<?php _e( sanitize_title( getValidatedValue( $property, 'city' ) ) ) ?>" class="visit btn">Visit <?php _e( getValidatedValue( $property, 'city' ) ) ?></a>
			</div>
		</div>
	</div>
</section>

<section class="listing-nearby">
	<div class="container">
		<h1 class="col-xs-12">Nearby Properties</h1>

		<div class="row">
			<?php $near_listings = getListingsNearby( $property ); ?>


			<?php foreach ( $near_listings as $s_listing ) : ?>
				
				<div class="property col-sm-6 col-md-4">
					<a href="/single-property/<?php _e( getValidatedValue( $s_listing, 'listingID' ) ); ?>" >
						<div class="thumbnail">
							<?php
								$pictures = json_decode( $s_listing->pictures, true ); 
							?>
							<?php if ( count( $pictures ) ) : ?>
								<img src="<?php _e( $pictures[0]['url'] ) ?>">
							<?php else: ?>
								<span class="no-image">No Image</span>
							<?php endif; ?>
						</div>

						<div class="summary">
							<p class="address"><?php _e( getValidatedValue( $s_listing, 'address' ) ); ?></p>
							<p class="price"><?php _e( '$' . number_format( floatval( getValidatedValue( $s_listing, 'list_price', 0 ) ) ) ); ?></p>
						</div>
					</a>
				</div>

			<?php endforeach; ?>
		</div>
	</div>
</section>