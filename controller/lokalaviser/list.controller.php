<?php

require_once('UKM/monstring.class.php');
require_once('UKM/aviser.class.php');

$monstring = new monstring_v2( get_option('pl_id') );
$aviser = new aviser();

UKMpr::addViewData(
	'monstring',
	new monstring_v2( get_option('pl_id') )
);
UKMpr::addViewData(
	'aviser',
	$aviser->getAllByFylke( $monstring->getFylke()->getId() )
);
UKMpr::addViewData(
	'site_type',
	get_option('site_type')
);