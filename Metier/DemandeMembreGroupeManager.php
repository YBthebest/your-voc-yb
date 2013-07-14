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
	
	public function getDemandeByPseudoAndIdGroupe($pseudo, $idGroupe){
		$query = "select * from ".$this->table." where id_membre = :id_membre AND id_groupe = :id_groupe ORDER BY id DESC" ;
		$entity = new DemandeMembreGroupe(array("id_membre"=>$pseudo, "id_groupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
	
	public function createDemande($idGroupe, $idMembre){
		$query = "insert into ".$this->table." values('', '".$idGroupe."', '".$idMembre."', 'pending')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>