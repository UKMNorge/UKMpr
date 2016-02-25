<?php
require_once('UKM/monstring.class.php');

$pl = new monstring( get_option('pl_id') );
$fylke = $pl->get('fylke_id');

$aviser = new aviser();
$aviser->getAllByFylke( $fylke );

$kommuner = $pl->get('kommuner');
$aviser->unrelateAll( $kommuner );

foreach( $_POST as $kommune => $avis_array ) {
	if( strpos( $kommune, 'kommune_') !== 0 ) {
		continue;
	}
	
	$kommune_id = str_replace('kommune_', '', $kommune);
	$aviser->unrelateAllForKommune( $kommune_id );
	foreach( $avis_array as $avis ) {
		$aviser->relate( $avis, $kommune_id );
	}
}


$VIEW = 'dashboard';
require_once('dashboard.controller.php');