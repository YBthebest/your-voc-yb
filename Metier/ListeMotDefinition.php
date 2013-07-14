<?php
class ListeMotDefinition extends Entity{
	public $titre;
	public $membre;
	public $listeMot = array();	
	public $timestamp;
	public $date;
	public $categorie;
	public $categorie2;
	public $note;
	public $vue;
	public $commentaire;
	public static $separator = " ";
	
	public function __construct (){
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
        $this->listeMot = $this->convertMotsToArray($listeMot);
	}
	
	private function convertMotsToArray($listeMot){		
		$normalize = str_replace("\\r\\n", "__", $listeMot);
		if(strrpos($normalize, "\r\n")  !== false){
			$normalize = str_replace("\r\n", "__", $listeMot);
		}
		if(strrpos($normalize, "\\n")  !== false){
			$normalize = str_replace("\\n", "__", $listeMot);
		}else if(strrpos($normalize, "\n")  !== false){
			$normalize = str_replace("\n", "__", $listeMot);
		}
		$normalize = trim($normalize, "__");
		return explode("__", $normalize);
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
	public function timestamp(){
		return $this->timestamp;
	}
	public function setTimestamp($timestamp){
		$this->date = timestampToString($timestamp);
		$this->timestamp = stringDateToTimestamp($timestamp);
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
}
?>
