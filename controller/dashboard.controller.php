<?php

require_once('UKM/monstring.class.php');

$pl = new monstring( get_option('pl_id') );
$aviser = new aviser();

$TWIGdata['aviser'] = $aviser->getAllByFylke( $pl->get('fylke_id') );
$TWIGdata['site_type'] = get_option('site_type');

if( get_option('site_type') == 'fylke') {
	$kommuner = $pl->get('kommuner_i_fylket');
	foreach( $kommuner as $id => $name ) {
		$data = new stdClass();
		$data->id = $id;
		$data->name = $name;
		$TWIGdata['kommuner'][] = $data;
	}
} else {
	$TWIGdata['kommuner'] = $pl->get('kommuner');
}
?>