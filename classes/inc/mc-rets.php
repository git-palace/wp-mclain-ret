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

		if ( $this->login() ) {
			$this->populateProperties();
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
	function populateProperties() {
		//RE_1, RI_2, LN_3, RT_4, RN_6, OF_9, LR_11 (Property)
		//RE_1, RI_2 (Openhouse)
		// $results = $this->rets->Search( 'Property', 'RE_1', '(L_City=San Diego)', ['Limit' => 3]);
		/*echo "<pre>";
		print_r( $results );
		echo "</pre>";*/
	}

	function populateDB() {
		global $wpdb;
		$table_name = $wpdb->prefix."mc_rets";

		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $table_name (
					ID bigint(20) NOT NULL AUTO_INCREMENT,
					listing_ID bigint(10) NOT NULL, 
					addr_num varchar(15) NOT NULL DEFAULT '',
					addr_st varchar(50) NOT NULL DEFAULT '',
					addr_2 varchar(50) NOT NULL DEFAULT '',
					city varchar(50) NOT NULL DEFAULT '',
					state varchar(2) NOT NULL DEFAULT '',
					zip varchar(20) NOT NULL DEFAULT '',
					low_price bigint(10) NOT NULL DEFAULT 0,
					supplement text NOT NULL DEFAULT '',
					listing_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					sr_type varchar(1) NOT NULL DEFAULT 'S',
					v_type varchar(10) NOT NULL,
					c_parking bigint(10) NOT NULL,
					beds_num bigint(3) NOT NULL DEFAULT 0,
					baths_num bigint(5) NOT NULL DEFAULT 0,
					photo_count bigint(10) NOT NULL DEFAULT 0,
					year_built bigint(5) NOT NULL,
					inter_sqft bigint(10) NOT NULL DEFAULT 0,
					lotsize_sqft bigint(10) NOT NULL DEFAULT 0,
					parking_total bigint(10) NOT NULL DEFAULT 0,
					inclusion varchar(10) NOT NULL DEFAULT '',
					PRIMARY KEY  (ID)
				) $charset_collate;
			";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}
}