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
	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );
});