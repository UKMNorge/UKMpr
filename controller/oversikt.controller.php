<?php

if(get_option('site_type') == 'kommune' || get_option('site_type') == 'fylke') {
	$monstring = new monstring_v2(get_option('pl_id'));
	$TWIG['monstring'] = $monstring;
}