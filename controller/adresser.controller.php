<?php
	
require_once('UKM/monstring.class.php');

$TWIGdata['aviser'] = new aviser();
$TWIGdata['monstring'] = new monstring_v2( get_option('pl_id') );
$TWIGdata['fylke'] = $TWIGdata['monstring']->getFylke()->getId();