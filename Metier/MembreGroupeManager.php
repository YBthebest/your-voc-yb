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
	public function createMembreGroupe($idMembre, $idGroupe, $idDroit){
		$query = "insert into ".$this->table." values('', '".$idMembre."', '".$idGroupe."', '".$idDroit."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getMembresByIdGroupe($idGroupe){
		$query = "select * from ".$this->table." where id_groupe = :id_groupe" ;
		$entity = new MembreGroupe(array("id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
	public function deleteMembreGroupe($idMembre, $idGroupe){
		$query = "delete from ".$this->table." where id_membre = '$idMembre' AND id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getMembreGroupeByIdMembre($idMembre){
		$query = "select * from ".$this->table." where id_membre = :id_membre" ;
		$entity = new MembreGroupe(array("id_membre"=>$idMembre));
		return $this->select($query, $entity);
	}
	public function deleteAllMembreGroupe($idGroupe){
		$query = "delete from ".$this->table." where id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>