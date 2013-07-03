<?php
class MembreGroupe extends Entity{
	private $idMembre;
	private $idGroupe;
	private $idDroit;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['id_membre']))$this->idMembre = $donnees['id_membre'];
		if(isset($donnees['id_groupe']))$this->idGroupe = $donnees['id_groupe'];
		if(isset($donnees['id_droit']))$this->idDroit = $donnees['id_droit'];
	}
	
	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
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
	public function idDroit(){
		return $this->idDroit;
	}
	public function setIdDroit($idDroit){
		$this->idDroit = $idDroit;
	}
}
?>