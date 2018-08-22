<section class="filter-options"></section>

<section class="searched-results">
	<div class="container-fluid">

		<div class="row">

			<div class="col-xs-6">
				<div class="row">
					<?php foreach ( $properties as $idx => $property ) : ?>
			    	<?php $pictures = json_decode( $property->pictures ); ?>
						
						<div class="col-xs-6">
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
						    <a class="left carousel-control" href="#listing-images-<?php _e( $idx )?>" data-slide="prev">
						      <span><i class="fa fa-chevron-left"></i></span>
						    </a>
						    <a class="right carousel-control" href="#listing-images-<?php _e( $idx )?>" data-slide="next">
						      <span><i class="fa fa-chevron-right"></i></span>
						    </a>
						  </div>
							<a href=""><?php _e( $property->address ) ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="col-xs-6">
			</div>

		</div>

	</div>
</section>