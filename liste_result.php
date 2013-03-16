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
		$arr[$i]['id'] = $result->id();
		$arr[$i]['titre'] = $result->titre();
		$arr[$i]['mots'] = $result->listeMot();
		$i++;
	}
	echo json_encode($arr);
}
?>
