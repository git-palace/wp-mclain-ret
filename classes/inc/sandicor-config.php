<?php
class SandicorConfig {
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

		return ["rets" => $rets, "brelicense" => self::getBRELicense()];
	}

	function getLoginURL() {
		$config = get_option( 'sandicor_config', false );

		return $config ? $config["login_url"] : false;
	}

	function getUsername() {
		$config = get_option( 'sandicor_config', false );

		return $config ? $config["username"] : false;
	}

	function getPassword() {
		$config = get_option( 'sandicor_config', false );

		return $config ? $config["password"] : false;
	}

	function getBRELicense() {
		$config = get_option( 'sandicor_config', false );

		return $config ? $config["brelicense"] : false;		
	}

	function getAutoSave() {
		$config = get_option( 'sandicor_config', false );
		return $config ? $config['autosave'] : "no";
	}

	public static function saveConfig($config) {
		update_option( 'sandicor_config', $config );

		if ( class_exists( "Sandicor" ) ) {
			//Schedule an action if it's not already scheduled
			$timestamp = wp_next_scheduled( 'sandicor_cronjob' );
			
			while ( $timestamp ) {
				wp_unschedule_event( $timestamp, "sandicor_cronjob" );
				
				$timestamp = wp_next_scheduled( 'sandicor_cronjob' );
			}

			if( $config["autosave"] == "yes" ) {
				wp_schedule_single_event( time() + 60, "sandicor_cronjob" );
				wp_schedule_event( time(), 'every_six_hours', 'sandicor_cronjob' );
			}
		}

		return true;
	}
}