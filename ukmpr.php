<?php
/* 
Plugin Name: UKMpr
Plugin URI: http://www.ukm-norge.no
Description: Alt om markedsføring i UKM. Materiell, lokalaviser og nedslagsfelt, husk UKM
Author: UKM Norge / M Mandal 
Version: 2.0
Author URI: http://www.ukm-norge.no
*/

use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Geografi\Fylker;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Wordpress\Modul;
use UKMNorge\Avis\Aviser;

require_once('UKM/Autoloader.php');

class UKMpr extends Modul
{
	public static $action = 'dashboard';
	public static $path_plugin = null;

	public static function hook()
	{
        add_action('user_admin_menu', ['UKMpr', 'meny']);
		if (get_option('pl_id')){
            add_action('admin_menu', ['UKMpr', 'menyArrangement'],1000);
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
			'subscriber',
			'UKMmarketing',
			['UKMpr', 'renderAdmin'],
			'dashicons-megaphone',#'//ico.ukm.no/megaphone-menu.png',
			40
		);
		add_action(
			'admin_print_styles-' . $page,
			['UKMpr', 'scripts_and_styles']
		);

		if (in_array(get_option('pl_eier_type'), ['fylke', 'land'])) {
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
            /*
            $page_lokalaviser = add_submenu_page(
                'UKMmonstring',
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
            */
		}
    }

    /**
     * Meny for arrangementene
     *
     * @return void
     */
    public static function menyArrangement() {
        $arrangement = new Arrangement(intval(get_option('pl_id')));
        if( in_array($arrangement->getEierType(), ['fylke', 'land']) && $arrangement->erMonstring() ) {
            $page_pressemelding = add_submenu_page(
                'index.php',
                'Pressemelding',
                'Pressemelding',
                'editor',
                'UKMpressemelding',
                ['UKMpr', 'renderPressemelding'],
                100
            );
            add_action(
                'admin_print_styles-' . $page_pressemelding,
                ['UKMpr', 'scripts_and_styles']
            );
        }
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
		static::include('controller/pressemelding.controller.php');
	}

	/**
	 * Legger til meldinger i WP-dash
	 *
	 * @param Array $meldinger
	 * @return Array $meldinger
	 */
	public static function meldinger($meldinger)
	{
		$aviser = new Aviser();        

		if (get_option('pl_eier_type') == 'kommune') {
            $kommuner = explode(',', get_option('kommuner') );
			foreach ( $kommuner as $kommune_id ) {
                $kommune = new Kommune( (Int) $kommune_id );
				if (!$aviser->hasRelation($kommune->getId())) {
					$meldinger[] = array(
						'level' 	=> 'alert-error',
						'link' 		=> '/wp-admin/user/admin.php?page=UKMmarketing',
						'header' 	=> 'Info om lokalaviser mangler!',
                        'body' 		=> 'Velg "Lokalaviser" i menyen til venstre',
                        'target'    => '_blank'
					);
					break;
				}
			}
		} elseif (get_option('pl_eier_type') == 'fylke') {
            try {
                $count = 0;
                $fylke = Fylker::getById( (Int) get_option('fylke') );
                foreach ($fylke->getKommuner()->getAll() as $kommune) {
                    if (!$aviser->hasRelation($kommune->getId())) {
                        $count++;
                    }
                }
                /*
                if ($count > 0) {
                    $percent_missing = (100 / $monstring->getFylke()->getKommuner()->getAntall()) * $count;
                    $meldinger[] = array(
                        'level' 	=> 'alert-' . ($percent_missing > 15 ? 'error' : 'warning'),
                        'link' 		=> 'admin.php?page=UKMpr',
                        'header' 	=> $count . ' ' . ($monstring->getFylke()->erOslo() ? 'bydeler' : 'kommuner') . ' mangler info om lokalaviser!',
                        'body' 		=> 'Velg "Lokalaviser" i menyen til venstre'
                    );
                }
                */
            } catch( Exception $e ) {
                return $meldinger;
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