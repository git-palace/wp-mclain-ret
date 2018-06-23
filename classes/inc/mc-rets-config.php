<?php
class MCRETS_Config {
	public static function setConfiguration() {
		$config = new \PHRETS\Configuration;

		$login_url = self::getLoginURL();
		$username = self::getUsername();
		$password = self::getPassword();

		if ( !$login_url || !$username || !$password ) {
			return null;
		}

		$config->setLoginUrl( $login_url )
						->setUsername( $username )
						->setPassword( $password )
						->setRetsVersion( '1.7.2' )
						->setHttpAuthenticationMethod( 'basic' );

		$rets = new \PHRETS\Session($config);

		return $rets;
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