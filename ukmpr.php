<?php  
/* 
Plugin Name: UKMpr
Plugin URI: http://www.ukm-norge.no
Description: Lokalaviser og nedslagsfelt, kontaktinfo for artiker
Author: UKM Norge / M Mandal 
Version: 1.0
Author URI: http://www.ukm-norge.no
*/

if(is_admin()) {
	if( get_option('site_type') != false ) {
		add_action('UKM_admin_menu', 'UKMpr_menu');
		add_filter('UKMWPDASH_messages', 'UKMpr_dash_messages');
	}
}

// Regular menu
function UKMpr_menu() {
	UKM_add_menu_page('ressurser','Markedsføring', 'Markedsføring', 'editor', 'UKMmarketing','UKMmarketing', '//ico.ukm.no/megaphone-menu.png', 11);
	if(get_option('site_type') != 'kommune')
		UKM_add_submenu_page('UKMmarketing','Pressemelding', 'Pressemelding', 'editor', 'UKMpr_melding','UKMpr_melding');#, 'http://ico.ukm.no/megaphone-menu.png', 11);
	UKM_add_submenu_page('UKMmarketing','Lokalaviser', 'Lokalaviser', 'editor', 'UKMpr','UKMpr');#, '//ico.ukm.no/contact-menu.png', 11);
	UKM_add_scripts_and_styles('UKMmarketing', 'UKMpr_scripts_and_styles' );
	
	if(get_option('site_type') != 'kommune')
		UKM_add_submenu_page('UKMmarketing', 'E-postadresser', 'E-postadresser', 'editor', 'UKMpr_adresser', 'UKMpr_adresser');
	
	UKM_add_scripts_and_styles('UKMpr', 'UKMpr_scripts_and_styles' );
	UKM_add_scripts_and_styles('UKMpr_melding', 'UKMpr_scripts_and_styles' );
}

function UKMpr_scripts_and_styles(){
	wp_enqueue_script('WPbootstrap3_js');
	wp_enqueue_style('WPbootstrap3_css');
}

function UKMmarketing() {
	$TWIG = array();
	require_once('controller/layout.controller.php');

	$VIEW = isset( $_GET['action'] ) ? $_GET['action'] : 'oversikt';
	$TWIG['tab_active'] = $VIEW;
	require_once(__DIR__.'/controller/'. $VIEW .'.controller.php');
	
	echo TWIG($VIEW .'.html.twig', $TWIG, dirname(__FILE__), true);
	echo TWIGjs( dirname(__FILE__) );
}

function UKMpr_melding() {
	if( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
		$TWIG['saved'] = update_option('pressemelding', $_POST['pressemelding_editor'] );
	}
	$TWIGdata = array('UKM_HOSTNAME' => UKM_HOSTNAME);
	echo TWIG('pressemelding_pre_editor.html.twig', $TWIGdata, dirname(__FILE__) );
	wp_editor( stripslashes(get_option('pressemelding')), 'pressemelding_editor', $settings = array() );
	echo TWIG('pressemelding_post_editor.html.twig', $TWIGdata, dirname(__FILE__) );
}

function UKMpr_adresser() {
	# TODO: MOVE TO API
	require_once('UKM/aviser.class.php');

	$TWIGdata = array();
	require_once('controller/adresser.controller.php');
	

	echo TWIG('adresser.twig.html', $TWIGdata, dirname(__FILE__));
	
}

function UKMpr() {
	# TODO: MOVE TO API
	require_once(plugin_dir_path(__FILE__).'aviser.class.php');

	$TWIGdata = array();
	$VIEW = isset( $_GET['action'] ) ? $_GET['action'] : 'dashboard';
	require_once('controller/'. $VIEW .'.controller.php');
	

	echo TWIG($VIEW. '.twig.html', $TWIGdata, dirname(__FILE__));
}



function UKMpr_dash_messages( $messages ) {
	# TODO: MOVE TO API
	require_once(plugin_dir_path(__FILE__).'aviser.class.php');
	$aviser = new aviser();
	$pl = new monstring( get_option('pl_id') );
	$kommuner = $pl->get('kommuner');
	
	if( get_option('site_type') == 'kommune' ) {
		foreach( $kommuner as $kommune ) {
			if( !$aviser->hasRelation( $kommune['id'] ) ) {
				$messages[] = array('level' 	=> 'alert-error',
									'link' => 'admin.php?page=UKMpr',
									'header' 	=> 'Info om lokalaviser mangler!',
									'body' 	=> 'Velg "Lokalaviser" i menyen til venstre'
									);
				break;
			}
		}
	}
	elseif( get_option('site_type') == 'fylke') {
		$kommuner = $pl->get('kommuner_i_fylket');
		$count = 0;
		foreach( $kommuner as $id => $name ) {
			if( !$aviser->hasRelation( $id ) ) {
				$count++;
			}
		}
		if( $count > 0 ) {
			$percent_missing = (100 / sizeof( $kommuner ) ) * $count;
			$messages[] = array('level' 	=> 'alert-'. ($percent_missing > 15 ? 'error' : 'warning'),
								'link' 		=> 'admin.php?page=UKMpr',
								'header' 	=> $count . ' '. ( $pl->get('fylke_id') == 3 ? 'bydeler' : 'kommuner' ) .' mangler info om lokalaviser!',
								'body' 		=> 'Velg "Lokalaviser" i menyen til venstre'
								);
		}
	}

	return $messages;
}