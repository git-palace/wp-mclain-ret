<?php
global $perPages;
$perPages = [10, 20, 35, 50];

global $limits;
$limits = ['perPage' => $perPages[0], 'pageIdx' => 1];

if ( 
	isset( $_GET['perPage'] ) && 
	!empty( $_GET['perPage'] ) && 
	in_array( $_GET['perPage'], $perPages ) 
){
	$limits['perPage'] = intval( $_GET['perPage'] );
}

if (
	isset( $_GET['pageIdx'] ) &&
	!empty( $_GET['pageIdx'] )
) {
	$limits['pageIdx'] = intval( $_GET['pageIdx'] );
}

add_action( 'rest_api_init', function () {
	register_rest_route( 
		'sandicor', 
		'update-config', 
		array(
			'methods' => 'post',
			'callback' => function() {
				if (
					isset( $_POST["login_url"] ) && !empty( $_POST["login_url"] ) &&
					isset( $_POST["username"] ) && !empty( $_POST["username"] ) &&
					isset( $_POST["password"] ) && !empty( $_POST["password"] ) &&
					isset( $_POST["brelicense"] ) && !empty( $_POST["brelicense"] ) &&
					class_exists( "SandicorConfig" )
				) {
					return SandicorConfig::saveConfig( array(
						"login_url" => $_POST["login_url"],
						"username"	=> $_POST["username"],
						"password"	=> $_POST["password"],
						"brelicense" => $_POST["brelicense"],
						"autosave"	=> $_POST["autosave"] 
					) );
				}

				return false;
			}
		)
	);

	register_rest_route( 
		'sandicor', 
		'populate-db', 
		array(
			'methods' => 'post',
			'callback' => function() {
				return SI()->populateDB();
			}
		)
	);
} );

// sandicor instance
function SI() {
	global $sandicor;
	$sandicor = Sandicor::getInstance();	

	return $sandicor;
}

function custom_cron_schedule( $schedules ) {
	$schedules['every_six_hours'] = array(
		'interval' => 21600, // Every 6 hours
		'display'  => __( 'Every 6 hours' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'custom_cron_schedule' );

///Hook into that action that'll fire every six hours
add_action( 'sandicor_cronjob', function() {
	wp_remote_post( home_url( '/wp-json/sandicor/populate-db' ) );
} );

function getPlaceholder( $field ) {
	switch ( $field ) {
		case 'listing_ID':
			return '12345678';

		case 'addr_num':
			return '123';

		case 'addr_st':
			return 'Main St';

		case 'city':
			return 'Carlsbad';

		case 'state':
			return 'CA';

		case 'zip':
			return '92029';

		case 'area':
			return 'Carlsbad (92029)';

		case 'county':
			return 'San Diego';
			
		case 'list_price':
		case 'system_price':
		case 'sold_price':
		case 'low_price':
			return number_format( 700000, 2 );

		case $field:
			return $field;
		
		default:
			return '';
	}
}