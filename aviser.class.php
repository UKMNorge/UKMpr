<?php

require_once('UKM/sql.class.php');

class aviser {
	
	private $aviser = null;
	private $fylke = null;
	
	
	public function __construct() {
		
	}
	
	public function hasRelation( $kommune ) {
		$sql = new SQL("SELECT `id` FROM `ukm_avis_nedslagsfelt` 
						WHERE `kommune_id` = '#kommune'
						LIMIT 1",
						array('kommune' => $kommune ) 
						);
		$res = $sql->run('field', 'id');
		if( null === $res ) {
			return false;
		}
		return true;
	}
	
	public function getAllByFylke( $fylke_id ) {
		$this->fylke = $fylke_id;
		if( null == $this->aviser ) {
			$this->_load();
		}
		
		return $this->aviser;
	}
	
	public function relate( $avis, $kommune ) {
		$SQL = new SQLins('ukm_avis_nedslagsfelt');
		$SQL->add('avis_id', $avis );
		$SQL->add('kommune_id', $kommune );
		$SQL->run();
	}
	
	public function unrelateAll( $kommuner ) {
		if( is_array( $kommuner ) ) {
			foreach( $kommuner as $kommune ) {
				$this->unrelateAllForKommune( $kommune['id'] );
			}
		}
	}
	
	public function unrelateAllForKommune( $k_id ) {
		$SQLdel = new SQLdel('ukm_avis_nedslagsfelt', array('kommune_id' => $k_id ) );
		$res = $SQLdel->run();
		return $res;
	}
	
	private function _load() {
		$sql = new SQL("SELECT * FROM `ukm_avis`
						WHERE `fylke` = '#fylke'
						ORDER BY `type` ASC,
						`name` ASC",
						array('fylke'=> $this->fylke )
					);
		$res = $sql->run();

		while( $r = SQL::fetch( $res ) ) {
			$this->aviser[] = new avis( $r );
		}
	}
}

class avis {
	private $id;
	private $name;
	private $url;
	private $email;
	private $fylke;
	private $type;
	
	public function __construct( $avis ) {
		if( is_numeric( $avis ) ) {
			$this->_load_from_id( $avis );
		} else {
			$this->_load_from_row( $avis );
		}
	}
	
	public function isRelated( $kommune ) {
		$sql = new SQL("SELECT * FROM `ukm_avis_nedslagsfelt`
						WHERE `avis_id` = '#avis'
						AND `kommune_id` = '#kommune'",
						array( 'avis' => $this->id, 'kommune' => $kommune )
					);
		$res = $sql->run('field', 'id');
		if( null === $res ) {
			return false;
		}
		return true;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getType() {
		return $this->type;
	}
	
	private function _load_from_id( $id ) {
		$SQL = new SQL("SELECT * FROM `ukm_avis` WHERE `id` = '#id'",
						array('id' => $id ) );
		$res = $SQL->fetch('array');
		$this->_load_from_row( $res );
	}
	
	private function _load_from_row( $row ) {
		$this->id = $row['id'];
		$this->name = utf8_encode($row['name']);
		$this->url = $row['url'];
		$this->email = $row['email'];
		$this->fylke = $row['fylke'];
		$this->type = $row['type'];
	}
}