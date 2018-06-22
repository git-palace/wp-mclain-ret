<?php
class MCRETS {
	private static $instance = null;
	private $rets = null;

	private function __construct() {
		if ( class_exists( "MCRETS_Config") ) {
			$this->rets = MCRETS_Config::setConfiguration();
		}
	}

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new MCRETS();

		return self::$instance;
	}
}