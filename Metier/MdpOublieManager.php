<?php
class MdpOublieManager extends DbManager{
	public $ID_COLUMN = "id";
	public $table = "mdp_oublie";
	public $entityName = "MdpOublie";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding[$this->ID_COLUMN] = "id";
		$this->arrayBinding["id_membre"] = "idMembre";
		$this->arrayBinding["token"] = "token";
		$this->arrayBinding["date"] = "date";
		$this->arrayBinding["dateExpire"] = "dateExpire";
		$this->arrayBinding["used"] = "used";		
	}
	
	protected function newInstanceEntity($donnees){	
		return new MdpOublie($donnees);
	}
	
	public function getTokenNotUsedByPseudo($pseudo){
		$date = time();
		$query = "select * from ".$this->table." where id_membre = :id_membre AND used = 'no' ORDER BY id DESC";
		$entity = new MdpOublie(array("id_membre"=>$pseudo));
		return $this->select($query, $entity);
	}
	public function createToken($pseudo, $token, $date, $dateExpire){
		$query = "insert into ".$this->table." values('', '".$pseudo."', '".$token."', '".$date."', '".$dateExpire."', 'no')" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getMdpOublieByToken($token){
		$date = time();
		$query = "select * from ".$this->table." where token = :token";
		$entity = new MdpOublie(array("token"=>$token));
		return $this->select($query, $entity);
	}
	public function updateUsedByTokenAndPseudo($token, $pseudo){
		$query = "update ".$this->table." set used = 'yes' where token = '$token' AND id_membre = '$pseudo'";
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
}
?>
