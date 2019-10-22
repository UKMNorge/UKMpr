<?php
require_once('UKM/monstring.class.php');
throw new Exception('Kontakt UKM Norge. Funksjonen må re-integreres for å håndtere omplassering til user-admin');

$monstring = new monstring_v2( get_option('pl_id') );

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