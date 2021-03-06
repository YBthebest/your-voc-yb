<?php
class Combinaison extends Entity{
	private $idListeOrigine;
	private $membre;
	private $liste;
	private $titre;
	private $date;
	private $timestamp;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['liste']))$this->liste = $donnees['liste'];
		if(isset($donnees['id_membre']))$this->membre = $donnees['id_membre'];
		if(isset($donnees['titre']))$this->titre = $donnees['titre'];
		if(isset($donnees['date'])){
			$this->setTimestamp($donnees['date']);			
		}
		if(isset($donnees['id_liste']))$this->id_liste = $donnees['id_liste'];
	}
	
	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
	}
	
	public function liste(){
		return $this->liste;
	}
	public function setListesDefinition($liste){
		$this->liste = $liste;
	}
	
	public function membre(){
		return $this->membre;
	}
	public function setMembre($membre){
		require_once('modelDAO.php');
		if(is_numeric($membre)){
			$m = getMembreById($membre);
			$this->membre = $m->login();
		}
		else{
			$m = getMembreByLogin($membre);	
			$this->membre = $m->id();
		}
	}
	
	public function titre(){
		return $this->titre;
	}
	public function setTitre($titre){
		$this->commentaire = $titre;
	}
	
	public function id_liste(){
		return $this->id_liste;
	}
	public function setId_Liste($idListe){
		$this->id_liste = $idListe;
	}
	public function setTimestamp($timestamp){
		$this->date = timestampToString($timestamp);
		$this->timestamp = $timestamp;
	}
}
?>