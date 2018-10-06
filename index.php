<?php
/*
	Plugin Name:  Sandicor RETs
	Plugin URI:   https://github.com/git-palace/wp-sandicor-rets
	Description:  Sandicor RETs
*/

define( 'SR_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'init', function() {
	if ( is_user_logged_in() && !current_user_can( 'administrator' ) )
		show_admin_bar( false );
} );

require_once( SR_PLUGIN_PATH . 'classes/init.php' );

require_once( SR_PLUGIN_PATH . 'templates/init.php' );

require_once( SR_PLUGIN_PATH . 'admin-templates/init.php' );