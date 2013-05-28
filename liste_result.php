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
	$query = "select * from listes_public where id = '$id'";
	$result = mysql_query($query);
    if(mysql_num_rows($result) != 0){
       	  $arr = array();
          $i = 0;
          while($result1 = mysql_fetch_array($result)){
       	  	$arr[$i]['id'] = $result1['id'];
     	    $arr[$i]['titre'] = $result1['titre'];
           	$arr[$i]['mots'] = $result1['liste'];
           	$arr[$i]['categorie'] = $result1['categorie'];
       		$arr[$i]['categorie2'] = $result1['categorie2'];
       		$i++;
       	}
        echo json_encode($arr);
    }
}
?>
