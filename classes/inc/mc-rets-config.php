<?php
class MCRETS_Config {
	public static function setConfiguration() {
		$config = new \PHRETS\Configuration;

		$config->setLoginUrl( self::getLoginURL() )
						->setUsername( self::getUsername() )
						->setPassword( self::getPassword() )
						->setRetsVersion('1.7.2');

		$rets = new \PHRETS\Session($config);
	}

	function getLoginURL() {
		$config = get_option( 'sandicore_config', false );

		return $config ? $config["login_url"] : false;
	}

	function getUsername() {
		$config = get_option( 'sandicore_config', false );

		return $config ? $config["username"] : false;
	}

	function getPassword() {
		$config = get_option( 'sandicore_config', false );

		return $config ? $config["password"] : false;
	}

	public static function saveConfig($config) {
		update_option( 'sandicore_config', $config );
	}
}