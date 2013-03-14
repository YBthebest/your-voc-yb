<?php
require_once("controller.php");
require_once("modelDAO.php");
if(isset($_GET['q'])){
	$q = $_GET['q'];
	$liste = getListeByCategorie($q);
	$arr = array();
	$i = 0;
	foreach($liste as $result){
		$titre = $result->titre();
		$id = $result->id();
		$arr[$i]['titre'] = $titre;
		$arr[$i]['id'] = $id;
		$i++;
	}
	echo json_encode($arr);
}elseif(isset($_GET['l'])){
	$liste_id = $_GET['l'];
	$liste = getListeById($liste_id);
	$arr = array();
	$i = 0;
	foreach($liste as $result){
		$titre = $result->titre();
		$mots = $result->listeMot();
		$arr[$i]['titre'] = $titre;
		$arr[$i]['mots'] = $mots;
		$i++;
	}
	echo json_encode($arr);
}
?>
