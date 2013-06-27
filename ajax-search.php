<?php
require_once("controller.php");
require_once("modelDAO.php");

if(isset($_GET['keyword'])){
    $keyword = trim($_GET['keyword']) ;
$keyword = mysql_real_escape_string($keyword);
$keyword = htmlspecialchars($keyword);
$query = "select * from listes_public where titre like '%$keyword%'";
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
    }else {
        echo 'No Results for :"'.$_GET['keyword'].'"';
    }
}elseif(isset($_GET['groupe'])){
	$g_keyword = trim($_GET['groupe']) ;
	$g_keyword = mysql_real_escape_string($g_keyword);
	$g_keyword = htmlspecialchars($g_keyword);
	$query = "select * from groupe where nom like '%$g_keyword%'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) != 0){
		$arr = array();
		$i = 0;
		while($result1 = mysql_fetch_array($result)){
			$arr[$i]['id'] = $result1['id'];
			$arr[$i]['nom'] = $result1['nom'];
			$arr[$i]['date'] = $result1['date'];
			$i++;
		}
		echo json_encode($arr);
	}else {
		echo 'No Results for :"'.$_GET['groupe'].'"';
	}
}else {
    echo 'Parameter Missing';
}
?>