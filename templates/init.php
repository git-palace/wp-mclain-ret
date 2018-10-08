<?php
require_once( 'search-form.php' );

// search form 
add_shortcode( 'search-form', function( $atts ) {
	$atts = shortcode_atts(
		array( 'type' => 'simple' ),
		$atts
	);

	if ( in_array( $atts['type'], array( "simple" ) ) )
		call_user_func( $atts['type']."_search_form" );	
});

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'slick', plugins_url( 'assets/slick/slick.css', __FILE__ ) );
	wp_enqueue_style( 'slick-theme', plugins_url( 'assets/slick/slick-theme.css', __FILE__ ) );

	wp_enqueue_script( 'slick', plugins_url( 'assets/slick/slick.min.js', __FILE__ ), array( 'jquery' ), '1.8.1', true );

	wp_enqueue_style( 'style', plugins_url( 'assets/css/style.css', __FILE__ ) );

	wp_enqueue_script( 'scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array( 'slick' ), '1.0.0', true );

	wp_localize_script( 'scripts', 'ajax_obj', array( 'url' => admin_url( 'admin-ajax.php' ) ) );

	wp_enqueue_script( 'global-scripts', plugins_url( 'assets/js/global.js', __FILE__ ), array( 'jquery' ), '1.0.0' );
});

// add single property page in front end
add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ) {
	$wp_rewrite->rules = array_merge(
		[
			'single-property/(\d+)/?$' => 'index.php?spType=single&listingID=$matches[1]',
			'search-results/([a-zA-Z0-9 ,_]+)/?$' => 'index.php?spType=search&keyword=$matches[1]',
			'sandicor-dashboard' => 'index.php?spType=dashboard'
		],
		$wp_rewrite->rules
	);

	return $wp_rewrite->rules;
} );

// add queries to the url
add_filter( 'query_vars', function( $query_vars ) {
	array_push( $query_vars, 'listingID', 'keyword', 'spType');

	return $query_vars;
} );

// template redirect hook
add_action( 'template_redirect', function() {
	$spType = get_query_var('spType');

	if ($spType) {
		get_header();

		switch ( $spType ) {
			case 'single':
				$listingID = intval( get_query_var( 'listingID' ) );

				$property = SI()->getDataBylistingID( $listingID );

				if ( $property)
					include "single-property.php";
				else
					echo "<h1>Invalid Listing ID</h1>";
				break;
			
			case 'search':
				$ip = $_SERVER['REMOTE_ADDR'];

				$s_criteria_arr = get_transient( $ip . '_searched_criteria' );

				if( !is_array( $s_criteria_arr ) || empty( $s_criteria_arr ) )
					$s_criteria_arr = array();

				if ( count( $s_criteria_arr ) >= 3 && !is_user_logged_in()) {
					_e( '<div class="container sandicor-signup">' );
						_e( "<h1 class='text-center' style='margin: 5em 0 2em'>Please register to search more.</h1>" );

						_e( '<div class="col-md-6 col-md-offset-3" style="margin-bottom: 3em;">' );
							_e( do_shortcode('[gravityform id="8" title="false" description="false" ajax="true"]') );
						_e( '</div>' );
					_e( '</div>' );
				} else {

					$keywords = explode( ' ', str_replace( ',', ' ', get_query_var('keyword') ) );

					$properties = SI()->getPropertiesByKeyWord( $keywords );

					if ( !is_user_logged_in() ) {
						array_push( $s_criteria_arr, $keywords );

						set_transient( $ip . '_searched_criteria', $s_criteria_arr, 86400 );
					}

					if ( count( $properties ) )
						include "search-results.php";
					else
						echo "<h1 class='text-center' style='margin: 5em 0;'>No Results</h1>";
				}

				break;

			case 'dashboard':
				if ( is_user_logged_in() )
					include 'user-dashboard.php';
				else {
					wp_redirect( home_url() );
					wp_die();
				}


				break;
		}

		get_footer();

		die;
	}
} );

// add template to page editor page
add_filter( 'theme_page_templates', function( $templates ) {

	$templates['properties-by-location.php'] = 'Properties by Location';

	return $templates;
} );

add_filter( 'page_template', function( $template ) {
	if ( get_page_template_slug() == 'properties-by-location.php' )
		$template = SR_PLUGIN_PATH . 'templates/properties-by-location.php';

	return $template;
} );


// save, remove search criteria from search result page
add_action( 'wp_ajax_save_search_criteria', function() {
	$criteria = $_POST['criteria'];

	if ( isset( $criteria ) && !empty( $criteria ) ) {
		$sandicor_criterias = get_user_meta( get_current_user_id(), 'sandicor_criterias', true );

		if ( !isset( $sandicor_criterias ) || empty( $sandicor_criterias ) )
			$sandicor_criterias = array();

		if ( in_array( $criteria, $sandicor_criterias ) ) {
			foreach ( $sandicor_criterias as $key => $s_criteria ) {
				if ( $s_criteria['keyword'] == $criteria ) {
					unset( $sandicor_criterias[$key] );
					break;
				}
			}
		} else {

			$keywords = explode( ' ', str_replace( ',', ' ', $criteria ) );
			$properties = SI()->getPropertiesByKeyWord( $keywords );

			$m_properties = array();

			foreach ( $properties as $key => $property ) {
				array_push( $m_properties, $property->listingID );
			}
			
			array_push( $sandicor_criterias, array( 
				'keyword' => $criteria,
				'properties' => $m_properties
			) );
		}

		update_user_meta( get_current_user_id(), 'sandicor_criterias', $sandicor_criterias );
	}
	
	wp_die();
} );

// delete keyword from user dashboard
add_action( 'wp_ajax_delete_key_word', function() {
	if ( isset( $_POST['criteria'] ) && !empty( $_POST['criteria'] ) ) {
		$sandicor_criterias = get_user_meta( get_current_user_id(), 'sandicor_criterias', true );

		foreach ( $sandicor_criterias as $key => $s_criteria ) {
			if ( $s_criteria['keyword'] == $_POST['criteria'] ) {
				unset( $sandicor_criterias[$key] );
				break;
			}
		}

		update_user_meta( get_current_user_id(), 'sandicor_criterias', $sandicor_criterias );
		$sandicor_criterias = get_user_meta( get_current_user_id(), 'sandicor_criterias', true );

		?>

		<?php 
		if ( isset( $sandicor_criterias ) && !empty( $sandicor_criterias ) ) : $idx = 0; $html = '';
		
			foreach ( $sandicor_criterias as $criteria ):
				$html .= '<tr><td><b>' . ( $idx +1 ) . '</b></td><td>' . $criteria['keyword'] . '</td><td><a class="delete" href="javascript:void(0)" k-index="' . esc_attr( $idx ) . '" keyword="' . esc_attr( $criteria['keyword'] ) . '">Delete</a> | <a class="" href="/search-results/' . esc_attr( $criteria['keyword'] ) . '" k-index="' . esc_attr( $idx ) . '" keyword="' . esc_attr( $criteria['keyword'] ) . '">Visit</a></td></tr>';
			endforeach;
			
			echo json_encode( array( 'failed' => false, 'html' => $html ) );
		else:
			echo json_encode( array( 'failed' => true ) );
		endif;

		wp_die();
	}
} );


add_action( 'wp_ajax_change_password', function() {
	$user = wp_get_current_user();

	if ( isset( $_POST['oldPWD'], $_POST['newPWD'] ) ) {
		if ( $user && wp_check_password( $_POST['oldPWD'], $user->data->user_pass, $user->ID ) ) {
		    wp_set_password( $_POST['newPWD'], $user->ID );
		    
		    $creds = array(
		        'user_login'    => $user->user_email,
		        'user_password' => $_POST['newPWD']
		    );
		 
		    $user = wp_signon( $creds, false );

		    echo json_encode( array( 'failed' => false ) );
		} else {
		    echo json_encode( array( 'failed' => true, 'msg' => 'Old password is incorrect!') );
		}
	}

	wp_die();
} );