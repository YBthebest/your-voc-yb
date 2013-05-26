<?php 
require_once("controller.php");
require_once("modelDAO.php");
if(isset($_GET['action'])){
	if(isset($_POST['mots']) AND !empty($_POST['mots'])){
		$mots = mysql_real_escape_string($_POST['mots']);
		$titre = mysql_real_escape_string($_POST['titre']);
		$membre = mysql_real_escape_string($_POST['membre']);
		$idListe = $_POST['idListe'];
		$idListeTotal = implode(",", $idListe);
		$date = time();
		if(createNewCombinaison($idListeTotal, $membre, $titre, $mots, $date)){
			echo 'success';
		}else{
			echo 'fail';
		}
	}
}
?>