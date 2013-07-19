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

}
?>
