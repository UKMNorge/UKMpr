<?php
require_once('UKM/monstring.class.php');

$pl = new monstring( get_option('pl_id') );
$fylke = $pl->get('fylke_id');

$SQL = new SQLins('ukm_avis');
$SQL->add('name', $_POST['name']);
$SQL->add('url', $_POST['url']);
$SQL->add('email', $_POST['email']);
$SQL->add('fylke', $fylke);
$SQL->add('type', $_POST['type']);

$res = $SQL->run();
$id = $SQL->insId();

if( $id > 0 ) {
	foreach( $_POST['kommuner'] as $kommune_id ) {
		$sql_rel = new SQLins('ukm_avis_nedslagsfelt');
		$sql_rel->add('kommune_id', $kommune_id );
		$sql_rel->add('avis_id', $id );
		$sql_rel->run();
	}
}

if( $res === true || $res == 1 ) {
	$TWIGdata['flashbag'] = array('status'=>'success', 'message'=> $_POST['name'] .' lagt til');
} else {
	$TWIGdata['flashbag'] = array('status'=>'danger', 'message'=>'Kunne ikke opprette avisen '. $_POST['name'].'. Systemet ga følgende feilmelding: '. $SQL->error() );
}

$VIEW = 'dashboard';
require_once('dashboard.controller.php');