<?php
add_action( 'admin_menu', function() {
	add_menu_page(
		'McLain RETS',
		'Mclain RETS',
		'manage_options',
		'mclain-rets',
		function() {
			include "config.php";
		}
	);

	add_submenu_page(
		'mclain-rets',
		'McLain Properties',
		'Properties',
		'manage_options',
		'mclain-rets-properties',
		function() {
			include "manage-properties.php";			
		}
	);

	add_submenu_page(
		'mclain-rets',
		'Single Property in Mclain',
		null,
		'manage_options',
		'mclain-single-property',
		function() {
			include "single-property.php";			
		}
	);
} );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );	
} );

// make address string
function getPropertyAddress( $property ) {
	$address_str = "";

	if ( !empty( $property->addr_num ) )
		$address_str .= $property->addr_num;

	if ( !empty( $property->addr_st ) )
		$address_str .= ' ' . $property->addr_st;

	if ( !empty( $property->addr_2 ) )
		$address_str .= ', ' . $property->addr_2;

	if ( !empty( $property->city ) )
		$address_str .= '<br> ' . $property->city;

	if ( !empty( $property->state ) )
		$address_str .= ', ' . $property->state;

	if ( !empty( $property->zip ) )
		$address_str .= ' ' . $property->zip;

	return trim( $address_str );
}