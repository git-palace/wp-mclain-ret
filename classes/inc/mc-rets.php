<?php
class MCRETS {
	private static $instance = null;
	private $rets = null;
	private $properties;
	private $openhouses;

	// constructor
	private function __construct() {
		if ( class_exists( "MCRETS_Config") ) {
			$this->rets = MCRETS_Config::setConfiguration();
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

	// populate properties
	function getProperties( $filter = 'Property', $class = 'RE_1', $perPage = 10, $offset = 0) {
		$results = $this->rets->Search( $filter, $class, '(L_City=San Diego)', ['Limit' => $perPage, 'Offset' => $offset]);
		return $results;
	}

	function populateDB() {
		$populatingDB = get_option( "populatingDB", false );

		if ( $populatingDB == "yes" )
			return;

		update_option( "populatingDB", "yes" );
		ini_set('max_execution_time', 0);

		global $wpdb;
		$table_name = $wpdb->prefix."mc_rets";

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
					year_built varchar(5) NOT NULL,
					inter_sqft varchar(10) NOT NULL DEFAULT 0,
					lotsize_sqft varchar(10) NOT NULL DEFAULT 0,
					parking_total varchar(10) NOT NULL DEFAULT 0,
					status varchar(20) NOT NULL DEFAULT '',
					domls varchar(10) NOT NULL DEFAULT '',
					inclusion varchar(10) NOT NULL DEFAULT '',
					PRIMARY KEY  (ID),
					UNIQUE KEY `listing_ID` (`listing_ID`)
				) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}

		$filters = array(
			'property' => array(
				'resource' => 'Property',
				'classes' => ['RE_1', 'RI_2', 'LN_3', 'RT_4', 'RN_6', 'OF_9', 'LR_11']
			)/*,
			'open_house' => array(
				'resource' => 'OpenHouse',
				'classes' => ['RE_1', 'RI_2']
			)*/
		);

		foreach ( $filters as $type => $filter ) {
			foreach ( $filter['classes'] as $class ) {
				$perPage = 30;
				$offset = 0;

				while ( true ) {
					$results = $this->getProperties( $filter['resource'], $class, $perPage, $offset );
					$offset += 1;

					if ( !count( $results ) )
						break;

					foreach ( $results as $result ) {
						if ( $type == 'property' )
							$this->addPropertyToDB( $result );
						// else
							// $this->addOpenHouseToDB( $result );
					}
				}
			}
		}
		
		delete_option( "populatingDB" );
	}

	function addPropertyToDB( $property ) {
		global $wpdb;
		$table_name = $wpdb->prefix."mc_rets";

		$count = $wpdb->get_var( sprintf( "SELECT COUNT(*) FROM `$table_name` WHERE `listing_ID` = '%s'", $property['L_ListingID'] ) );

		if ( $count ) {
			$wpdb->query( sprintf( "DELETE * FROM `$table_name` WHERE `listing_ID` = '%s'", $property['listing_ID'] ) );
		}

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
			'inclusion'			=> ''
		);

		$wpdb->insert( $table_name, $data );
	}
}