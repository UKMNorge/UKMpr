<?php

require_once(__DIR__.'/MobilnummerCollection.class.php');
require_once(__DIR__.'/SecretFinder.class.php');

### Denne klassen brukes av rapport-modulen for å hente ut mobilnummer.
class MobilnummerForSted {

	private $api_key = 'ukmno_rekruttering';

	public function __construct($fylke_id, $kommune_id) {
		$this->fylke_id = $fylke_id;
		$this->kommune_id = $kommune_id;

		$this->secretFinder = new SecretFinder();	
	}

	public function fetchAll() {
		$mobilnummerCollection = new MobilnummerCollection($this->api_key, $this->secretFinder->getSecret($this->api_key));
		$nummer = $mobilnummerCollection->fetchByPlace($this->fylke_id, $this->kommune_id);

		return $nummer;
	}

	public function fetchByYear() {

	}
}