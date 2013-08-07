<?php
class VoteManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "vote";
	public $entityName = "Vote";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id_liste"] = "id_liste";
		$this->arrayBinding["note"] = "note";
		$this->arrayBinding["id_membre"] = "membre";
	}
	
	protected function newInstanceEntity($donnees){
		return new Vote($donnees);
	}
	
	public function getVotesById($id){
		$query = "select * from ".$this->table." where id_liste = :id_liste" ;
		$entity = new Vote(array("id_liste"=>$id));
		return $this->select($query, $entity);
	}
	
	public function getVotesByIdAndPseudo($id, $pseudo){
		$query = "select * from ".$this->table." where id_liste = :id_liste AND id_membre = :id_membre" ;
		$entity = new Vote(array("id_liste"=>$id));
		$entity -> setMembre($pseudo);
		return $this->select($query, $entity);
	}
	
	public function createVote($id_liste, $note, $pseudo){	
		$query = "insert into ".$this->table." values('', '".$id_liste."', '".$note."', '".$pseudo."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteAllVotesByMembre($idMembre){
		$query = "delete from ".$this->table." where id_membre = ".$idMembre."" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>