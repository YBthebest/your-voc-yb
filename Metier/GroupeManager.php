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
	public function createGroupe($nom, $date){
		$query = "insert into ".$this->table." values('', '".$nom."', '".$date."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>
