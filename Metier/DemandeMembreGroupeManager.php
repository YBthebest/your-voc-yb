<?php
class DemandeMembreGroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "demande_membre_groupe";
	public $entityName = "DemandeMembreGroupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id"] = "id";
		$this->arrayBinding["id_groupe"] = "idGroupe";
		$this->arrayBinding["id_membre"] = "pseudo";
		$this->arrayBinding["statut"] = "statut";
	}
	
	protected function newInstanceEntity($donnees){
		return new DemandeMembreGroupe($donnees);
	}
	
	public function getLastDemandeByPseudoAndIdGroupe($pseudo, $idGroupe){
		$query = "select * from ".$this->table." where id_membre = :id_membre AND id_groupe = :id_groupe ORDER BY id DESC LIMIT 1" ;
		$entity = new DemandeMembreGroupe(array("id_membre"=>$pseudo, "id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
	
	public function createDemande($idGroupe, $idMembre){
		$query = "insert into ".$this->table." values('', '".$idGroupe."', '".$idMembre."', 'pending')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getDemandePendingByIdGroupe($idGroupe){
		$query = "select * from ".$this->table." where statut = 'pending' AND id_groupe = :id_groupe" ;
		$entity = new DemandeMembreGroupe(array("id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
	public function updateDemandeByStatut($idGroupe, $idMembre, $statut){
		$query = "update ".$this->table." SET statut = '$statut' WHERE id_membre = '$idMembre' AND id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getDemandeByStatut($statut){
		$query = "select * from ".$this->table." where statut = '$statut'" ;
		$entity = new DemandeMembreGroupe(array("statut"=>$statut));
		return $this->select($query, $entity);
	}
	public function deleteDemande($idGroupe, $idMembre){
		$query = "delete from ".$this->table." where id_groupe = '$idGroupe' and id_membre = '$idMembre'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllDemandeByGroupe($idGroupe){
		$query = "delete from ".$this->table." where id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllDemandeByMembre($idMembre){
		$query = "delete from ".$this->table." where id_membre = '$idMembre'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>