<?php
class MembreGroupe extends Entity{
	private $idMembre;
	private $idGroupe;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['idMembre']))$this->idMembre = $donnees['idMembre'];
		if(isset($donnees['idGroupe']))$this->idGroupe = $donnees['idGroupe'];
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
		$this->idGroupe = idGroupe;
	}
}
?>