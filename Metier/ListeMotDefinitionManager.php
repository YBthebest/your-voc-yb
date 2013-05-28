<?php
class ListeMotDefinitionManager extends DbManager {
	protected $ID_COLUMN = "id";
	protected $table = "listes_public";
	protected $entityName = "ListeMotDefinition";
	
	public function __construct(){
		parent::__construct();
	}
	
	protected function binding(){
		$this->arrayBinding[$this->ID_COLUMN] = "id";
		$this->arrayBinding["pseudo"] = "membre";
		$this->arrayBinding["titre"] = "titre";
		$this->arrayBinding["liste"] = "listeMot";
		$this->arrayBinding["date"] = "date";
		$this->arrayBinding["categorie"] = "categorie";
		$this->arrayBinding["categorie2"] = "categorie2";
		$this->arrayBinding["note"] = "note";
		$this->arrayBinding["vues"] = "vue";
		$this->arrayBinding["commentaire"] = "commentaire";
	}
	
	protected function newInstanceEntity($donnees){
		$entity = new ListeMotDefinition();
		if(isset($donnees['id']))$entity->setId($donnees['id']);
		if(isset($donnees['titre']))$entity->setTitre($donnees['titre']);
		if(isset($donnees['pseudo']))$entity->setMembre($donnees['pseudo']);
		if(isset($donnees['date'])){
			$entity->setTimestamp($donnees['date']);
		}
		if(isset($donnees['liste'])){
			//$this->listeMot = explode($separator, $donnees['listeMot']);
			$entity->setListeMot($donnees['liste']);
		}
		if(isset($donnees['categorie']))$entity->setCategorie($donnees['categorie']);
		if(isset($donnees['categorie2']))$entity->setCategorie2($donnees['categorie2']);
		if(isset($donnees['note']))$entity->setNote($donnees['note']);
		if(isset($donnees['vues']))$entity->setVue($donnees['vues']);
		if(isset($donnees['commentaire']))$entity->setCommentaire($donnees['commentaire']);
		return $entity;
	}
	
	public function getListeByKeyWord($keyWord){
		$requete = explode(" ", $requete);
		$query = "Select * from ".$this.table." where ";
		return $this->select($query);
	}
	
	public function getNbListeByCategorie($nomCategorie){
		$query = "SELECT * FROM ".$this->table." WHERE categorie = :categorie OR categorie2 = :categorie2";
		$listMotDef = new ListeMotDefinition();
		$listMotDef->setCategorie($nomCategorie);
		$listMotDef->setCategorie2($nomCategorie);
		return $this->count($query, $listMotDef);
	}
	
	public function getNbListe(){
		$query = "SELECT * FROM ".$this->table."";
		$entity = $this->newInstanceEntity(array());
		return $this->count($query, $entity);
	}
	
	public function getListeByCritere($critere){
		$query = "SELECT * FROM ".$this->table." WHERE ";
		$datas = array();		
		$entityCritere = new ListeMotDefinition(array());
		if(isset($critere['titre'])){
			$query.="titre like :titre";
			$entityCritere->setTitre("%".$critere['titre']."%");
		}
		if(isset($critere['categorie'])){
			$query.=" or categorie = :categorie";
			$entityCritere->setCategorie($critere['categorie']);
		}
		if(isset($critere['categorie2'])){
			$query.=" or categorie2 = :categorie2";
			$entityCritere->setCategorie2($critere['categorie2']);
		}
		return $this->select($query, $entityCritere);
	}
	public function getListeByPseudoLimit3($pseudo){
		$query = "select * from ".$this->table." where pseudo = :pseudo ORDER BY id DESC LIMIT 3";
		$entity = new ListeMotDefinition();
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);
	}
	public function getListeByPseudo($pseudo){
		$query = "select * from ".$this->table." where pseudo = :pseudo";
		$entity = new ListeMotDefinition();
		$entity->setMembre($pseudo);
		return $this->select($query, $entity);
	}
	public function getListeById($id){
		$query = "select * from ".$this->table." where id = :id";
		$entity = new ListeMotDefinition();
		$entity->setId($id);
		return $this->selectUniqueResult($query, $entity);		
	}
	public function getListeByCategorie($categorie){
		$query = "select * from ".$this->table." where categorie = :categorie or categorie2 = :categorie2";
		$entity = new ListeMotDefinition();
		$entity->setCategorie($categorie);
		$entity->setCategorie2($categorie);
		return $this->select($query, $entity);
	}
	public function getListeOrderByVues(){
		$query = "select * from ".$this->table." order by (vues + 0)";
		$entity = new ListeMotDefinition();
		return $this->select($query, $entity);		
	}
	public function rechercheByCriteres($categorie, $recherche_sur, $mots_cles, $critere, $premiereEntree, $messagesParPage){
		$categorie = $categorie;
		$requete1 = htmlspecialchars(addslashes($mots_cles)); // on crée une variable $requete pour faciliter l'écriture de la requête SQL, mais aussi pour empêcher les éventuels malins qui utiliseraient du PHP ou du JS, avec la fonction htmlspecialchars().
		$requete = explode(" ", $requete1);
		$number = count($requete);
		$query_made = "";
		for( $i = 0 ; $i < $number ; $i++) {
			$query_made .= $requete[$i];
			$query_made .= "%";
		}
		$array_decompose = array(
			"1" => "SELECT * FROM listes_public ",
			"2" => "WHERE titre LIKE '%$query_made' ",
			"2a" => "WHERE liste LIKE '%$query_made' ",
			"2b" => "WHERE (liste LIKE '%$query_made' OR titre LIKE '%$query_made') ",
			"3" => "AND (categorie = '$categorie' OR categorie2 = '$categorie') ",
			"3a" => "",
			"4" => "ORDER BY note DESC ",
			"4a" => "ORDER BY (vues + 0) DESC ",
			"4b" => "ORDER BY pseudo DESC ",
			"4c" => "ORDER BY date DESC ",
			"5" => "LIMIT $premiereEntree, $messagesParPage",
			"5a" => ""
		);
		$partie1 = $array_decompose['1'];
		if(isset($recherche_sur)){
			if($recherche_sur == 'titre') { $partie2 = $array_decompose['2']; } elseif($recherche_sur == 'mots'){ $partie2 = $array_decompose['2a']; } elseif($recherche_sur == 'tous'){ $partie2 = $array_decompose['2b']; } else{ $partie2 = $array_decompose['2']; }
		}
		else {
			$partie2 = $array_decompose['2'];
		}
		if(getCategorieByName($categorie) != null){ $partie3 = $array_decompose['3']; } else{ $partie3 = $array_decompose['3a']; }
		if(isset($critere)){
			if($critere == 'note'){ $partie4 = $array_decompose['4']; } elseif($critere == 'vues'){ $partie4 = $array_decompose['4a']; } elseif($critere == 'pseudo'){ $partie4 = $array_decompose['4b']; } elseif($critere == 'date'){ $partie4 = $array_decompose['4c']; } else{ $partie4 = $array_decompose['4a']; }
		}
		else {
			$partie4 = $array_decompose['4a'];
		}
		if($messagesParPage == "illimite"){
			$partie5 = $array_decompose['5a'];
		} else{
			$partie5 = $array_decompose['5'];
		}
		$query = $partie1.$partie2.$partie3.$partie4.$partie5;
		$entity = new ListeMotDefinition();
		return $this->select($query, $entity);
	}
	public function updateNoteInListe($id_liste, $note){
		$query = "update ".$this->table." set note = '".$note."' where id = '".$id_liste."'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function deleteListeByIdAndPseudo($id, $pseudo){
		$query = "delete from ".$this->table." where id = '".$id."' and pseudo = '".$pseudo."'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function updateListe($mot, $categorie, $categorie2, $titre, $id, $pseudo, $commentaire){
		$query = "UPDATE ".$this->table." SET liste = '$mot', titre = '$titre', categorie = '$categorie', categorie2 = '$categorie2', commentaire = '$commentaire' WHERE id = '$id' AND pseudo = '$pseudo'" ;
		$statement = $this->_db->prepare($query);
		$statement->execute();
	}
	public function getListeByTitreLikeKeyword($keyword){
		$query = "select * from ".$this->table." where titre like '%$keyword%'";
		$entity = new ListeMotDefinition();
		return $this->select($query, $entity);	
	}
}
?>
