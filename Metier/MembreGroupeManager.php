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
		$this->arrayBinding["idMembre"] = "idMembre";
		$this->arrayBinding["idGroupe"] = "idGroupe";
	}
	
	protected function newInstanceEntity($donnees){
		return new Membre($donnees);
	}
}
?>