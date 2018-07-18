<?php
require_once( 'search-form.php' );

add_shortcode( 'search-form', function( $atts ) {
	$atts = shortcode_atts(
		array( 'type' => 'simple' ),
		$atts
	);

	if ( in_array( $atts['type'], array( "simple" ) ) )
		call_user_func( $atts['type']."_search_form" );	
});

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'slick', plugins_url( 'assets/slick/slick.css', __FILE__ ) );
	wp_enqueue_style( 'slick-theme', plugins_url( 'assets/slick/slick-theme.css', __FILE__ ) );

	wp_enqueue_script( 'slick', plugins_url( 'assets/slick/slick.min.js', __FILE__ ), array( 'jquery' ), '1.8.1', true );

	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );

	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array( 'slick' ), '1.8.1', true );
});


// add single property page in front end
add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ) {
	$wp_rewrite->rules = array_merge(
		['single-property/(\d+)/?$' => 'index.php?listingID=$matches[1]'],
		$wp_rewrite->rules
	);
} );

add_filter( 'query_vars', function( $query_vars ) {
	$query_vars[] = 'listingID';
	return $query_vars;
} );

add_action( 'template_redirect', function() {
	$listingID = intval( get_query_var( 'listingID' ) );
	if ( $listingID ) {
		get_header();

		if ( function_exists( 'SI' ) ) {
			$property = SI()->getDataBylistingID( $listingID );

			if ( $property)
				include "single-property.php";
			else
				echo "<h1>Invalid Listing ID</h1>";
		}

		get_footer();
		die;
	}
} );