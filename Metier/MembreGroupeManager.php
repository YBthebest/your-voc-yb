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
	
	public function getMembreByIdGroupeAndMembre($idGroupe, $idMembre){
		$query = "select * from ".$this->table." where id_membre = :id_membre AND id_groupe = :id_groupe" ;
		$entity = new MembreGroupe(array("id_membre"=>$idMembre, "id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
}
?>