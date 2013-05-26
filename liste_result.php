<?php
require_once("controller.php");
require_once("modelDAO.php");
if(isset($_GET['q'])){
	$q = $_GET['q'];
	$liste = getListeByCategorie($q);
	$arr = array();
	$i = 0;
	foreach($liste as $result){
		$arr[$i]['id'] = $result->id();
		$arr[$i]['titre'] = $result->titre();
		$arr[$i]['mots'] = $result->listeMot();
		$arr[$i]['categorie'] = $result->categorie();
		$arr[$i]['categorie2'] = $result->categorie2();
		$arr[$i]['pseudo'] = $result->membre();
		$i++;
	}
	echo json_encode($arr);
}elseif(isset($_GET['id'])){
	$id = $_GET['id'];
	$result = getListeById($id);
	$arr[0]['id'] = $result->id();
	$arr[0]['titre'] = $result->titre();
	$arr[0]['mots'] = $result->listeMot();
	$arr[0]['categorie'] = $result->categorie();
	$arr[0]['categorie2'] = $result->categorie2();
	$arr[0]['pseudo'] = $result->membre();
	echo json_encode($arr);
}
?>
