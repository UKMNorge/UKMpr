<?php
/* 
Plugin Name: UKMpr
Plugin URI: http://www.ukm-norge.no
Description: Alt om markedsføring i UKM. Materiell, lokalaviser og nedslagsfelt, husk UKM
Author: UKM Norge / M Mandal 
Version: 2.0
Author URI: http://www.ukm-norge.no
*/


require_once('UKM/wp_modul.class.php');

class UKMpr extends UKMWPmodul
{
	public static $action = 'dashboard';
	public static $path_plugin = null;

	public static function hook()
	{
		if (in_array(get_option('site_type'), ['kommune', 'fylke', 'land'])) {
			add_action('admin_menu', ['UKMpr', 'meny']);
			add_filter('UKMWPDASH_messages', ['UKMpr', 'meldinger']);
		}
	}

	/**
	 * Oppretter admin meny
	 *
	 * @return void
	 */
	public static function meny()
	{
		$page = add_menu_page(
			'Markedsføring',
			'Markedsføring',
			'editor',
			'UKMmarketing',
			['UKMpr', 'renderAdmin'],
			'//ico.ukm.no/megaphone-menu.png',
			40
		);
		add_action(
			'admin_print_styles-' . $page,
			['UKMpr', 'scripts_and_styles']
		);

		if (in_array(get_option('site_type'), ['fylke', 'land'])) {
			$page_pressemelding = add_submenu_page(
				'UKMmarketing',
				'Pressemelding',
				'Pressemelding',
				'editor',
				'UKMpr_melding',
				['UKMpr', 'renderPressemelding']
			);
			add_action(
				'admin_print_styles-' . $page_pressemelding,
				['UKMpr', 'scripts_and_styles']
			);
		}

		$page_lokalaviser = add_submenu_page(
			'UKMmarketing',
			'Lokalaviser',
			'Lokalaviser',
			'editor',
			'UKMpr_lokalaviser',
			['UKMpr', 'renderLokalaviser']
		);
		add_action(
			'admin_print_styles-' . $page_lokalaviser,
			['UKMpr', 'scripts_and_styles']
		);

		$page_husk = add_submenu_page(
			'UKMmarketing',
			'Husk UKM',
			'Husk UKM',
			'editor',
			'UKMpr_husk',
			['UKMpr', 'renderHusk']
		);
		add_action(
			'admin_print_styles-' . $page_husk,
			['UKMpr', 'scripts_and_styles']
		);
	}

	/**
	 * Vis admin-siden for Husk UKM
	 *
	 * @return void
	 */
	public static function renderHusk()
	{
		static::setAction('husk');
		static::renderAdmin();
	}

	/**
	 * Vis admin-siden for lokalaviser
	 *
	 * @return void
	 */
	public static function renderLokalaviser()
	{
		if (isset($_GET['action'])) {
			static::setAction('lokalaviser/' . basename($_GET['action']));
		} else {
			static::setAction('lokalaviser/list');
		}
		static::renderAdmin();
	}
	/**
	 * Vis admin-siden for pressemelding
	 *
	 * @return void
	 */
	public static function renderPressemelding()
	{
		require_once( static::getPluginPath() .'controller/pressemelding.controller.php');
	}

	/**
	 * Legger til meldinger i WP-dash
	 *
	 * @param Array $meldinger
	 * @return Array $meldinger
	 */
	public static function meldinger($meldinger)
	{
		require_once('UKM/aviser.class.php');
		require_once('UKM/monstring.class.php');

		$aviser = new aviser();
		$monstring = new monstring_v2(get_option('pl_id'));

		if (get_option('site_type') == 'kommune') {
			foreach ($monstring->getKommuner()->getAll() as $kommune) {
				if (!$aviser->hasRelation($kommune->getId())) {
					$meldinger[] = array(
						'level' 	=> 'alert-error',
						'link' 		=> 'admin.php?page=UKMpr',
						'header' 	=> 'Info om lokalaviser mangler!',
						'body' 		=> 'Velg "Lokalaviser" i menyen til venstre'
					);
					break;
				}
			}
		} elseif (get_option('site_type') == 'fylke') {
			$count = 0;
			foreach ($monstring->getFylke()->getKommuner()->getAll() as $kommune) {
				if (!$aviser->hasRelation($kommune->getId())) {
					$count++;
				}
			}
			if ($count > 0) {
				$percent_missing = (100 / $monstring->getFylke()->getKommuner()->getAntall()) * $count;
				$meldinger[] = array(
					'level' 	=> 'alert-' . ($percent_missing > 15 ? 'error' : 'warning'),
					'link' 		=> 'admin.php?page=UKMpr',
					'header' 	=> $count . ' ' . ($monstring->getFylke()->erOslo() ? 'bydeler' : 'kommuner') . ' mangler info om lokalaviser!',
					'body' 		=> 'Velg "Lokalaviser" i menyen til venstre'
				);
			}
		}

		return $meldinger;
	}

	/**
	 * Scripts og styles for modulen
	 *
	 * @return void
	 */
	public static function scripts_and_styles()
	{
		wp_enqueue_script('WPbootstrap3_js');
		wp_enqueue_style('WPbootstrap3_css');
	}
}

UKMpr::init(__DIR__);
if (is_admin()) {
	UKMpr::hook();
}