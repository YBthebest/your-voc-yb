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
		return new Categorie($donnees);
	}
}
?>
