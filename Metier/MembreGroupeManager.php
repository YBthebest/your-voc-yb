<?php
class MembreGroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "membre_groupe";
	public $entityName = "MembreGroupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id"] = "id";
		$this->arrayBinding["id_membre"] = "idMembre";
		$this->arrayBinding["id_groupe"] = "idGroupe";
		$this->arrayBinding["id_droit"] = "idDroit";
	}
	
	protected function newInstanceEntity($donnees){
		return new MembreGroupe($donnees);
	}
}
?>