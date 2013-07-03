<?php
class DroitGroupeManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "droit_groupe";
	public $entityName = "DroitGroupe";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id"] = "id";
		$this->arrayBinding["libelle"] = "libelle";
	}
	
	protected function newInstanceEntity($donnees){
		return new Membre($donnees);
	}
}
?>