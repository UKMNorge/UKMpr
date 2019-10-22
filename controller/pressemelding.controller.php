<?php

if ('POST' == $_SERVER['REQUEST_METHOD']) {
	$res = update_option('pressemelding', $_POST['pressemelding_editor']);
	if( $res ) {
		UKMpr::getFlash()->add('success', 'Lagret pressemelding!');
	} else {
		UKMpr::getFlash()->add('danger', 'Kunne ikke lagre pressemeldingen');
	}
	
}
echo TWIG(
	'pressemelding_pre_editor.html.twig',
	UKMpr::getViewData(), 
	UKMpr::getPluginPath()
);

wp_editor(
	stripslashes(
		get_option('pressemelding')
	), 
	'pressemelding_editor', 
	[]
);

echo TWIG(
	'pressemelding_post_editor.html.twig',
	UKMpr::getViewData(), 
	UKMpr::getPluginPath()
);