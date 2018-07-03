<?php
function custom_cron_schedule( $schedules ) {
		$schedules['every_six_hours'] = array(
				'interval' => 21600, // Every 6 hours
				'display'  => __( 'Every 6 hours' ),
		);
		return $schedules;
}
add_filter( 'cron_schedules', 'custom_cron_schedule' );

///Hook into that action that'll fire every six hours
 add_action( 'MCRETSCronJob', 'MCRETSCronJob_function' );

//create your function, that runs on cron
function MCRETSCronJob_function() {
	MCR()->populateDB();
}

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

	function getAutoSave() {
		$config = get_option( 'sandicore_config', false );
		return $config ? $config['autosave'] : "no";
	}

	public static function saveConfig($config) {
		update_option( 'sandicore_config', $config );

		if ( class_exists( "MCRETS" ) ) {
			if( $config["autosave"] == "yes" ) {
				//Schedule an action if it's not already scheduled
				$timestamp = wp_next_scheduled( 'MCRETSCronJob' );
				
				if ( $timestamp ) {
					wp_unschedule_event( $timestamp, "MCRETSCronJob" );
				}

				wp_schedule_single_event( time() + 60, "MCRETSCronJob" );
				wp_schedule_event( time(), 'every_six_hours', 'MCRETSCronJob' );
			} else {
				$timestamp = wp_next_scheduled( 'MCRETSCronJob' );
				
				if ( $timestamp ) {
					wp_unschedule_event( $timestamp, "MCRETSCronJob" );
				}
			}
		}
	}
}