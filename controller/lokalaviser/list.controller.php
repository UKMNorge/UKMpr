<?php

use UKMNorge\Arrangement\Arrangement;

require_once('UKM/Autoloader.php');

require_once('UKM/monstring.class.php');
require_once('UKM/aviser.class.php');

$monstring = new Arrangement(intval( get_option('pl_id') ));
$aviser = new aviser();

UKMpr::addViewData(
	'monstring',
	new Arrangement(intval( get_option('pl_id') ))
);
UKMpr::addViewData(
	'aviser',
	$aviser->getAllByFylke( $monstring->getFylke()->getId() )
);