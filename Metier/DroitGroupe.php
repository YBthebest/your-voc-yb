<?php
class DroitGroupe extends Entity{
	private $libelle;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['libelle']))$this->libelle = $donnees['libelle'];
	}
	
	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
	}
	
	public function libelle(){
		return $this->libelle;
	}
	public function setLibelle($libelle){
		$this->libelle = $libelle;
	}
}
?>