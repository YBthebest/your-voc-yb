<?php
require "Metier/DBHelper.php";
require "Metier/Entity.php";
require "Metier/DbManager.php";
require "Metier/Categorie.php";
require "Metier/CategorieManager.php";
require "Metier/ListeMotDefinition.php";
require "Metier/ListeMotDefinitionManager.php";
require "Metier/Membre.php";
require "Metier/MembreManager.php";
require "Metier/Commentaire.php";
require "Metier/CommentaireManager.php";
require "Metier/Erreur.php";
require "Metier/ErreurManager.php";
require "Metier/Favori.php";
require "Metier/FavoriManager.php";
require "Metier/Revision.php";
require "Metier/RevisionManager.php";
require "Metier/Vote.php";
require "Metier/VoteManager.php";
require "Metier/Combinaison.php";
require "Metier/CombinaisonManager.php";
require "Utils.php";
require "ConfigPage.php";

function dbconnect(){
	dbConfiguration();
    static $connect = null;
    if ($connect === null) {
		$connect = mysql_connect (getProperty('db.host.name'), getProperty('db.user.name'), getProperty('db.user.mdp'));
		mysql_select_db (getProperty('db.name'));
    }
    return $connect;
}

function dbPDO(){
    static $connect = null;
    if ($connect === null) {
		$dbhost = 'mysql:host='.getProperty('db.host.name').';dbname='.getProperty('db.name');
		$connect = new PDO($dbhost, getProperty('db.user.name'), getProperty('db.user.mdp'));
    }
    return $connect;
}

function dbConfiguration(){
	DBHelper::addManager(new CategorieManager());
	DBHelper::addManager(new ListeMotDefinitionManager());
	DBHelper::addManager(new MembreManager());
	DBHelper::addManager(new VoteManager());
	DBHelper::addManager(new RevisionManager());
	DBHelper::addManager(new FavoriManager());
	DBHelper::addManager(new ErreurManager());
	DBHelper::addManager(new CommentaireManager());
	DBHelper::addManager(new CombinaisonManager());
}

function getProperty($propertyName){
	$filename = "config.properties";
	global $props;
	if (file_exists($filename)) {
		$props = parse_ini_file($filename);
	}else{
		echo "fichier de propriété '".$filename."' introuvable<br>";
		die();
	}
	return $props[$propertyName];
}


function getGroupesCategorie(){
	$groupeCategorie = array("1"=>"Europe","2"=>"Asie","3"=>"Europe de l'Est", "4"=>"Europe de l'Est");
	return $groupeCategorie;
}

function getJsCategorieListe(){
	$catByGroupe = getCategoriesByGroupe();
	$igroupe = sizeof($catByGroupe);
	$groupeListejs = "";
	foreach ($catByGroupe as $key => $categories){
		$igroupe--;
		$icat = sizeof($categories);
		$categorieListejs = "";
		foreach ($categories as $categorie){
			$icat--;
			$categorieListejs .= '{value:"'.$categorie.'",text:"'.$categorie.'"}';
			if($icat > 0){
				$categorieListejs .= ",";
			}
		}
		$groupeListejs .= '{label:"'.$key.'",options:['.$categorieListejs.']}';
		if($igroupe > 0){
			$groupeListejs.=",";
		}
	}
	$javascriptObject = "[$groupeListejs]";
	return $javascriptObject;
}

function getCategoriesByGroupe(){
	$categories = getCategories();
	$catByGroupe = array();
	$groupe="";
	$groupeIndex="";
	$groupeArray = getGroupesCategorie();
	$i = 0;
	foreach ($categories as $categorie){
		if($groupeIndex != $categorie->groupe()){
			$groupeIndex = $categorie->groupe();
			$groupe = $groupeArray[$groupeIndex];
			$catByGroupe[$groupe] = array();
		}
		$catByGroupe[$groupe][] = $categorie->nom();
	}
	return $catByGroupe;
}

function getMembre($login, $mdp){
	$result = "";
	if(empty($login)){
		$result = "Vous devez renseigner votre login.";
	}
	if(empty($mdp)){
		$result .= "Vous devez renseigner votre mot de passe.";
	}
	if(empty($result)){
		$liste = DBHelper::getDBManager("Membre")->getMembreByLogin($login);
		$result = "Votre identifiant est inconnu, merci de vous inscrire pour vous connecter";
		if(count($liste) == 1){
			$result = $liste[0];
			if(md5($mdp) != $result->pass()){
				$result = "Votre mot de passe est incorrect";
			}
		}else if(count($liste) > 1){
			$result = "Une erreur dans notre base est surevenu plusieur membres porte le même login. Merci de contacter l'administrateur du site.";
		}
	}
	return $result;
}

function getMembreById($id){
	$membre = DBHelper::getDBManager("Membre")->getMembreById($id);
}

function getMembreByLogin($login){
	$membre = DBHelper::getDBManager("Membre")->getMembreByLogin($login);
	return $membre;
}

function getMembreByEmail($email){
	$membre = DBHelper::getDBManager("Membre")->getMembreByEmail($email);
	return $membre;
}

function getNombreListeMot(){
	$nombre = DBHelper::getDBManager("ListeMotDefinition")->countAll();
	return $nombre;
}

function getNombreMembre(){
	DBHelper::getDBManager("Membre")->countAll();
}

function getNombreCategorie(){
	DBHelper::getDBManager("Categorie")->countAll();
}

function getNombreRevision(){
	DBHelper::getDBManager("Revision")->countAll();
}

function getConfigPage(){
	$configPage = new ConfigPage();
	$configPage->setPageName(getPage());
	initTitle($configPage->pageName());
	$configPage->setTitle($_ENV['title']);
	$configPage->setMetaContent($_ENV['metaContent']);
	header("Content-Type: text/html; charset=utf-8");
	setlocale(LC_TIME, 'fr_FR.utf8','fra');
	if($_SERVER['SERVER_NAME']){
		ini_set('display_errors',1);
	}
	return $configPage;
}

function insertListeMot($login, $listeMots, $titre, $timestamp, $categorie, $categorie2, $commentaire, $vues, $note){
	$listeMot = new ListeMotDefinition();
	$listeMot->setId('');
	$listeMot->setMembre($login);
	$listeMot->setListeMot($listeMots);
	$listeMot->setTitre($titre);
	$listeMot->setDate($timestamp);
	$listeMot->setCategorie($categorie);
	$listeMot->setCategorie2($categorie2);
	$listeMot->setCommentaire($commentaire);
	$listeMot->setNote($note);
	$listeMot->setVue($vues);
	return DBHelper::getDBManager("ListeMotDefinition")->save($listeMot);
}

function getCategoriesWithNbListe($nb=0){
	$categories = getCategories($nb);
	$managerListeMot = DBHelper::getDBManager("ListeMotDefinition");
	foreach($categories as $categorie){
		$categorie->setNbListe($managerListeMot->getNbListeByCategorie($categorie->nom()));
	}
	return $categories;
}

function getCategories($nb=0){
	$categories = getLimiteListe(DBHelper::getDBManager("Categorie"), $nb);
	return $categories;
}
function getCategoriesByName($arrayName){
	return $categories = DBHelper::getDBManager("Categorie")->getCategoriesByName($arrayName);
}

function getListesMotDefinitionByDate($nb=0){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getList();
	usort($liste, function($a, $b) {
	    return $a->id() < $b->id() ? 1 : -1;
	});
	if($nb > 0){
		$liste = array_slice($liste ,0,$nb);
	}
	return $liste;
}

function comparator($a, $b){
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}

function getListeMotByCritere(array $critere){
	return DBHelper::getDBManager("ListeMotDefinition")->getListeByCritere($critere);
}

function getLimiteListe($manager, $nb=0){
	$liste = $manager->getList();
	if($nb > 0){
		$liste = array_slice($liste ,0,$nb);
	}
	return $liste;	
}

function getPage(){
	$filename = "accueil.php";
	if(isset($_GET['page'])){
		$filename = $_GET['page'].".php";
		if(!file_exists($filename)){
			$filename = "error.php";
		}
	}
	return $filename;
}

function get_comment($news_id){
}

function insert_comment($comment){
}

function getCategorieByName($name){
	$categorie = DBHelper::getDBManager("Categorie")->getCategorieByName($name);
	return $categorie;
}
function getCategorieById($id){
	$categorie = DBHelper::getDBManager("Categorie")->getCategorieById($id);
	return $categorie;
}
function getListeByPseudoLimit3($pseudo){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeByPseudoLimit3($pseudo);
	return $liste;
}
function getListeByPseudo($pseudo){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeByPseudo($pseudo);
	return $liste;
}
function getListeById($id){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeById($id);
	return $liste;
}

function updateListeMot($listeMotDefinition){
	DBHelper::getDBManager("ListeMotDefinition")->update($listeMotDefinition);
}

function getNbListeByCategorie($nomCategorie){
	$NbListe = DBHelper::getDBManager("ListeMotDefinition")->getNbListeByCategorie($nomCategorie);
	return $NbListe;
}
function getCategorieByGeneral($id){
	$categorie = DBHelper::getDBManager("Categorie")->getCategorieByGeneral($id);
	return $categorie;
}
function getListeByCategorie($categorie){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeByCategorie($categorie);
	return $liste;	
}
function getNbListe(){
	$NbListe = DBHelper::getDBManager("ListeMotDefinition")->getNbListe();
	return $NbListe;
}
function getAllListe(){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getList();
	return $liste;
}
function getListeOrderByVues(){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeOrderByVues();
	return $liste;
}
function getPassByLogin($login){
	$membre = DBHelper::getDBManager("Membre")->getPassByLogin($login);
	return $membre;
}
function rechercheByCriteres($categorie, $recherche_sur, $mots_cles, $critere, $premiereEntree, $messageParPage){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->rechercheByCriteres($categorie, $recherche_sur, $mots_cles, $critere, $premiereEntree, $messageParPage);
	return $liste;
}
function createMembre($pseudo, $email, $password){
	$membre = new Membre(array("login"=>$pseudo, "pass_md5"=>$password, "email"=>$email));
	DBHelper::getDBManager("Membre")->saveMembre($pseudo, $email, $password);
	return $membre;
}
function getVotesById($id){
	$votes = DBHelper::getDBManager("Vote")->getVotesById($id);
	return $votes;
}
function createVote($id_liste, $note, $pseudo){
	$vote = new Vote(array("id_liste"=>$id_liste, "note"=>$note, "pseudo"=>$pseudo));
	DBHelper::getDBManager("Vote")->createVote($id_liste, $note, $pseudo);
	return $vote;
}
function getVotesByIdAndPseudo($id, $pseudo){
	$votes = DBHelper::getDBManager("Vote")->getVotesByIdAndPseudo($id, $pseudo);
	return $votes;
}
function createFavori($id_liste, $pseudo){
	$favori = new Favori(array("id_liste"=>$id_liste, "pseudo"=>$pseudo));
	DBHelper::getDBManager("Favori")->createFavori($id_liste, $pseudo);
	return $favori;
}
function updateNoteInListe($id_liste, $note){
	$liste = new ListeMotDefinition(array("id_liste"=>$id_liste, "note"=>$note));
	DBHelper::getDBManager("ListeMotDefinition")->updateNoteInListe($id_liste, $note);
	return $liste;
}
function getFavoriByIdAndPseudo($id_liste, $membre){
	$favori = DBHelper::getDBManager("Favori")->getFavoriByIdAndPseudo($id_liste, $membre);
	return $favori;
}
function deleteFavoriByIdAndMembre($id_liste, $membre){
	$favori = new Favori(array("id_liste"=>$id_liste, "membre"=>$membre));
	DBHelper::getDBManager("Favori")->deleteFavoriByIdAndMembre($id_liste, $membre);
	return $favori;
}
function countNbCommentairesById($id_liste){
	$nbCommentaire = DBHelper::getDBManager("Commentaire")->countNbCommentairesById($id_liste);
	return $nbCommentaire;	
}
function getCommentairesById($id_liste){
	$commentaires = DBHelper::getDBManager("Commentaire")->getCommentairesById($id_liste);
	return $commentaires;	
}
function createCommentaire($id_liste, $pseudo, $time, $commentaire){
	$createCommentaire = new Commentaire(array("id_liste"=>$id_liste, "pseudo"=>$pseudo, "date"=>$time, "commentaire"=>$commentaire));
	DBHelper::getDBManager("Commentaire")->createCommentaire($id_liste, $pseudo, $time, $commentaire);
	return $createCommentaire;
}
function deleteListeByIdAndPseudo($id, $pseudo){
	$deleteListe = new ListeMotDefinition(array("id_liste"=>$id, "pseudo"=>$pseudo));
	DBHelper::getDBManager("ListeMotDefinition")->deleteListeByIdAndPseudo($id, $pseudo);
	return $deleteListe;	
}
function updateListe($mot, $categorie, $categorie2, $titre, $id, $pseudo, $commentaire){
	$updateListe = new ListeMotDefinition(array("id"=>$id, "pseudo"=>$pseudo, "categorie"=>$categorie, "categorie2"=>$categorie2, "liste"=>$mot, "titre"=>$titre, "commentaire"=>$commentaire));
	DBHelper::getDBManager("ListeMotDefinition")->updateListe($mot, $categorie, $categorie2, $titre, $id, $pseudo, $commentaire);
	return $updateListe;	
}
function updateMdpByLogin($mdp, $pseudo){
	$updateMdp = new Membre(array("pass_md5"=>$mdp, "pseudo"=>$pseudo));
	DBHelper::getDBManager("Membre")->updateMdpByLogin($mdp, $pseudo);
	return $updateMdp;	
}
function getRevisionsByPseudoLimit20($pseudo){
	$revisions = DBHelper::getDBManager("Revision")->getRevisionsByPseudoLimit20($pseudo);
	return $revisions;	
}
function getFavoriByPseudo($membre){
	$favori = DBHelper::getDBManager("Favori")->getFavoriByPseudo($membre);
	return $favori;
}
function getFavoriByPseudoLimit20($membre){
	$favori = DBHelper::getDBManager("Favori")->getFavoriByPseudoLimit20($membre);
	return $favori;
}
function getFavoriByPseudoLimit50($membre){
	$favori = DBHelper::getDBManager("Favori")->getFavoriByPseudoLimit50($membre);
	return $favori;
}
function getRevisionsByPseudoLimit3($pseudo){
	$revisions = DBHelper::getDBManager("Revision")->getRevisionsByPseudoLimit3($pseudo);
	return $revisions;
}
function getCombinaisonByPseudoLimit5($pseudo){
	$combinaisons = DBHelper::getDBManager("Combinaison")->getCombinaisonByPseudoLimit5($pseudo);
	return $combinaisons;
}
function getCombinaisonByPseudoLimit15($pseudo){
	$combinaisons = DBHelper::getDBManager("Combinaison")->getCombinaisonByPseudoLimit15($pseudo);
	return $combinaisons;
}
function getAllCommentaires(){
	$commentaires = DBHelper::getDBManager("Commentaire")->getAllCommentaires();
	return $commentaires;
}
function getAllRevisions(){
	$revisions = DBHelper::getDBManager("Revision")->getAllRevisions();
	return $revisions;
}

function createNewRevision($id, $membre, $moyenne, $time){
	$newRevision = new Revision(array("id_liste" => $id, "membre" => $membre, "moyenne" => $moyenne, "date" => $time));
	DBHelper::getDBManager("Revision")->CreateNewRevision($id, $membre, $moyenne, $time);
	return $newRevision;
}
function createNewErreur($id_liste, $type, $message, $pseudo, $time){
	$newErreur = new Erreur(array("id_liste" => $id_liste, "membre" => $pseudo, "type" => $type, "message" => $message, "date" => $time));
	DBHelper::getDBManager("Erreur")->createNewErreur($id_liste, $type, $message, $pseudo, $time);
	return $newErreur;
}
?>
