<?php

require_once('UKM/sql.class.php');

class SecretFinder {
	public function getSecret($api_key) {
		$sql = new SQL("SELECT secret FROM API_Keys WHERE `api_key` = '#api_key'", array('api_key' => $api_key));

		$result = $sql->run('field', 'secret');
		return $result;
	}
}