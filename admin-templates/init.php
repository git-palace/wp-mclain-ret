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
}
add_action( 'admin_menu', 'mclain_rets_options_page' );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );	
} );