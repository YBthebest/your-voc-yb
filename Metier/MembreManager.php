<?php
class MembreManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "membre";
	public $entityName = "Membre";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding["id"] = "id";
		$this->arrayBinding["login"] = "login";
		$this->arrayBinding["email"] = "email";
		$this->arrayBinding["pass_md5"] = "pass";
	}
	
	protected function newInstanceEntity($donnees){
		return new Membre($donnees);
	}
	
	public function getMembreByLogin($login){
		$query = "select * from ".$this->table." where login = :login" ;
		$entity = new Membre(array("login"=>$login));
		return $this->select($query, $entity);
	}
	
	public function getMembreById($id){
		$query = "select * from ".$this->table." where id = :id" ;
		$entity = new Membre(array("id"=>$id));
		return $this->select($query, $entity);
	}
	
	public function getMembreByEmail($email){
		$query = "select * from ".$this->table." where email = :email" ;
		$entity = new Membre(array("email"=>$email));
		return $this->select($query, $entity);
	}
	public function getPassByLogin($login){
		$query = "select pass_md5 from ".$this->table." where login = :login" ;
		$entity = new Membre(array("login"=>$login));
		return $this->select($query, $entity);
	}
	
	public function saveMembre($pseudo, $email, $password){		
		$query = "insert into ".$this->table." values('', '".$pseudo."', '".$password."', '".$email."')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function updateMdpByLogin($mdp, $pseudo){
		$query = "update ".$this->table." set pass_md5 = '$mdp' where login = '$pseudo' " ;
		$statement = $this->_db->prepare($query);
		$statement->execute();		
	}
}
?>