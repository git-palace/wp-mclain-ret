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
		//, RI_2, LN_3, RT_4, RN_6, OF_9, LR_11
		$results = $this->rets->Search( 'Property', 'RE_1', '(L_City=San Diego)', ['Limit' => 3]);
		/*echo "<pre>";
		print_r( $results );
		echo "</pre>";*/
	}
}