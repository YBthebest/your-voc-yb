<?php
class ListesGroupe extends Entity{
	private $idListe;
	private $idMembre;
	private $idGroupe;
	private $date;
	private $timestamp;
	
	public function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['id_liste']))$this->idListe = $donnees['id_liste'];
		if(isset($donnees['id_membre']))$this->idMembre = $donnees['id_membre'];
		if(isset($donnees['id_groupe']))$this->idGroupe = $donnees['id_groupe'];		
		if(isset($donnees['date'])){
			$this->setTimestamp($donnees['date']);
		}
	}

	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
	}
	public function idListe(){
		return $this->idListe;
	}
	public function setIdListe($p_idListe){
		$this->idListe = $p_idListe;
	}
	public function idMembre(){
		return $this->idMembre;
	}
	public function setIdMembre($p_idMembre){
		$this->idMembre = $p_idMembre;
	}
	public function idGroupe(){
		return $this->idGroupe;
	}
	public function setIdGroupe($p_idGroupe){
		$this->idGroupe = $p_idGroupe;
	}
	public function date(){
		return $this->date;
	}
	public function timestamp(){
		return $this->timestamp;
	}
	public function setTimestamp($timestamp){
		$this->date = timestampToString($timestamp);
		$this->timestamp = $timestamp;
	}
	public function droitMembres(){
		return $this->droitMembres;
	}
	public function setDroitMembres($p_droitMembres){
		$this->droitMembres = $p_droitMembres;
	}
}
?>