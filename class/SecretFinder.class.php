<?php

use UKMNorge\Database\SQL\Query;

require_once('UKM/Autoloader.php');

class SecretFinder {
	public function getSecret($api_key) {
		$sql = new Query("SELECT secret FROM API_Keys WHERE `api_key` = '#api_key'", array('api_key' => $api_key));

		$result = $sql->run('field', 'secret');
		return $result;
	}
}