<?php
add_action( 'rest_api_init', function () {
	register_rest_route( 
		'mclain-rets', 
		'update-config', 
		array(
			'methods' => 'post',
			'callback' => 'update_mclain_config',
		)
	);
} );

function update_mclain_config() {
	if (
		isset( $_POST["login_url"] ) && !empty( $_POST["login_url"] ) &&
		isset( $_POST["username"] ) && !empty( $_POST["username"] ) &&
		isset( $_POST["password"] ) && !empty( $_POST["password"] ) &&
		class_exists( "MCRETS_Config" )
	) {
		MCRETS_Config::saveConfig( array(
			"login_url" => $_POST["login_url"],
			"username"	=> $_POST["username"],
			"password"	=> $_POST["password"],
			"autosave"	=> $_POST["autosave"] 
		) );

		return true;
	}
	return false;
}

function MCR() {
	global $mcrets;
	$mcrets = MCRETS::getInstance();	

	return $mcrets;
}