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
		$query = "select * from ".$this->table." where pseudo = :pseudo AND id_groupe = :idGroupe" ;
		$entity = new Vote(array("pseudo"=>$pseudo, "idGroupe"=>$idGroupe));
		return $this->select($query, $entity);
	}
}
?>