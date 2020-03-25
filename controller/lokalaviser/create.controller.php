<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

throw new Exception('Kontakt UKM Norge. Funksjonen må re-integreres for å håndtere omplassering til user-admin');

$monstring = new Arrangement(intval( get_option('pl_id') ));

if( get_option('site_type') == 'fylke') {
	UKMpr::addViewData(
		'kommuner',
		$monstring->getFylke()->getKommuner()->getAll()
	);
} else {
	UKMpr::addViewData(
		'kommuner',
		$monstring->getKommuner()->getAll()
	);
}
UKMpr::addViewData(
	'monstring',
	$monstring
);
?>