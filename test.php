<?php 
$listes = getAllListe();
foreach($listes as $res){
	$date = $res->date();
	if(strpos($date, '2012') == false){
		$date .=  " 2012";
		$id = $res->id();
		mysql_query('UPDATE listes_public SET date = "'.$date.'" WHERE id = "'.$id.'"')or die(mysql_error());	
	}
}
?>