<?php

function mclain_rets_options_page()
{
	add_menu_page(
		'McLain RETS Management',
		'MCLain RETS',
		'manage_options',
		'mclain-rets'
	);

	add_submenu_page(
		'mclain-rets',
		'McLain RETS Configuration',
		'Configuration',
		'manage_options',
		'mclain-rets-config',
		function() {
			include "config.php";
		}
	);
}
add_action( 'admin_menu', 'mclain_rets_options_page' );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );
	
} );