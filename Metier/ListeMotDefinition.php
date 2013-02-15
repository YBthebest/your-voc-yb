<?php
class ListeMotDefinition extends Entity{
	private $titre;
	private $membre;
	private $listeMot;// = array();	
	private $date;
	private $categorie;
	private $categorie2;
	private $note;
	private $vue;
	private $commentaire;
	private static $separator = " ";
	
	public function __construct ($donnees=array()){
		if(isset($donnees['id']))$this->setId($donnees['id']);
		if(isset($donnees['titre']))$this->setTitre($donnees['titre']);
		if(isset($donnees['pseudo']))$this->setMembre($donnees['pseudo']);
		if(isset($donnees['date'])){
			if(!preg_match("(.*\s2[0-9]{3}])", $donnees['date'])){
				$donnees['date'] .=  " 2012";
			}
			$this->setDate($donnees['date']);
		}
		if(isset($donnees['liste'])){
			//$this->listeMot = explode($separator, $donnees['listeMot']);
			$this->setListeMot($donnees['liste']);
		}
		if(isset($donnees['categorie']))$this->setCategorie($donnees['categorie']);
		if(isset($donnees['categorie2']))$this->setCategorie2($donnees['categorie2']);
		if(isset($donnees['note']))$this->setNote($donnees['note']);
		if(isset($donnees['vues']))$this->setVue($donnees['vues']);
		if(isset($donnees['commentaire']))$this->setCommentaire($donnees['commentaire']);
	}        
	
    public function setDatas(array $donnees){
    }
    
	public function id(){
		return $this->id;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function listeMot(){
		return $this->listeMot;
	}
	public function setListeMot($listeMot){
		if ($listeMot == null) {
                trigger_error('La liste de mot ne doit pas être null', E_USER_WARNING);
                return;
        }
		$this->listeMot = $listeMot;
	}
	public function titre(){
		return $this->titre;
	}
	public function setTitre($titre){
		$this->titre = $titre;
	}
	public function date(){
		return $this->date;
	}
	public function setDate($date){
		$this->date = $date;
	}
	public function categorie(){
		return $this->categorie;
	}
	public function setCategorie($categorie){
		$this->categorie = $categorie;
	}
	public function categorie2(){
		return $this->categorie2;
	}
	public function setCategorie2($categorie2){
		$this->categorie2 = $categorie2;
	}
	public function note(){
		return $this->note;
	}
	public function setNote($note){
		$this->note = $note;
	}
	public function vue(){
		return $this->vue;
	}
	public function setVue($vue){
		$this->vue = $vue;
	}
	public function commentaire(){
		return $this->commentaire;
	}
	public function setCommentaire($commentaire){
		$this->commentaire = $commentaire;
	}
	public function membre(){
		return $this->membre;
	}
	public function setMembre($membre){
		$this->membre = $membre;
	}
}
?>
