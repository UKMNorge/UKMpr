<?php

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Database\SQL\Insert;

require_once('UKM/Autoloader.php');


$monstring = new Arrangement( get_option('pl_id') );

$SQL = new Insert('ukm_avis');
$SQL->add('name', $_POST['name']);
$SQL->add('url', $_POST['url']);
$SQL->add('email', $_POST['email']);
$SQL->add('fylke', $monstring->getFylke()->getId());
$SQL->add('type', $_POST['type']);

try {
	$id = $SQL->run();
	
	foreach( $_POST['kommuner'] as $kommune_id ) {
		$sql_rel = new Insert('ukm_avis_nedslagsfelt');
		$sql_rel->add('kommune_id', $kommune_id );
		$sql_rel->add('avis_id', $id );
		$sql_rel->run();
	}

	UKMpr::getFlash()->add('success',$_POST['name'] .' lagt til');
} catch( Exception $e ) {
	UKMpr::getFlash()->add(
		'danger',
		'Kunne ikke opprette avisen '. $_POST['name'].
		'. Systemet ga følgende feilmelding: '. $SQL->error()
	);
}

require_once(UKMpr::getPluginPath() .'controller/lokalaviser/list.controller.php');
UKMpr::setAction('lokalaviser/list');