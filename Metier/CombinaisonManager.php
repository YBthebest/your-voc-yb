<?php
class CombinaisonManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "combiner";
	public $entityName = "Combinaison";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding[$this->ID_COLUMN] = "id";
		$this->arrayBinding["liste"] = "liste";
		$this->arrayBinding["id_membre"] = "membre";
		$this->arrayBinding["titre"] = "titre";
		$this->arrayBinding["date"] = "date";
		$this->arrayBinding["id_liste"] = "id_liste";
	}
	
	protected function newInstanceEntity($donnees){
		return new Combinaison($donnees);
	}
	
	public function getCombinaisonByPseudoLimit5($pseudo){
		$query = "select * from ".$this->table." where id_membre = :id_membre ORDER BY id DESC LIMIT 5";
		$entity = new Combinaison(array());
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);		
	}
	public function getCombinaisonByPseudoLimit15($pseudo){
		$query = "select * from ".$this->table." where id_membre = :id_membre ORDER BY id DESC LIMIT 15";
		$entity = new Combinaison(array());
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);
	}
	public function createNewCombinaison($id, $membre, $titre, $mots, $date){
		$query = "insert into ".$this->table." values('', '".$membre."', '".$mots."', '".$titre."', '".$date."', '".$id."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteCombinaisonByIdAndMembre($id, $membre){
		$query = "delete from ".$this->table." where id = '".$id."' and id_membre = '".$membre."'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>