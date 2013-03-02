<?php 
$days = array(
		"janvier"=>"January",
		"février"=>"February",
		"mars"=>"March",
		"avril"=>"April",
		"mai"=>"May",
		"juin"=>"June",
		"juillet"=>"July",
		"août"=>"August",
		"septembre"=>"September",
		"octobre"=>"October",
		"novembre"=>"November",
		"décembre"=>"December"
);
$listeOk = false;
$updated = false;
$listes = array();
$executer = (isset($_GET['executer']))?$_GET['executer']:"";
if($executer !== ""){
	if($executer==="listes_public" || $executer==="update_listes_public"){
		$listes = getAllListe();
	}else if($executer==="commentaires" || $executer==="update_commentaires"){
		$listes = getAllCommentaires();
	}
}
foreach($listes as $res){
	$dateold = $res->timestamp();
	if(preg_match("/[a-zA-Z]+/", $dateold)){
		if(!preg_match("/ 201[0-9] /", $dateold)){
			$dateold = preg_replace("/([0-9]{2}:[0-9]{2}:[0-9]{2})/","2012 $1", $dateold);
		}
		$explode = explode(" ", $dateold);
		$date = $explode[1]." ".$days[$explode[2]]." ".$explode[3]." ".$explode[4];
		print_r("$dateold => $date = ");
		$date = strtotime($date);
		print_r("$date<br>");
		if(startswith($executer, "update")){
			$id = $res->id();
			$query = 'UPDATE '.substr($executer,strpos($executer, "_")+1).' SET date = "'.$date.'" WHERE id = "'.$id.'"';
			print_r($query);
			mysql_query($query)or die(mysql_error());
			$updated = "";
		}
		if(!startswith($executer, "update_")){
			$listeOk = $executer;
		}
	}else{
		print_r("$dateold<br>");
	}
}
?>
<html>
<form id="maj">
	<select name="executer">
		<option value="listes_public">listes mot</option>
		<option value="commentaires">commentaires</option>
		<?php if($listeOk){?>		
			<option value="update_<?php echo $listeOk;?>">update <?php echo $listeOk;?></option>
		<?php }?>
	</select>
	<input type="submit" value="executer"/>

	<?php if($updated){?>
		<div>la liste a été mise a jour</div>
	<?php } ?>
</form>
</html>