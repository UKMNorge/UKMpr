<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');


require_once('UKM/monstring.class.php');
require_once('UKM/aviser.class.php');

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    throw new Exception('Kontakt UKM Norge. Funksjonen må re-integreres for å håndtere omplassering til user-admin');
	$monstring = new Arrangement(intval( get_option('pl_id') ));

	$aviser = new aviser();
	$aviser->getAllByFylke( $monstring->getFylke()->getId() );

	if( get_option('site_type') == 'kommune' ) {
		$aviser->unrelateAll( $monstring->getKommuner()->getAll() );
	}

	foreach( $_POST as $kommune => $avis_array ) {
		if( strpos( $kommune, 'kommune_') !== 0 ) {
			continue;
		}
		
		$kommune_id = str_replace('kommune_', '', $kommune);
		if( get_option('site_type') == 'fylke' ) {
			$aviser->unrelateAllForKommune( $kommune_id );
		}
		foreach( $avis_array as $avis ) {
			$aviser->relate( $avis, $kommune_id );
		}
	}

	UKMpr::getFlash()->add('success','Relasjoner er nå oppdatert!');
}
require_once(UKMpr::getPluginPath() .'controller/lokalaviser/list.controller.php');
UKMpr::setAction('lokalaviser/list');