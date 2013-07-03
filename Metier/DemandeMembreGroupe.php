<?php
class DemandeMembreGroupe extends Entity{
	private $idGroupe;
	private $pseudo;
	private $statut;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['id_groupe']))$this->idGroupe = $donnees['id_groupe'];
		if(isset($donnees['id_membre']))$this->pseudo = $donnees['id_membre'];
		if(isset($donnees['statut']))$this->statut = $donnees['statut'];
	}
	
	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
	}
	
	public function idGroupe(){
		return $this->idGroupe;
	}
	public function setIdGroupe($idGroupe){
		$this->idGroupe = $idGroupe;
	}
	public function pseudo(){
		return $this->pseudo;
	}
	public function setPseudo($pseudo){
		$this->pseudo = $pseudo;
	}
	public function statut(){
		return $this->statut;
	}
	public function setStatut($statut){
		$this->statut = $statut;
	}
}
?>