<?php
class MCRETS {
	private static $instance = null;
	private $rets = null;
	private $properties;
	private $openhouses;
	private $brelicense;

	// constructor
	private function __construct() {
		if ( class_exists( "MCRETS_Config") ) {
			$config = MCRETS_Config::setConfiguration();
			$this->rets = $config["rets"];
			$this->brelicense = $config["brelicense"];
		}
	}

	// get instance for singleton design pattern
	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new MCRETS();

		return self::$instance;
	}

	// login to sandicore
	function login() {
		try {
			$this->rets->Login();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	// get Resource Classes
	function getResourceClasses( $type = "property" ) {
		switch ( $type ) {
			case 'property':
				return ['RE_1', 'RI_2', 'LN_3', 'RT_4', 'RN_6', 'OF_9', 'LR_11'];

			case 'open_house':
				return ['RE_1', 'RI_2'];
		}

		return [];
	}

	// add bre license to dmql
	function getDMQL( $args = []) {
		$args = array_merge( $args, ['LM_char10_48' => $this->brelicense] );

		$query = '(';
		foreach ( $args as $key => $value )
			$query .= sprintf('%s=%s', $key, $value) . ( $value == end( $args ) ? '' : '),(' );
		$query .= ')';

		return $query;
	}

	function test() {
		$dmql = $this->getDMQL(['L_City' => 'San Diego']);
		return $this->rets->Search( 'Property', 'RE_1', $dmql );

	}

	// populate properties
	function getDataFromSandicore( $filter = 'Property', $class = 'RE_1' ) {
		$dmql = $this->getDMQL([]);
		$results = $this->rets->Search( $filter, $class, $dmql );
		return $results;
	}

	// auto populate database
	function populateDB() {
		if ( !$this->login() )
			return "failed to login";

		ini_set('max_execution_time', 0);

		global $wpdb;
		$table_name = $wpdb->prefix . "mc_rets";

		// create table if it's not exisitng
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					listing_ID varchar(10) NOT NULL, 
					type varchar(20) NOT NULL DEFAULT 'property',
					area varchar(20) NOT NULL DEFAULT '',
					addr_num varchar(15) NOT NULL DEFAULT '',
					addr_st varchar(50) NOT NULL DEFAULT '',
					addr_2 varchar(50) NOT NULL DEFAULT '',
					city varchar(50) NOT NULL DEFAULT '',
					state varchar(2) NOT NULL DEFAULT '',
					zip varchar(20) NOT NULL DEFAULT '',
					low_price varchar(10) NOT NULL DEFAULT 0,
					supplement text NOT NULL DEFAULT '',
					listing_date varchar(20) NOT NULL DEFAULT '',
					sr_type varchar(20) NOT NULL DEFAULT '',
					v_type varchar(20) NOT NULL,
					c_parking varchar(10) NOT NULL,
					beds_num varchar(3) NOT NULL DEFAULT 0,
					baths_num varchar(5) NOT NULL DEFAULT 0,
					county  varchar(10) NOT NULL DEFAULT '',
					photo_count varchar(10) NOT NULL DEFAULT 0,
					year_built varchar(20) NOT NULL,
					inter_sqft varchar(10) NOT NULL DEFAULT 0,
					lotsize_sqft varchar(10) NOT NULL DEFAULT 0,
					parking_total varchar(10) NOT NULL DEFAULT 0,
					status varchar(20) NOT NULL DEFAULT '',
					domls varchar(10) NOT NULL DEFAULT '',
					inclusion varchar(10) NOT NULL DEFAULT '',
					created_at varchar(20) NOT NULL DEFAULT '',
					PRIMARY KEY  (ID),
					UNIQUE KEY `listing_ID` (`listing_ID`)
				) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			delete_transient( "populating-db" );
		}

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
			)/*,
			'open_house' => array(
				'resource' => 'OpenHouse',
				'classes' => $this->getResourceClasses( "open_house" )
			)*/
		);

		foreach ( $filters as $type => $filter ) {
			foreach ( $filter['classes'] as $class ) {
				$results = $this->getDataFromSandicore( $filter['resource'], $class );

				if ( !count( $results ) )
					break;

				foreach ( $results as $result ) {
					if ( !in_array( $result['L_ListingID'], $listingIDs ) )
						array_push( $listingIDs, $result['L_ListingID'] );

					if ( $type == 'property' )
						$this->addPropertyToDB( $result );
					// else
						// $this->addOpenHouseToDB( $result );
				}
			}
		}
		
		delete_transient( "populating-db" );
		return $listingIDs;
	}

	// add to database
	function addPropertyToDB( $property ) {
		global $wpdb;
		$table_name = $wpdb->prefix."mc_rets";

		$data = array(
			'listing_ID' 		=> $property['L_ListingID'],
			'type'					=> 'property',
			'area'					=> $property['L_Area'],
			'addr_num'			=> $property['L_AddressNumber'],
			'addr_st'				=> $property['L_AddressStreet'],
			'addr_2'				=> $property['L_Address2'],
			'city'					=> $property['L_City'],
			'state'					=> $property['L_State'],
			'zip'						=> $property['L_Zip'],
			'low_price'			=> $property['L_asking_price_low'],
			'supplement'		=> $property['LR_remarks66'],
			'listing_date'	=> $property['L_ListingDate'],
			'sr_type'				=> $property['L_SaleRent'],
			'v_type'				=> $property['LFD_View_44'],
			'c_parking'			=> $property['LFD_ParkingGarage_22'],
			'beds_num'			=> $property['LM_Int1_3'],
			'baths_num'			=> $property['LM_Int2_6'],
			'county'				=> $property['LM_Char10_1'],
			'photo_count'		=> $property['L_PictureCount'],
			'year_built'		=> $property['LM_Int2_1'],
			'inter_sqft'		=> $property['LM_Int4_1'],
			'lotsize_sqft'	=> $property['LM_Int4_6'],
			'parking_total'	=> $property['LM_Int4_8'],
			'status'				=> $property['L_Status'],
			'domls'					=> $property['L_DOMLS'],
			'inclusion'			=> '',
			'created_at'		=> date('Y-m-d H:i:s')
		);

		$count = $wpdb->get_var( sprintf( "SELECT COUNT(*) FROM `%s` WHERE `listing_ID` = '%s'", $table_name, $property['L_ListingID'] ) );

		if ( $count )
			$wpdb->update( $table_name, $data, array( 'listing_ID' => $property['listing_ID'] ) );
		else
			$wpdb->insert( $table_name, $data );
	}

	// get table headers
	function getVisibleHeaders( $excludes = [] ) {
		$headers = array(
			'listing_ID' 		=> 'Listing ID',
			'type'					=> 'Type',
			'area'					=> 'Area ',
			'address'				=> 'Address',
			'county'				=> 'County',
			'low_price'			=> 'Low Price',
			'supplement'		=> 'Supplement',
			'sr_type'				=> 'For Sale / Rent',
			'v_type'				=> 'View Type',
			'c_parking'			=> 'Parking Garage',
			'beds_num'			=> 'Bedrooms',
			'baths_num'			=> 'Bathrooms',
			'photo_count'		=> 'Photos Count',
			'year_built'		=> 'Year Built',
			'inter_sqft'		=> 'Interior Sqft',
			'lotsize_sqft'	=> 'Lot size Sqft',
			'parking_total'	=> 'Parking Total',
			'listing_date'	=> 'Listing Date',
			'status'				=> 'Status',
			'domls'					=> 'Days on Market',
			'inclusion'			=> 'Inclusions'
		);

		
		return array_diff( $headers, $excludes );
	}

	// get Data
	function getDataFromLocal( $limits = ['perPage' => 10, 'pageIdx' => 1], $where = [] ) {
		global $wpdb;
		
		$table_name = $wpdb->prefix."mc_rets";
		$sql = sprintf( "SELECT * FROM `%s` WHERE", $table_name );

		if ( !count( $where ) ) {
			$sql .= " 1";
		}

		foreach ( $where as $field => $value ) {
			if ( end( $where ) == $value )
				$sql .= sprintf( " `%s` = '%s'", $field, $value );
			else
				$sql .= sprintf( " `%s` = '%s' AND", $field, $value );
		}

		if (
			isset( $limits['perPage'] ) && !empty( $limits['perPage'] ) &&
			isset( $limits['pageIdx'] ) && !empty( $limits['pageIdx'] )
		) {
			$sql .= sprintf( " LIMIT %d, %d", ( intval( $limits['pageIdx'] ) - 1 ) * $limits['perPage'], $limits['perPage'] );
		}

		return $wpdb->get_results( $sql, OBJECT );
	}

	// get total number by type
	function getTotalNumberByType( $type ) {
		global $wpdb;
		$table_name = $wpdb->prefix."mc_rets";

		$count = $wpdb->get_var( sprintf( "SELECT COUNT(*) FROM `%s` WHERE `type` = '%s'", $table_name, $type ) );

		return $count;
	}
}