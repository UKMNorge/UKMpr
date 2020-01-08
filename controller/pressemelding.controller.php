<?php

require_once('UKM/Autoloader.php');

use UKMNorge\Meta\Write;
use UKMNorge\Arrangement\Arrangement;

$arrangement = new Arrangement( intval(get_option('pl_id')) );
$pressemelding = $arrangement->getMeta('pressemelding');

if ('POST' == $_SERVER['REQUEST_METHOD']) {
    $pressemelding->setValue($_POST['pressemelding_editor']);
    try {
        Write::set($pressemelding);
		UKMpr::getFlash()->add('success', 'Lagret pressemelding!');
    } catch( Exception $e ) {
		UKMpr::getFlash()->add('danger', 'Kunne ikke lagre pressemeldingen. Systemet sa: '. $e->getMessage());
	}
	
}
echo TWIG(
	'pressemelding_pre_editor.html.twig',
	UKMpr::getViewData(), 
	UKMpr::getPluginPath()
);

wp_editor(
	stripslashes(
		$pressemelding->getValue()
	), 
	'pressemelding_editor', 
	[]
);

echo TWIG(
	'pressemelding_post_editor.html.twig',
	UKMpr::getViewData(), 
	UKMpr::getPluginPath()
);