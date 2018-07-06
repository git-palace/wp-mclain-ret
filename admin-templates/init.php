<?php
add_action( 'admin_menu', function() {
	add_options_page(
		'Sandicor Configuration',
		'Sandicor Configuration',
		'manage_options',
		'sandicor-config',
		function() {
			include "config.php";
		}
	);

	add_menu_page(
		'Sandicor',
		'Sandicor',
		'manage_options',
		'mclain-properties',
		function() {
			include "properties.php";
		}
	);

	/*add_submenu_page(
		'mclain-properties',
		'Single Property in Mclain',
		null,
		'manage_options',
		'mclain-single-property',
		function() {
			include "single-property.php";			
		}
	);*/
} );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );	
} );