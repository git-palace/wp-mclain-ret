<?php
get_header();
$properties = SI()->getPropertiesByCity( get_the_title() );
?>

<section class="filter-options"></section>

<section class="searched-results">
	<div class="container-fluid">

		<?php if( count( $properties ) ) : ?>
		
			<div class="row">

				<div class="col-xs-6 h-850px">
					<div class="row">
						<?php foreach ( $properties as $idx => $property ) : ?>
						<?php $pictures = json_decode( $property->pictures ); ?>
							
							<div class="col-xs-6 matched-query">
								
								<?php if ( is_array( $pictures ) && !empty( $pictures )  ): ?>

									<div id="listing-images-<?php _e( $idx )?>" class="carousel slide" data-ride="carousel" data-interval="false">
									<!-- Wrapper for slides -->
									<div class="carousel-inner">
										
										<?php foreach ( $pictures as $p_idx => $picture ): ?>
										<div class="item <?php _e( $p_idx == 0 ? 'active' : '' ) ?>">
											<img src="<?php _e( $picture->url ); ?>" style="width:100%;">
										</div>
										<?php endforeach; ?>
									
									</div>

									<!-- Left and right controls -->
									<a class="left carousel-control" role="button" data-target="#listing-images-<?php _e( $idx )?>" data-slide="prev">
									<span><i class="fa fa-chevron-left"></i></span>
									</a>
									<a class="right carousel-control" role="button" data-target="#listing-images-<?php _e( $idx )?>" data-slide="next">
									<span><i class="fa fa-chevron-right"></i></span>
									</a>
								</div>
								<?php else: ?>
									<div class="no-image"></div>

								<?php endif;?>

								<div class="summary">
									<p><a target="_blank" href="/single-property/<?php _e( getValidatedValue( $property, 'listingID' ) ); ?>"><?php _e( getValidatedValue( $property, 'address' ) ); ?>, <?php _e( getValidatedValue( $property, 'city' ) ); ?></a></p>
									<p>
										<a target="_blank" href="/single-property/<?php _e( getValidatedValue( $property, 'listingID' ) ); ?>">
											<?php _e( getValidatedValue( $property, 'beds_num', 'No' ) ); ?> Bed(s)&nbsp;∙&nbsp;
											<?php _e( getValidatedValue( $property, 'baths_num', 'No' ) ); ?> Bath(s)
										</a>
									</p>
									<p><a target="_blank" href="/single-property/<?php _e( getValidatedValue( $property, 'listingID' ) ); ?>"><?php _e( '$' . number_format( floatval( getValidatedValue( $property, 'list_price', 0 ) ) ) ); ?></a></p>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="col-xs-6 h-850px search-result-map-container">
					<div id="search-result-map"></div>
				</div>

			</div>
		
		<?php else: ?>
			<div class="row">
				<div class="col-xs-12">
				<h1 class="text-center" style="margin: 250px 0px;">No result in this city.</h1>
				</div>
			</div>
		<?php endif; ?>

	</div>
</section>

<script type="text/javascript">
	(function() {
		google.maps.event.addDomListener(window, 'load', init_search_result_map);
	})(jQuery);

	<?php 
		$marker_list = array();
	
		foreach ( $properties as $property ) {
			array_push( $marker_list, array(
				'address' => getValidatedValue( $property, 'address' ),
				'lat' => getValidatedValue( $property, 'lat', 0 ),
				'lng' => getValidatedValue( $property, 'lng', 0)
			) );
		}
	?>

	var marker_list = <?php _e( json_encode( $marker_list ) ); ?>;
</script>

<?php get_footer(); ?>