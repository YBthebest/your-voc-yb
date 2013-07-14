<?php
class GroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "groupe";
	public $entityName = "Groupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding[$this->ID_COLUMN] = "id";
		$this->arrayBinding["nom"] = "nom";
		$this->arrayBinding["id_createur"] = "idCreateur";
		$this->arrayBinding["date"] = "date";		
	}
	
	protected function newInstanceEntity($donnees){	
		return new Groupe($donnees);
	}
	
	public function getGroupeById($id){
		$query = "select * from ".$this->table." where id = :id" ;
		$entity = new Groupe(array("id"=>$id));
		return $this->selectUniqueResult($query, $entity);
	}
	public function createGroupe($nom, $idCreateur, $date){
		$query = "insert into ".$this->table." values('', '".$nom."', '".$idCreateur."', '".$date."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getGroupeByNom($nom){
		$query = "select * from ".$this->table." where nom = :nom" ;
		$entity = new Groupe(array("nom"=>$nom));
		return $this->selectUniqueResult($query, $entity);
	}
	public function getGroupeByNomAndCreateur($nom, $idCreateur){
		$query = "select * from ".$this->table." where nom = :nom and id_createur = :id_createur" ;
		$entity = new Groupe(array("nom"=>$nom, "id_createur"=>$idCreateur));
		return $this->selectUniqueResult($query, $entity);
	}
}
?>
