<?php
class ListesGroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "listes_groupe";
	public $entityName = "ListesGroupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding[$this->ID_COLUMN] = "id";
		$this->arrayBinding["id_liste"] = "idListe";
		$this->arrayBinding["id_membre"] = "idMembre";
		$this->arrayBinding["id_groupe"] = "idGroupe";
		$this->arrayBinding["date"] = "date";	
	}
	
	protected function newInstanceEntity($donnees){	
		return new ListesGroupe($donnees);
	}
	public function createListeGroupe($id_liste, $id_membre, $id_groupe, $date){
		$query = "insert into ".$this->table." values('', '".$id_liste."', '".$id_membre."', '".$id_groupe."', '".$date."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getListesGroupeByIdGroupe($id_groupe){
		$query = "select * from ".$this->table." where id_groupe = :id_groupe order by id desc" ;
		$entity = new ListesGroupe(array("id_groupe"=>$id_groupe));
		return $this->select($query, $entity);
	}
	public function deleteListeGroupe($id, $idMembre, $idGroupe){
		$query = "delete from ".$this->table." where id = '".$id."' and id_membre = '".$idMembre."' and id_groupe = '".$idGroupe."'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllListesByGroupe($idGroupe){
		$query = "delete from ".$this->table." where id_groupe = '$idGroupe'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllListesGroupeByMembre($idMembre){
		$query = "delete from ".$this->table." where id_membre = '$idMembre'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}

}
?>
