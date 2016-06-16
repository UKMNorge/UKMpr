<?php
	
require_once('UKM/monstring.class.php');
require_once('UKM/fylker.class.php');

$TWIGdata['site_type'] = get_option('site_type');

$TWIGdata['monstring'] = new monstring_v2( get_option('pl_id') );

$aviser = new aviser();
$TWIGdata['aviser'] = array();
if( 'land' == get_option('site_type') ) {
	$fylker = fylker::getAll();
	foreach( $fylker as $fylke ) {
		$aviser->reset();
		$fylke_aviser = $aviser->getAllByFylke( $fylke->getId() );
		if( is_array( $fylke_aviser ) ) {
			$TWIGdata['aviser'] = array_merge( $TWIGdata['aviser'], $fylke_aviser );
		}
	}
} elseif( 'fylke' == get_option('site_type') ) {
	$TWIGdata['aviser'] = $aviser->getAllByFylke( $TWIGdata['monstring']->getFylke()->getId() );
}