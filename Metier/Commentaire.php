<?php
class Commentaire extends Entity{
	private $id_liste;
	private $membre;
	private $commentaire;
	private $date;
	private $timestamp;
	
	function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['id_liste']))$this->id_liste = $donnees['id_liste'];
		if(isset($donnees['id_membre']))$this->membre = $donnees['id_membre'];		
		if(isset($donnees['commentaire']))$this->commentaire = $donnees['commentaire'];
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
	
	public function id_liste(){
		return $this->id_liste;
	}
	public function setId_liste($p_id_liste){
		$this->id_liste = $id_liste;
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
	
	public function commentaire(){
		return $this->commentaire;
	}
	public function setCommentaire($commentaire){
		$this->commentaire = $commentaire;
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
}
?>