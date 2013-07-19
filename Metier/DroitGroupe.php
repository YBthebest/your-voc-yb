<?php
class DroitGroupe extends Entity{
	private $libelle;
	private $idMembre;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['libelle']))$this->libelle = $donnees['libelle'];
		if(isset($donnees['id_membre']))$this->idMembre = $donnees['id_membre'];
		if(isset($donnees['id_groupe']))$this->idGroupe = $donnees['id_groupe'];
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
	public function idMembre(){
		return $this->idMembre;
	}
	public function setIdMembre($idMembre){
		$this->idMembre = $idMembre;
	}
	public function idGroupe(){
		return $this->idGroupe;
	}
	public function setIdGroupe($idGroupe){
		$this->idGroupe = $idGroupe;
	}
}
?>