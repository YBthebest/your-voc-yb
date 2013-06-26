<?php
class MdpOublie extends Entity{
	private $pseudo;
	private $token;
	private $date;
	private $dateExpire;
	private $used;
	
	public function __construct(array $donnees){
		if(isset($donnees['id']))$this->id = $donnees['id'];
		if(isset($donnees['pseudo']))$this->pseudo = $donnees['pseudo'];
		if(isset($donnees['token']))$this->token = $donnees['token'];
		if(isset($donnees['date']))$this->date = $donnees['date'];
		if(isset($donnees['dateExpire']))$this->dateExpire = $donnees['dateExpire'];
		if(isset($donnees['used']))$this->used = $donnees['used'];
	}

	public function id(){
		return $this->id;
	}
	public function setId($p_id){
		$this->id= $p_id;
	}
	public function pseudo(){
		return $this->pseudo;
	}
	public function setPseudo($p_pseudo){
		$this->pseudo = $p_pseudo;
	}
	public function token(){
		return $this->token;
	}
	public function setToken($p_token){
		$this->token = $p_token;
	}
	public function date(){
		return $this->date;
	}
	public function setDate($p_date){
		$this->token = $p_token;
	}	
	public function dateExpire(){
		return $this->dateExpire;
	}
	public function setDateExpire($p_dateExpire){
		$this->dateExpire = $p_dateExpire;
	}
	public function used(){
		return $this->used;
	}
	public function setUsed($p_used){
		$this->used = $p_used;
	}
}
?>