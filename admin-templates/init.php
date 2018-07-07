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
		'Sandicor RETs',
		'Sandicor RETs',
		'manage_options',
		'sandicor',
		function() {
			include "properties.php";
		},
		'dashicons-admin-site',
		20
	);

	add_submenu_page(
		'sandicor',
		'Single Property',
		null,
		'manage_options',
		'add-sandicor',
		function() {
			include sprintf( "single-%s.php", $_GET['type'] );
		}
	);
} );

add_action( 'admin_enqueue_scripts', function() {
	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );
	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__  ), array( 'jquery' ), '1.0.0', true );	
} );