<?php
class Sandicor {
	private static $instance = null;
	private $rets = null;
	private $properties;
	private $openhouses;
	private $brelicense;
	private $google_api_key;

	// constructor
	private function __construct() {
		if ( class_exists( "SandicorConfig") ) {
			$config = SandicorConfig::setConfiguration();
			$this->rets = $config["rets"];
			$this->brelicense = $config["brelicense"];
			$this->google_api_key = $config["google_api_key"];
		}
	}

	// get instance for singleton design pattern
	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new Sandicor();

		return self::$instance;
	}

	// login to sandicor
	function login() {
		try {
			$this->rets->Login();
			
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	// get table name

	// get Resource Classes
	function getResourceClasses( $resource = "property" ) {
		switch ( $resource ) {
			case 'property':
				return ['RE_1', 'RI_2', 'LN_3', 'RT_4', 'RN_6', 'OF_9', 'LR_11'];

			case 'open_house':
				return ['RE_1', 'RI_2'];
		}

		return [];
	}

	// populate properties
	function getDataFromSandicor( $filter = 'Property', $class = 'RE_1' ) {
		$args = [];

		switch ( $filter) {
			case 'Property':
				$args = array_merge( $args, [
					'LM_char10_48'	=> $this->brelicense
				] );
				break;
			
			case 'OpenHouse':
				$args = array_merge( $args, [
					'OH_UniqueID'	=> '0+',
				] );
				break;
		}

		$dmql = '(';
		foreach ( $args as $key => $value )
			$dmql .= sprintf('%s=%s', $key, $value) . ( $value == end( $args ) ? '' : '),(' );
		$dmql .= ')';

		return $this->rets->Search( $filter, $class, $dmql );
	}

	// get pictures from sandicor
	function getPicturesFromSandicor( $resource, $listingID, $json_format = false ) {
		$results = [];

		$objects = $this->rets->GetObject( $resource, 'Photo', $listingID, '*', 1 );

		foreach ( $objects as $key => $obj) {
			if ( $obj->isError() ) continue;
			
			array_push( $results, [
				"url"	=> $obj->getLocation(),
				"desc"	=> $obj->getContentDescription()
			] );
		}

		return $json_format ? json_encode( $results ) : $results;
	}

	// auto populate database
	function populateDB( $force = false ) {
		if ( !$this->login() ) {
			return "failed to login";
		}

		ini_set('max_execution_time', 0);

		global $wpdb;
		$table_name = $wpdb->prefix . "sandicor_rets";

		// create table if it's not exisitng
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "
				CREATE TABLE $table_name (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					listingID varchar(10) NOT NULL, 
					resource varchar(20) NOT NULL DEFAULT '',
					address varchar(200) NOT NULL DEFAULT '',	
					addr_num varchar(15) NOT NULL DEFAULT '',
					addr_st varchar(50) NOT NULL DEFAULT '',
					addr_2 varchar(50) NOT NULL DEFAULT '',
					city varchar(50) NOT NULL DEFAULT '',
					state varchar(2) NOT NULL DEFAULT '',
					zip varchar(20) NOT NULL DEFAULT '',
					area varchar(50) NOT NULL DEFAULT '',
					county  varchar(10) NOT NULL DEFAULT '',
					list_price varchar(10) NOT NULL DEFAULT '',
					system_price varchar(10) NOT NULL DEFAULT '',
					sold_price varchar(10) NOT NULL DEFAULT '',
					low_price varchar(10) NOT NULL DEFAULT '',
					supplement longtext NOT NULL DEFAULT '',
					sr_type varchar(20) NOT NULL DEFAULT '',
					v_type varchar(20) NOT NULL DEFAULT '',
					c_parking varchar(50) NOT NULL DEFAULT '',
					parking_total varchar(10) NOT NULL DEFAULT '',
					beds_num varchar(3) NOT NULL DEFAULT '',
					baths_num varchar(5) NOT NULL DEFAULT '',
					pictures longtext NOT NULL DEFAULT '',
					picture_count varchar(10) NOT NULL DEFAULT '',
					year_built varchar(20) NOT NULL DEFAULT '',
					inter_sqft varchar(10) NOT NULL DEFAULT '',
					lotsize_sqft varchar(10) NOT NULL DEFAULT '',
					domls varchar(10) NOT NULL DEFAULT '',
					inclusions longtext NOT NULL DEFAULT '',
					start_datetime varchar(20) NOT NULL DEFAULT '',
					end_datetime varchar(20) NOT NULL DEFAULT '',
					class varchar(50) NOT NULL DEFAULT '',
					listing_date varchar(20) NOT NULL DEFAULT '',
					lat varchar(20) NOT NULL DEFAULT '',
					lng varchar(20) NOT NULL DEFAULT '',
					status varchar(20) NOT NULL DEFAULT '',
					created_by varchar(20) NOT NULL DEFAULT '',
					created_at varchar(20) NOT NULL DEFAULT '',
					PRIMARY KEY  (ID),
					UNIQUE KEY `listingID` (`listingID`)
				) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			delete_transient( "populating-db" );
		}

		if ( $force )
			delete_transient( "populating-db" );

		$listingIDs = [];
		$populating = get_transient( "populating-db" );

		if ( $populating == "yes" )
			return $listingIDs;
		else
			set_transient( "populating-db", "yes", 72000 );

		$filters = array(
			'property' => array(
				'resource' => 'Property',
				'classes' => $this->getResourceClasses( "property" )
			),
			'open_house' => array(
				'resource' => 'OpenHouse',
				'classes' => $this->getResourceClasses( "open_house" )
			)
		);

		foreach ( $filters as $resource => $filter ) {
			foreach ( $filter['classes'] as $class ) {
				$results = $this->getDataFromSandicor( $filter['resource'], $class );

				if ( !count( $results ) )
					break;

				foreach ( $results as $result ) {
					if ( !in_array( $result['L_ListingID'], $listingIDs ) )
						array_push( $listingIDs, $result['L_ListingID'] );

					$result['L_Pictures'] = $this->getPicturesFromSandicor( $filter['resource'], $result['L_ListingID'], true );
					$result['L_Resource'] = $resource;
					$result['created_by'] = 'sandicor';
					$this->addToLocalDB( $result );
				}
			}
		}
		
		delete_transient( "populating-db" );
		return $listingIDs;
	}

	// detelte from local
	function deleteFromLocalDB( $where ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "sandicor_rets";


		if ( isset( $where['id'] ) && !empty( $where['id'] ) )
			$wpdb->delete( $table_name, array( 'id' => $where['id'] ) );
		elseif( isset( $where['listingID'] ) && !empty( $where['listingID'] ) )
			$wpdb->delete( $table_name, array( 'listingID' => $where['listingID'] ) );
	}

	// add to database
	function addToLocalDB( $property ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "sandicor_rets";

		$default = array(
			'listingID' 	=> $property['L_ListingID'],
			'resource'		=> $property['L_Resource'],
			'addr_num'		=> $property['L_AddressNumber'],
			'addr_st'		=> $property['L_AddressStreet'],
			'addr_2'		=> $property['L_Address2'],
			'city'			=> $property['L_City'],
			'state'			=> $property['L_State'],
			'zip'			=> $property['L_Zip'],
			'status'		=> $property['L_Status'],
			'system_price'	=> $property['L_SystemPrice'],
			'created_at'	=> date('Y-m-d H:i:s'),
			'created_by'	=> $property['created_by'],
			'lat'	=> '-1',
			'lng'	=> '-1'
		);

		switch ( $property['L_Resource'] ) {
			case 'property':
				$address = "";

				if ( !empty( $property['L_AddressNumber'] ) )
					$address .= $property['L_AddressNumber'];

				if ( !empty( $property['L_AddressStreet'] ) )
					$address .= ' ' . $property['L_AddressStreet'];

				$data = array_merge( $default, [
					'address'		=> $address,
					'area'			=> $property['L_Area'],
					'list_price'	=> $property['L_AskingPrice'],
					'low_price'		=> $property['L_asking_price_low'],
					'supplement'	=> $property['LR_remarks66'],
					'listing_date'	=> $property['L_ListingDate'],
					'sr_type'		=> $property['L_SaleRent'],
					'v_type'		=> $property['LFD_View_44'],
					'c_parking'		=> $property['LFD_ParkingGarage_22'],
					'beds_num'		=> $property['LM_Int1_3'],
					'baths_num'		=> $property['LM_Int2_6'],
					'county'		=> $property['LM_Char10_1'],
					'pictures'		=> $property['L_Pictures'],
					'picture_count'	=> $property['L_PictureCount'],
					'year_built'	=> $property['LM_Int2_1'],
					'inter_sqft'	=> $property['LM_Int4_1'],
					'lotsize_sqft'	=> $property['LM_Int4_6'],
					'parking_total'	=> $property['LM_Int4_8'],
					'sold_price'	=> $property['L_SoldPrice'],
					'domls'			=> $property['L_DOMLS'],
					'inclusions'		=> $property['LFD_HomeOwnersFeeIncludes_13'],
				] );
				break;

			case 'open_house':
				$data = array_merge( $default, [
					'address'			=> $property['L_Address'],
					'start_datetime'	=> $property['OH_StartDateTime'],
					'end_datetime'		=> $property['OH_EndDateTime'],
					'class'				=> $property['L_Class']
				] );
				break;
		}

		$lat_lng = $this->convertAddress2Lat_Lng( implode( ' ', [ $data['addr_num'], $data['addr_st'], $data['addr_2'], $data['city'], $data['state'], $data['zip'] ] ) );
			
		if ( $lat_lng ) {
			$data['lat'] = $lat_lng->lat;
			$data['lng'] = $lat_lng->lng;
		}

		foreach ( $data as $key => $value ) {
			if ( is_null( $value ) )
				$data[$key] = '';
		}

		$wp_success = $wpdb->update( $table_name, $data, array( 'listingID' => $property['L_ListingID'] ) );

		if (!$wp_success)
			$wp_success = $wpdb->insert( $table_name, $data );
	}

	// get table headers
	function getExcludedHeaders( $resource = 'all', $excludes = [] ) {
		$default = [
			'listingID' 	=> 'Listing ID',
			'resource'		=> 'Resource',
			'address'		=> 'Address',
			'addr_num'		=> 'Address Num',
			'addr_st'		=> 'Address St',
			'addr_2'		=> 'Address 2',
			'city'			=> 'City',
			'state'			=> 'State',
			'zip'			=> 'zip',
			'area'			=> 'Area ',
			'county'		=> 'County',
			'list_price'	=> 'List Price',
			'system_price'	=> 'System Price',
			'sold_price'	=> 'Sold Price',
			'low_price'		=> 'Low Price',
			'supplement'	=> 'Supplement',
			'sr_type'		=> 'For Sale / Rent',
			'v_type'		=> 'View Type',
			'c_parking'		=> 'Parking Garage',
			'parking_total'	=> 'Parking Total',
			'beds_num'		=> 'Bedrooms',
			'baths_num'		=> 'Bathrooms',
			'pictures'		=> 'Pictures',
			'picture_count'	=> 'Picture Count',
			'year_built'	=> 'Year Built',
			'inter_sqft'	=> 'Interior Sqft',
			'lotsize_sqft'	=> 'Lot size Sqft',
			'domls'			=> 'Days on Market',
			'inclusions'	=> 'Inclusions',
			'start_datetime'=> 'Start Datetime',
			'end_datetime'	=> 'End Datetime',
			'class'			=> 'Class',
			'listing_date'	=> 'Listing Date',
			'status'		=> 'Status'
		];

		$headers = [];
		switch ( $resource ) {
			case 'property':
				$headers = array_diff( $default, [ 'Start Datetime', 'End Datetime', 'Class' ] );
				break;
			
			case 'open_house':
				break;

			default:
				break;
		}

		$headers = array_merge( $headers, ['status' => 'Status'] );

		return array_diff( $headers, $excludes );
	}

	// get Data from local db
	function getDataFromLocalDB( $limits = ['perPage' => 10, 'pageIdx' => 1], $where = [] ) {
		global $wpdb;
		
		$sql = sprintf( "SELECT * FROM `%s` WHERE", $wpdb->prefix . "sandicor_rets" );

		if ( !count( $where ) ) {
			$sql .= " 1";
		}

		foreach ( $where as $field => $value ) {
			if ( end( $where ) == $value )
				$sql .= sprintf( " `%s` = '%s'", $field, $value );
			else
				$sql .= sprintf( " `%s` = '%s' AND", $field, $value );
		}

		if ( isset( $limits['perPage'] ) && !empty( $limits['perPage'] ) ) {
			if ( $limits['perPage'] !== 'all' && isset( $limits['pageIdx'] ) && !empty( $limits['pageIdx'] ))
				$sql .= sprintf( " LIMIT %d, %d", ( intval( $limits['pageIdx'] ) - 1 ) * $limits['perPage'], $limits['perPage'] );
		}

		return $wpdb->get_results( $sql, OBJECT );
	}

	function getDataBylistingID( $listingID = -1 ) {
		global $wpdb;

		$sql = sprintf( "SELECT * FROM `%s` WHERE `listingID` = '%s'", $wpdb->prefix . "sandicor_rets", $listingID );

		$results = $wpdb->get_results( $sql, OBJECT );

		if ( count( $results ) )
			return $results[0];

		return false;
	}

	// get total number by resource
	function getTotalCountByResource( $resource ) {
		global $wpdb;

		$count = $wpdb->get_var( sprintf( "SELECT COUNT(*) FROM `%s` WHERE `resource` = '%s'", $wpdb->prefix . "sandicor_rets", $resource ) );

		return $count;
	}

	// get view types
	function getAllViewTypeList() {
		return [
			'BAY'	=> 'Bay',
			'BK'	=> 'Back Bay',
			'BLF'	=> 'Bluff',
			'BR'	=> 'Bridge',
			'CA'	=> 'Catalina',
			'CITY'	=> 'City',
			'CL'	=> 'Canal',
			'CO'	=> 'Coastline',
			'CTYL'	=> 'City Lights',
			'CY'	=> 'Courtyard',
			'DE'	=> 'Desert',
			'EVELT'	=> 'Evening Lights',
			'GLFCR'	=> 'Golf Course',
			'GRNBL'	=> 'Greenbelt',
			'HA'	=> 'Harbor',
			'LGNES'	=> 'Lagoon/Estuary',
			'LKRVR'	=> 'Lake/River',
			'LM'	=> 'Landmark',
			'MA'	=> 'Marina',
			'ME'	=> 'Meadow',
			'MTNHL'	=> 'Mountains/Hills',
			'NE'	=> 'Neighborhood',
			'NK'	=> 'N/K',
			'OCN'	=> 'Ocean',
			'ORC'	=> 'Orchard/Grove',
			'ORMKS'	=> 'Other/Remarks',
			'PANO'	=> 'Panoramic',
			'PANOC'	=> 'Panoramic Ocean',
			'PAS'	=> 'Pasture',
			'PE'	=> 'Peek-A-Boo',
			'PL'	=> 'Pool',
			'PO'	=> 'Pond',
			'PR'	=> 'Pier',
			'PRKLK'	=> 'Parklike',
			'RK'	=> 'Rocks',
			'RS'	=> 'Reservoir',
			'STM'	=> 'Creek/Stream',
			'TW'	=> 'Trees/Woods',
			'VIN'	=> 'Vineyard',
			'VLYCY'	=> 'Valley/Canyon',
			'VT'	=> 'Vincent Thomas Bridge',
			'WA'	=> 'Water',
			'WW'	=> 'White Water'
		];
	}

	// get all parking garages
	function getAllCoveredParkingList() {
		return [
			'ASGN'	=> 'Assigned',
			'ATT'	=> 'Attached',
			'COMG'	=> 'Community Garage',
			'CONV'	=> 'Converted',
			'DET'	=> 'Detached',
			'FEG'	=> 'Garage - Front Entry',
			'GA'	=> 'Direct Garage Access',
			'GAR'	=> 'Garage',
			'GATE'	=> 'Gated',
			'GDO'	=> 'Garage Door Opener',
			'GO'	=> 'Golf Cart Garage',
			'HEAT'	=> 'Heated Garage',
			'NK'	=> 'None Known',
			'REG'	=> 'Garage - Rear Entry',
			'SDG'	=> 'Garage - Single Door',
			'SEG'	=> 'Garage - Side Entry',
			'TDG'	=> 'Garage - Three Door',
			'TNDM'	=> 'Tandem',
			'TODG'	=> 'Garage - Two Door',
			'UNRG'	=> 'Underground'
		];
	}

	// get all status
	function getAllStatusList() {
		return [
			'ACT'	=> 'ACTIVE',
			'BOM'	=> 'BACK ON MARKET',
			'CANC'	=> 'CANCELLED',
			'CONT'	=> 'CONTINGENT',
			'EXP'	=> 'EXPIRED',
			'HLD'	=> 'HOLD',
			'PEND'	=> 'PENDING',
			'RNTD'	=> 'RENTED',
			'SOLD'	=> 'SOLD',
			'WITH'	=> 'WITHDRAWN'
		];
	}

	// get google api key
	function getGoogleAPIKey() {
		return $this->google_api_key;
	}

	// get properties by key word
	function getPropertiesByKeyWord( $keywords = []) {
		$all = $this->getDataFromLocalDB( [], [ 'resource' => 'property' ] );

		$matches = [];

		foreach ( $all as $item ) {
			$m_cnt = 0; //matched count

			foreach ( $keywords as $keyword ) {
				if ( empty( $keyword ) )
					continue;

				if ( strpos( strtolower( $item->addr_num ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;

				if ( strpos( strtolower( $item->addr_st ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;

				if ( strpos( strtolower( $item->addr_2 ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;

				if ( strpos( strtolower( $item->city ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;

				if ( strpos( strtolower( $item->state ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;

				if ( strpos( strtolower( $item->zip ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;
				
				if ( strpos( strtolower( $item->listingID ), strtolower( $keyword ) ) !== false )
					$m_cnt ++;
			}

			$item->m_cnt = $m_cnt;

			if ( $m_cnt )
				$matches[] = $item;
		}

		usort( $matches, function($a, $b) {
			return $a->m_cnt < $b->m_cnt;
		});

		return $matches;
	}

	// convert address to lat_lng
	function convertAddress2Lat_Lng( $address ) {
		$url = sprintf( "https://maps.googleapis.com/maps/api/geocode/json?address=%s&key=%s", str_replace( ' ', '+', $address ), $this->google_api_key );
		
		$response = wp_remote_get( $url );

		if ( !is_wp_error( $response ) ) {
			$response = json_decode( $response['body'] );

			if( $response->status == "OK" && isset( $response->results[0] ))
				return $response->results[0]->geometry->location;
			else {
				error_log( print_r( $response, true ) );
				return false;
			}
		} else {
			error_log( print_r( $response, true ) );
			return false;
		}
	}

	// get properties by city
	function getPropertiesByCity( $city = false ) {
		if ( !$city || empty( $city ) )
			return array();
		
		
		$all = $this->getDataFromLocalDB( [], [ 'resource' => 'property' ] );
		$matches = [];

		foreach ( $all as $item ) {
			if ( strpos( strtolower( $item->city ), strtolower( $city ) ) !== false )
				array_push( $matches, $item );
		}

		return $matches;
	}
}