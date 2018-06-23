<?php
class MCRETS {
	private static $instance = null;
	private $rets = null;

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
	function Login() {
		try {
			$this->rets->Login();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
	}

	// get all open houses
	function getAllOpenHouses() {
		if ( !$this->Login() )
			return array();

		echo "<pre>";

		// $classes = $this->rets->GetClassesMetadata( 'OpenHouse' );
		$search = $this->rets->Search( 'Property', 'RT_4', '(PostalCode=92008)', ['Limit' => 3] );

		echo "</pre>";
	}
}