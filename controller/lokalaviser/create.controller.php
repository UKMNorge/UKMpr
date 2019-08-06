<?php
require_once('UKM/monstring.class.php');

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