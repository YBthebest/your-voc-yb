<?php
class RevisionManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "revise";
	public $entityName = "Revision";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id_liste"] = "id_liste";
		$this->arrayBinding["id_membre"] = "membre";
		$this->arrayBinding["moyenne"] = "moyenne";
		$this->arrayBinding["date"] = "date";
	}
	
	protected function newInstanceEntity($donnees){
		return new Revision($donnees);
	}
	
	public function getRevisionsByPseudoLimit20($pseudo){
		$query = "select * from ".$this->table." where id_membre = :id_membre ORDER BY id DESC LIMIT 20";
		$entity = new Revision(array());
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);
	}
	public function getRevisionsByPseudoLimit3($pseudo){
		$query = "select * from ".$this->table." where id_membre = :id_membre ORDER BY id DESC LIMIT 3";
		$entity = new Revision(array());
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);
	}
	public function getAllRevisions(){
		$query = "select * from ".$this->table."";
		$entity = "";
		return $this->select($query, $entity);
	}
	public function createNewRevision($id, $membre, $moyenne, $time){
		$query = "insert into ".$this->table." values('', '".$id."', '".$membre."', '".$moyenne."', '".$time."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllRevisionsByMembre($idMembre){
		$query = "delete from ".$this->table." where id_membre = ".$idMembre."" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>