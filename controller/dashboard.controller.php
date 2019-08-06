<?php
global $wpdb;

/**
 * BRUKER-INFO FOR INNLOGGING TIL INSTRATO
 */
$current_user = wp_get_current_user();
$cuid = $current_user->ID;
$bruker = $wpdb->get_row("SELECT `b_id`,`lock_email`, `b_email` FROM `ukm_brukere`
						  WHERE `wp_bid` = '" . $cuid . "'");
$instrato_uid = $bruker->b_id;
$instrato_key = md5($bruker->b_id . UKM_INSTRATO_PEPPER);

// Deltaker-brukere oppfører seg litt annerledes. Override.
$qry = new SQL(
	"SELECT `p_id`, `username`, `email`
	FROM `ukm_wp_deltakerbrukere`
	WHERE `wp_id` = #cuid",
	['cuid' => $cuid]
);
$b = $qry->run('array');
if ($b) {
	$bruker_id = str_pad($b['p_id'], 8, "1000000000", STR_PAD_LEFT);
	$instrato_uid = $bruker_id;
	$instrato_key = md5($bruker_id . UKM_INSTRATO_PEPPER);
}

// Send bruker-info til view
UKMpr::addViewData(
	'user_id',
	$instrato_uid
);
UKMpr::addViewData(
	'user_key',
	$instrato_key
);


/**
 * EKSTERN-BRUKERE TIL INSTRATO
 */
if (get_option('site_type') == 'fylke') {
	require_once('UKM/monstring.class.php');
	$monstring = new monstring_v2(get_option('pl_id'));

	switch ($monstring->getFylke()->getURLsafe()) {
		case 'finnmark':
			$instrato_ekstern_pwd = 'postbud50';
			break;
		case 'troms':
			$instrato_ekstern_pwd =  'alke13';
			break;
		case 'nordland':
			$instrato_ekstern_pwd =  'dramatisere60';
			break;
		case 'nord-trondelag':
			$instrato_ekstern_pwd =  'tusenarig42';
			break;
		case 'sor-trondelag':
			$instrato_ekstern_pwd =  'trengende36';
			break;
		case 'moreogromsdal':
			$instrato_ekstern_pwd =  'opsjon72';
			break;
		case 'sognogfjordane':
			$instrato_ekstern_pwd =  'bindestrek63';
			break;
		case 'hordaland':
			$instrato_ekstern_pwd =  'deviasjon69';
			break;
		case 'rogaland':
			$instrato_ekstern_pwd =  'laken15';
			break;
		case 'vest-agder':
			$instrato_ekstern_pwd =  'harborste22';
			break;
		case 'aust-agder':
			$instrato_ekstern_pwd =  'kjorebane17';
			break;
		case 'telemark':
			$instrato_ekstern_pwd =  'blindgate24';
			break;
		case 'vestfold':
			$instrato_ekstern_pwd =  'bilradio81';
			break;
		case 'buskerud':
			$instrato_ekstern_pwd =  'utstrale62';
			break;
		case 'oslo':
			$instrato_ekstern_pwd =  'delfinfisk76';
			break;
		case 'ostfold':
			$instrato_ekstern_pwd =  'injurie94';
			break;
		case 'akershus':
			$instrato_ekstern_pwd =  'ventilatorhette79';
			break;
		case 'oppland':
			$instrato_ekstern_pwd =  'tilfredsstille52';
			break;
		case 'hedmark':
			$instrato_ekstern_pwd =  'aksjoner44';
			break;
	}
	UKMpr::addViewData(
		'eksternbruker',
		[
			'navn' => $monstring->getFylke()->getURLsafe() . '-ekstern',
			'pass' => $instrato_ekstern_pwd
		]
	);
}
?>