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
require "Metier/Favori.php";
require "Metier/FavoriManager.php";
require "Metier/Revision.php";
require "Metier/RevisionManager.php";
require "Metier/Vote.php";
require "Metier/VoteManager.php";
require "Metier/Combinaison.php";
require "Metier/CombinaisonManager.php";
require "Metier/MdpOublie.php";
require "Metier/MdpOublieManager.php";
require "Metier/Groupe.php";
require "Metier/GroupeManager.php";
require "Metier/MembreGroupe.php";
require "Metier/MembreGroupeManager.php";
require "Metier/DroitGroupe.php";
require "Metier/DroitGroupeManager.php";
require "Metier/DemandeMembreGroupe.php";
require "Metier/DemandeMembreGroupeManager.php";
require "Metier/ListesGroupe.php";
require "Metier/ListesGroupeManager.php";
require "Utils.php";
require "ConfigPage.php";

function dbconnect(){
	dbConfiguration();
    static $connect = null;
    if ($connect === null) {
		$connect = mysql_connect (getProperty('db.host.name'), getProperty('db.user.name'), getProperty('db.user.mdp'));	
		mysql_select_db (getProperty('db.name'));
    	mysql_set_charset( 'utf8' );
    }
    return $connect;
}

function dbPDO(){
    static $connect = null;
    if ($connect === null) {
		$dbhost = 'mysql:host='.getProperty('db.host.name').';dbname='.getProperty('db.name');
		$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
		$connect = new PDO($dbhost, getProperty('db.user.name'), getProperty('db.user.mdp'), $options);
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
	DBHelper::addManager(new CommentaireManager());
	DBHelper::addManager(new CombinaisonManager());
	DBHelper::addManager(new MdpOublieManager());
	DBHelper::addManager(new GroupeManager());
	DBHelper::addManager(new MembreGroupeManager());
	DBHelper::addManager(new DroitGroupeManager());
	DBHelper::addManager(new DemandeMembreGroupeManager());
	DBHelper::addManager(new ListesGroupeManager());
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
		$result = "Votre identifiant ou mot de passe est incorrect";
		if(count($liste) == 1){
			$result = $liste[0];
			if(md5($mdp) != $result->pass()){
				$result = "Votre identifiant ou mot de passe est incorrect";
			}
		}else if(count($liste) > 1){
			$result = "Une erreur dans notre base est survenue plusieurs membres portent le même login. Merci de contacter l'administrateur du site.";
		}
	}
	return $result;
}

function getMembreById($id){
	$membre = DBHelper::getDBManager("Membre")->getMembreById($id);
	return $membre;
}

function getMembreByLogin($login){
	$membre = DBHelper::getDBManager("Membre")->getMembreByLogin($login);
	if(sizeof($membre) > 0){
		$membre = $membre[0];
	}else{
		$membre = null;
	}
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
	//$configPage->setMetaContent($_ENV['metaContent']);
	//header("Content-Type: text/html; charset=utf-8");
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
function createNewCombinaison($id, $membre, $titre, $mots, $date){
	$newCombinaison = new Combinaison(array("id" => $id, "membre" => $membre, "titre" => $titre, "mots" => $mots, "date" => $date));
	DBHelper::getDBManager("Combinaison")->createNewCombinaison($id, $membre, $titre, $mots, $date);
	return $newCombinaison;
}
function deleteCombinaisonByIdAndMembre($id, $membre){
	$deleteCombi = new Combinaison(array("id"=>$id, "membre"=>$membre));
	DBHelper::getDBManager("Combinaison")->deleteCombinaisonByIdAndMembre($id, $membre);
	return $deleteCombi;
}
function getListeByTitreLikeKeyword($keyword){
	$liste = DBHelper::getDBManager("ListeMotDefinition")->getListeByTitreLikeKeyword($keyword);
	return $liste;
}
function getTokenNotUsedByPseudo($pseudo){
	$token = DBHelper::getDBManager("MdpOublie")->getTokenNotUsedByPseudo($pseudo);
	return $token;
}
function createToken($pseudo, $token, $date, $dateExpire){
	$newToken = new MdpOublie(array("pseudo" => $pseudo, "token" => $token, "date" => $date, "dateExpire" => $dateExpire));
	DBHelper::getDBManager("MdpOublie")->createToken($pseudo, $token, $date, $dateExpire);
	return $newToken;
}
function getMdpOublieByToken($token){
	$token = DBHelper::getDBManager("MdpOublie")->getMdpOublieByToken($token);
	return $token;
}
function updateUsedByTokenAndPseudo($token, $pseudo){
	$updateUsed = new MdpOublie(array("token"=>$token, "pseudo"=>$pseudo));
	DBHelper::getDBManager("MdpOublie")->updateUsedByTokenAndPseudo($token, $pseudo);
	return $updateUsed;
}
function getGroupeById($id){
	$groupe = DBHelper::getDBManager("Groupe")->getGroupeById($id);
	return $groupe;
}
function createGroupe($nom, $idCreateur, $date, $droitMembres, $urlAuto){
	$newGroupe = new Groupe(array("nom" => $nom, "idCreateur" => $idCreateur, "timestamp"=> $date, "droitMembres" => $droitMembres, "urlAuto" => $urlAuto));
	DBHelper::getDBManager("Groupe")->createGroupe($nom, $idCreateur, $date, $droitMembres, $urlAuto);
	return $newGroupe;
}
function getGroupeByNom($nom){
	$groupe = DBHelper::getDBManager("Groupe")->getGroupeByNom($nom);
	return $groupe;
}
function getGroupeByNomAndCreateur($nom, $idCreateur){
	$groupe = DBHelper::getDBManager("Groupe")->getGroupeByNomAndCreateur($nom, $idCreateur);
	return $groupe;
}
function getLastDemandeByPseudoAndIdGroupe($pseudo, $idGroupe){
	$demande = DBHelper::getDBManager("DemandeMembreGroupe")->getLastDemandeByPseudoAndIdGroupe($pseudo, $idGroupe);
	return $demande;
}
function getMembreByIdGroupeAndMembre($idGroupe, $idMembre){
	$membre = DBHelper::getDBManager("MembreGroupe")->getMembreByIdGroupeAndMembre($idGroupe, $idMembre);
	return $membre;
}
function createDemande($idGroupe, $idMembre){
	$newDemande = new DemandeMembreGroupe(array("idGroupe" => $idGroupe, "idMembre" => $idMembre));
	DBHelper::getDBManager("DemandeMembreGroupe")->createDemande($idGroupe, $idMembre);
	return $newDemande;
}
function createMembreGroupe($idMembre, $idGroupe, $idDroit){
	$newMembreGroupe = new MembreGroupe(array("idMembre" => $idMembre, "idGroupe" => $idGroupe, "idDroit" => $idDroit));
	DBHelper::getDBManager("MembreGroupe")->createMembreGroupe($idMembre, $idGroupe, $idDroit);
	return $newMembreGroupe;
}
function createDroit($libelle, $idMembre, $idGroupe){
	$newDroit = new DroitGroupe(array("libelle" => $libelle, "idMembre" => $idMembre, "idGroupe" => $idGroupe));
	DBHelper::getDBManager("DroitGroupe")->createDroit($libelle, $idMembre, $idGroupe);
	return $newDroit;
}
function getDroitByIdMembreAndIdGroupe($idMembre, $idGroupe){
	$droit = DBHelper::getDBManager("DroitGroupe")->getDroitByIdMembreAndIdGroupe($idMembre, $idGroupe);
	return $droit;
}
function getDemandePendingByIdGroupe($idGroupe){
	$demande = DBHelper::getDBManager("DemandeMembreGroupe")->getDemandePendingByIdGroupe($idGroupe);
	return $demande;
}
function updateDemandeByStatut($idGroupe, $idMembre, $statut){
	$newDemande = new DemandeMembreGroupe(array("idGroupe" => $idGroupe, "idMembre" => $idMembre));
	DBHelper::getDBManager("DemandeMembreGroupe")->updateDemandeByStatut($idGroupe, $idMembre, $statut);
	return $newDemande;
}
function getMembresByIdGroupe($idGroupe){
	$membre = DBHelper::getDBManager("MembreGroupe")->getMembresByIdGroupe($idGroupe);
	return $membre;
}
function deleteMembreGroupe($idMembre, $idGroupe){
	$deleteMembreGroupe = new MembreGroupe(array("idMembre" => $idMembre, "idGroupe" => $idGroupe));
	DBHelper::getDBManager("MembreGroupe")->deleteMembreGroupe($idMembre, $idGroupe);
	return $deleteMembreGroupe;
}
function deleteDroit($idMembre, $idGroupe){
	$deleteDroit = new DroitGroupe(array("idMembre" => $idMembre, "idGroupe" => $idGroupe));
	DBHelper::getDBManager("DroitGroupe")->deleteDroit($idMembre, $idGroupe);
	return $deleteDroit;
}
function getDemandeByStatut($statut){
	$demande = DBHelper::getDBManager("DemandeMembreGroupe")->getDemandeByStatut($statut);
	return $demande;
}
function deleteDemande($idGroupe, $idMembre){
	$newDemande = new DemandeMembreGroupe(array("idGroupe" => $idGroupe, "idMembre" => $idMembre));
	DBHelper::getDBManager("DemandeMembreGroupe")->deleteDemande($idGroupe, $idMembre);
	return $newDemande;
}
function updateDroitLibelle($idMembre, $idGroupe, $libelle){
	$updateDroit = new DroitGroupe(array("idMembre" => $idMembre, "idGroupe" => $idGroupe));
	DBHelper::getDBManager("DroitGroupe")->updateDroitLibelle($idMembre, $idGroupe, $libelle);
	return $updateDroit;
}
function getMembreGroupeByIdMembre($idMembre){
	$membre = DBHelper::getDBManager("MembreGroupe")->getMembreGroupeByIdMembre($idMembre);
	return $membre;
}
function deleteAllMembreGroupe($idGroupe){
	$deleteMembreGroupe = new MembreGroupe(array("idGroupe" => $idGroupe));
	DBHelper::getDBManager("MembreGroupe")->deleteAllMembreGroupe($idGroupe);
	return $deleteMembreGroupe;
}
function deleteAllDemandeByGroupe($idGroupe){
	$newDemande = new DemandeMembreGroupe(array("idGroupe" => $idGroupe));
	DBHelper::getDBManager("DemandeMembreGroupe")->deleteAllDemandeByGroupe($idGroupe);
	return $newDemande;
}
function deleteAllDroitByGroupe($idGroupe){
	$deleteDroit = new DroitGroupe(array("idGroupe" => $idGroupe));
	DBHelper::getDBManager("DroitGroupe")->deleteAllDroitByGroupe($idGroupe);
	return $deleteDroit;
}
function deleteGroupe($id, $idCreateur){
	$deleteGroupe = new Groupe(array("idCreateur" => $idCreateur));
	DBHelper::getDBManager("Groupe")->deleteGroupe($id, $idCreateur);
	return $deleteGroupe;
}
function updateNomGroupe($id, $idCreateur, $nom){
	$updateGroupe = new Groupe(array("idCreateur" => $idCreateur, "nom" => $nom));
	DBHelper::getDBManager("Groupe")->updateNomGroupe($id, $idCreateur, $nom);
	return $updateGroupe;
}
function updateDroitMembresGroupe($id, $idCreateur, $droitMembres){
	$updateGroupe = new Groupe(array("idCreateur" => $idCreateur, "droitMembres" => $droitMembres));
	DBHelper::getDBManager("Groupe")->updateDroitMembresGroupe($id, $idCreateur, $droitMembres);
	return $updateGroupe;
}
function createListeGroupe($id_liste, $id_membre, $id_groupe, $date){
	$newListeGroupe = new ListesGroupe(array());
	DBHelper::getDBManager("ListesGroupe")->createListeGroupe($id_liste, $id_membre, $id_groupe, $date);
	return $newListeGroupe;
}
function getListesGroupeByIdGroupe($id_groupe){
	$listesGroupe = DBHelper::getDBManager("ListesGroupe")->getListesGroupeByIdGroupe($id_groupe);
	return $listesGroupe;
}
function deleteListeGroupe($id, $idMembre, $idGroupe){
	$deleteListeGroupe = new ListesGroupe(array("id" => $id, "idMembre" => $idMembre, "idGroupe" => $idGroupe));
	DBHelper::getDBManager("ListesGroupe")->deleteListeGroupe($id, $idMembre, $idGroupe);
	return $deleteListeGroupe;
}
function deleteAllListesByGroupe($idGroupe){
	$deleteListes = new ListesGroupe(array("idGroupe" => $idGroupe));
	DBHelper::getDBManager("ListesGroupe")->deleteAllListesByGroupe($idGroupe);
	return $deleteListes;
}
function deleteAllCommentairesByIdMembre($id_membre){
	$deleteCommentaires = new Commentaire(array("membre" => $id_membre));
	DBHelper::getDBManager("Commentaire")->deleteAllCommentairesByIdMembre($id_membre);
	return $deleteCommentaires;
}
function deleteAllDemandeByMembre($idMembre){
	$newDemande = new DemandeMembreGroupe(array("idMembre" => $idMembre));
	DBHelper::getDBManager("DemandeMembreGroupe")->deleteAllDemandeByMembre($idMembre);
	return $newDemande;
}
function deleteAllDroitByMembre($idMembre){
	$deleteDroit = new DroitGroupe(array("idMembre" => $idMembre));
	DBHelper::getDBManager("DroitGroupe")->deleteAllDroitByMembre($idMembre);
	return $deleteDroit;
}
function deleteAllFavorisByMembre($idMembre){
	$deleteFavori = new Favori(array("membre" => $idMembre));
	DBHelper::getDBManager("Favori")->deleteAllFavorisByMembre($idMembre);
	return $deleteFavori;
}
function deleteAllCombinaisonsByMembre($idMembre){
	$deleteCombi = new Combinaison(array("membre" => $idMembre));
	DBHelper::getDBManager("Combinaison")->deleteAllCombinaisonsByMembre($idMembre);
	return $deleteCombi;
}
function deleteAllListesGroupeByMembre($idMembre){
	$deleteListes = new ListesGroupe(array("idMembre" => $idMembre));
	DBHelper::getDBManager("ListesGroupe")->deleteAllListesGroupeByMembre($idMembre);
	return $deleteListes;
}
function deleteAllMembresGroupeByMembre($idMembre){
	$deleteMembreGroupe = new MembreGroupe(array("idMembre" => $idMembre));
	DBHelper::getDBManager("MembreGroupe")->deleteAllMembresGroupeByMembre($idMembre);
	return $deleteMembreGroupe;
}
function deleteAllRevisionsByMembre($idMembre){
	$deleteRevisions = new Revision(array("membre" => $idMembre));
	DBHelper::getDBManager("Revision")->deleteAllRevisionsByMembre($idMembre);
	return $deleteRevisions;
}
function deleteAllVotesByMembre($idMembre){
	$deleteVotes = new Vote(array("membre" => $idMembre));
	DBHelper::getDBManager("Vote")->deleteAllVotesByMembre($idMembre);
	return $deleteVotes;
}
function updateMembreListe($old_membre, $new_membre){
	$updateListe = new ListeMotDefinition(array("membre" => $old_membre));
	DBHelper::getDBManager("ListeMotDefinition")->updateMembreListe($old_membre, $new_membre);
	return $updateListe;
}
function deleteMembreById($id){
	$deleteMembre = new Membre(array("id" => $id));
	DBHelper::getDBManager("Membre")->deleteMembreById($id);
	return $deleteMembre;
}
function getGroupeByIdCreateur($idCreateur){
	$groupe = DBHelper::getDBManager("Groupe")->getGroupeByIdCreateur($idCreateur);
	return $groupe;
}
?>