<?php

function mclain_rets_options_page()
{
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
	// remove_submenu_page( 'mclain-rets', 'mclain-single-property' );
}
add_action( 'admin_menu', 'mclain_rets_options_page' );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );	
} );