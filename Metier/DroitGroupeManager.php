<?php
class DroitGroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "droit_groupe";
	public $entityName = "DroitGroupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id"] = "id";
		$this->arrayBinding["libelle"] = "libelle";
		$this->arrayBinding["id_membre"] = "idMembre";
		$this->arrayBinding["id_groupe"] = "idGroupe";
	}
	
	protected function newInstanceEntity($donnees){
		return new DroitGroupe($donnees);
	}
	public function createDroit($libelle, $idMembre, $idGroupe){
		$query = "insert into ".$this->table." values('', '".$libelle."', '".$idMembre."', '".$idGroupe."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getDroitByIdMembreAndIdGroupe($idMembre, $idGroupe){
		$query = "select * from ".$this->table." where id_membre = :id_membre AND id_groupe = :id_groupe" ;
		$entity = new DroitGroupe(array("id_membre"=>$idMembre, "id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
	public function deleteDroit($idMembre, $idGroupe){
		$query = "delete from ".$this->table." where id_membre = '$idMembre' AND id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function updateDroitLibelle($idMembre, $idGroupe, $libelle){
		$query = "update ".$this->table." set libelle = '$libelle' where id_membre = '$idMembre' AND id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllDroitByGroupe($idGroupe){
		$query = "delete from ".$this->table." where id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}	
}
?>