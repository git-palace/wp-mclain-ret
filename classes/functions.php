<?php
if ( isset( $_GET['page'] ) && $_GET['page'] == 'sandicor' && isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['id'] ) && !empty( $_GET['id'] ) ) {
	SI()->deleteFromLocalDB( array( 'id' => $_GET['id'] ) );
}
//==============================================================================================//
global $perPages;
$perPages = [10, 20, 35, 50];

global $limits;
$limits = ['perPage' => $perPages[0], 'pageIdx' => 1];

if ( isset( $_GET['perPage'] ) && 	!empty( $_GET['perPage'] ) && 	in_array( $_GET['perPage'], $perPages ) ){
	$limits['perPage'] = intval( $_GET['perPage'] );
}

if ( isset( $_GET['pageIdx'] ) &&	!empty( $_GET['pageIdx'] ) ) {
	$limits['pageIdx'] = intval( $_GET['pageIdx'] );
}
//==============================================================================================//

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
					isset( $_POST["google_api_key"] ) && !empty( $_POST["google_api_key"] ) &&
					class_exists( "SandicorConfig" )
				) {
					return SandicorConfig::saveConfig( array(
						"login_url" 	=> $_POST["login_url"],
						"username"		=> $_POST["username"],
						"password"		=> $_POST["password"],
						"brelicense"	=> $_POST["brelicense"],
						"autosave"		=> $_POST["autosave"],
						"google_api_key" => $_POST["google_api_key"]
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
				return SI()->populateDB( true );
			}
		)
	);

	register_rest_route(
		'sandicor',
		'add-new-property',
		array(
			'methods'	=> 'post',
			'callback' => 'addNewRecordtoLocalDB'
		)
	);

	register_rest_route(
		'sandicor',
		'add-new-openhouse',
		array(
			'methods'	=> 'post',
			'callback' => 'addNewRecordtoLocalDB'
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
	delete_transient( "populating-db" );
	wp_remote_post( home_url( '/wp-json/sandicor/populate-db' ) );
} );

function getPlaceholder( $field ) {
	switch ( $field ) {
		case 'listingID':
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

		case 'parking_total':
		case 'beds_num':
		case 'baths_num':
			return '3';

		case 'year_built':
			return date("Y");

		case 'inter_sqft':
			return '1440';

		case 'lotsize_sqft':
			return '114840';

		case 'domls':
			return '7';

		case 'listing_date':
			return date("Y-m-d");
		
		default:
			return '';
	}
}

// get value with validation
function getValidatedValue( $property, $field, $default = '' ) {
	$v_value = json_decode( json_encode( $property ), true );

	if ( isset( $v_value[$field] ) )
		return $v_value[$field];

	return $default;
}

// post single property / openhouse form
function addNewRecordtoLocalDB() {
	if ( $_POST['action'] != 'sandicor_update' || !isset( $_POST['sandicor'] ) || empty( $_POST['sandicor'] ) )
		return;

	$property = $_POST['sandicor'];

	$pictures = [];

	if ( isset( $_POST['sandicor']['pictures'] ) ) {
		foreach ( $_POST['sandicor']['pictures'] as $key => $picture ) {
			array_push( $pictures, ['url' => $picture['url'], 'desc' => $picture['desc'] ]);
		}
	}

	$sandicor = [
		'L_ListingID' 		=> getValidatedValue( $property, 'listingID' ),
		'L_Resource'		=> getValidatedValue( $property, 'resource' ),
		'L_AddressNumber'	=> getValidatedValue( $property, 'addr_num' ),
		'L_AddressStreet'	=> getValidatedValue( $property, 'addr_st' ),
		'L_Address2'		=> getValidatedValue( $property, 'addr_2' ),
		'L_Area'			=> getValidatedValue( $property, 'area' ),
		'LM_Char10_1'		=> getValidatedValue( $property, 'county' ),
		'L_City'			=> getValidatedValue( $property, 'city' ),
		'L_State'			=> getValidatedValue( $property, 'state' ),
		'L_Zip'				=> getValidatedValue( $property, 'zip' ),
		'L_Status'			=> getValidatedValue( $property, 'status' ),
		'L_SystemPrice'		=> getValidatedValue( $property, 'system_price' ),
		'created_by'		=> getValidatedValue( $property, 'created_by' ),
		'L_AskingPrice'		=> getValidatedValue( $property, 'list_price' ),
		'L_asking_price_low'	=> getValidatedValue( $property, 'low_price' ),
		'LR_remarks66'		=> getValidatedValue( $property, 'supplement' ),
		'L_ListingDate'		=> getValidatedValue( $property, 'listing_date' ),
		'L_SaleRent'		=> getValidatedValue( $property, 'sr_type' ),
		'LFD_View_44'		=> getValidatedValue( $property, 'v_type' ),
		'LFD_ParkingGarage_22'	=> getValidatedValue( $property, 'c_parking' ),
		'LM_Int1_3'			=> getValidatedValue( $property, 'beds_num' ),
		'LM_Int2_6'			=> getValidatedValue( $property, 'baths_num' ),
		'L_Pictures'		=> json_encode( $pictures ),
		'L_PictureCount'	=> getValidatedValue( $property, 'photo_count' ),
		'LM_Int2_1'			=> getValidatedValue( $property, 'year_built' ),
		'LM_Int4_1'			=> getValidatedValue( $property, 'inter_sqft' ),
		'LM_Int4_6'			=> getValidatedValue( $property, 'lotsize_sqft' ),
		'LM_Int4_8'			=> getValidatedValue( $property, 'parking_total' ),
		'L_SoldPrice'		=> getValidatedValue( $property, 'sold_price' ),
		'L_DOMLS'			=> getValidatedValue( $property, 'domls' ),
		'LFD_HomeOwnersFeeIncludes_13'	=> getValidatedValue( $property, 'inclusions' ),
		'L_Address'			=> getValidatedValue( $property, 'address' ),
		'OH_StartDateTime'	=> getValidatedValue( $property, 'start_datetime' ),
		'OH_EndDateTime'	=> getValidatedValue( $property, 'end_datetime' ),
		'L_Class'			=> getValidatedValue( $property, 'class' ),

	];

	SI()->addToLocalDB( $sandicor );

	return $sandicor;
}

function convertAddress2Lat_Lng( $address, $apiKey ) {
	$url = sprintf( "https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s", str_replace( ' ', '+', $address ), $apiKey );
	$response = json_decode( wp_remote_get( $url )['body'] );

	if( $response->status == "OK" && isset( $response->results[0] ))
		return $response->results[0]->geometry->location;
	else
		return false;
}

function getListingsNearby( $property, $lat_lng = [], $limit = 3 ) {
	$results = SI()->getDataFromLocalDB( [
		'perPage'	=>	'all'
	], [
		'city'		=>	getValidatedValue( $property, 'city' ),
		'resource'	=>	getValidatedValue( $property, 'resource' )
	] );

	$results = json_decode( json_encode( $results ), true );

	foreach ( $results as $key => $result ) {
		if ( $property->listingID == $result['listingID'] ) {
			unset( $results[$key] );
		} else {
			$r_lat_lng = convertAddress2Lat_Lng( getValidatedValue( $result, 'address' ), SI()->getGoogleAPIKey() );
			
			if ( $r_lat_lng) {
				$results[$key]['distance'] = sqrt( pow( abs( $lat_lng->lat - $r_lat_lng->lat ), 2 ) + pow( abs( $lat_lng->lng - $r_lat_lng->lng ), 2) );
			} else {
				unset( $results[$key] );				
			}
		}
	}

	usort( $results, function( $a, $b ) {
		return $a['distance'] > $b['distance'];
	} );

	return json_decode( json_encode ( array_slice( $results, 0, 3 ) ) );
}