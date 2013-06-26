<?php
class Groupe extends Entity{
	private $nom;
	private $date;
	private $timestamp;
	
	public function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['nom']))$this->nom = $donnees['nom'];
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
	public function nom(){
		return $this->nom;
	}
	public function setNom($p_nom){
		$this->nom = $p_nom;
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