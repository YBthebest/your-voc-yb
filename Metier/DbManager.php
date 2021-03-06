<?php
/**
 * @author lbigot
 */
abstract class DbManager {
	protected $_db; // Instance de PDO
	protected $arrayBinding;
	protected $table;
	protected $entityName;
	protected $ID_COLUMN;
	
	protected function __construct(){
		$this->init();
	}
	
	private function init(){
		$this->setDb(dbPDO());
		$this->binding();
		DBHelper::addManager($this);
	}
	
	abstract protected function newInstanceEntity($donnees);
	/**
	 * key = columnName , value=EntityPropertyName 
	 */
	abstract protected function binding();
	
	public function getID_COLUMN(){
		return $this->ID_COLUMN;
	}
	
	public function getEntityName(){
		return $this->entityName;
	}
	
	public function setDb(PDO $db){
		$this->_db = $db;
	}
	
	protected function select($query, $entity){
		$statement = (isset($entity))?$this->bind($query, $entity):$this->_db->prepare($query);
		$donnees = $statement->execute();	
		$entityListe = array();
		while ($donnees = $statement->fetch(PDO::FETCH_ASSOC)){			
			$entityListe[] = $this->newInstanceEntity($donnees);			
		}
		return $entityListe;
	}
	
	protected function selectUniqueResult($query, $entity){
		$result = $this->select($query, $entity);
		if(sizeof($result) == 1){
			$result = $result[0];
		}else if(sizeof($result) == 0){
			$result = null;
		}else {
			throw new Exception("Un resultat unique était attendu pour " + get_class($query));
		}
		return $result;
	}
	
	protected function count($query, $entity){
		$liste = $this->select($query, $entity);
		return sizeof($liste);
	}
	
	protected function countAll(){
		$query = "select count(*) as nombre from ".$this->table;
		$statement = $this->_db->prepare($query);
		return $statement->execute()->fetchColumn();;
	}
	
	public function delete($entity){
		$query = "DELETE FROM ".$this->table." WHERE $ID_COLUMN = :$ID_COLUMN".$entity->id();
		$this->_db->exec($query);
	}
	
	public function bind($query, $entity){	
		preg_match_all('#\s?:\s?[a-zA-Z0-9_]+\s?#', $query, $explodeQ);
		$explodeQuery = $explodeQ[0];
		$bindingArrayQuery = array();
		foreach($explodeQuery as $partQuery){		
			$bindingArrayQuery[] = str_replace(" ", "",$partQuery);
		}
		$statement = $this->_db->prepare($query);	
		
		foreach($bindingArrayQuery as $binder){
			$methodeName = $this->arrayBinding[str_replace(":", "",$binder)];			
			$value = call_user_func(array($entity, $methodeName));
			if(is_array($value)){
				$value = implode("__", $value);				
			}
			$statement->bindValue($binder, $value);
		}
		return $statement;
	}
	
	public function save($entity){
		$query = "INSERT INTO ".$this->table;
		$i = 1;		
		$arrayBind = $this->arrayBinding;
		$length = count($arrayBind);
		$columns = "";
		$values = "";
		foreach ($arrayBind as $key=>$binding){
			$columns.= $key ;
			$values.= ":".$key;
			if($i < $length){
				$columns.= "," ;
				$values.= ",";
			}
			$i++;
		}
		$query.= " ($columns) values ($values)";
		$statement = $this->bind($query, $entity);
		$this->_db->beginTransaction();
		return $statement->execute();
	}
	
	public function get($id){
		$id = (int)$id;	
		$query = "SELECT * FROM ".$this->table." WHERE ".$this->ID_COLUMN." = :id";	
		$entityListe = $this->select($query, $this->newInstanceEntity(array("id"=>$id)));
		return $entityListe[0];
	}
	
	public function getList(){
		$query = "SELECT * FROM ".$this->table;	
		return $this->select($query, null);
	}
	
	public function update($entity){
		$query = "UPDATE $this->table SET ";
		$arrayBind = $this->arrayBinding;
		$i = 1;
		$length = count($arrayBind);
		$querySet = "";
		foreach ($arrayBind as $key=>$binding){
			if($key != $this->ID_COLUMN){
				$querySet.= $key."=".$key;
				if($i < $length){
					$querySet.= "," ;
				}
				$i++;
			}
		}
		$query.= $querySet." where ".$this->ID_COLUMN."=:".$this->ID_COLUMN;
		$statement = $this->bind($query, $entity);
		$this->_db->beginTransaction();
		return $statement->execute();
	}	
	
	protected function find($query, $arrayCritere){
		$statement = $this->bind($query, $arrayCritere);
		$donnees = $statement->execute();
		$entityListe = array();
		while ($donnees = $statement->fetch(PDO::FETCH_ASSOC)){
			$entityListe[] = $this->newInstanceEntity($donnees);
		}
		return $entityListe;
	}
	
	protected function binder($statement, $query, $arrayCritere){
		$values = count($nameCritere);
		foreach($arrayCritere as $critere){
				
		}
		$criteria = sprintf("?%s", str_repeat(",?", ($values ? $values-1 : 0)));
		$query = sprintf("select * from ".$this->table." where categorie in (%s)", $criteria);
	}
}
?>
