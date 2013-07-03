<?php
if(isset($_POST['executer'])){
	$table = $_POST['executer'];
	if($table == 'favoris'){
		mysql_query("ALTER TABLE `favoris` ADD `id_membre` TEXT NOT NULL ");
		$requete = mysql_query("SELECT * FROM favoris");
		while($result = mysql_fetch_array($requete)){
			$pseudo = $result['membre'];
			$requete1 = mysql_query("SELECT * FROM membre WHERE login = '$pseudo'");
			while($result1 = mysql_fetch_array($requete1)){
				$id = $result1['id'];
				mysql_query("UPDATE favoris SET id_membre = '$id' WHERE membre = '$pseudo'");
			}
		}
		mysql_query("ALTER TABLE 'favoris' DROP 'membre'");
	}
	else{
		$requete = 'SELECT * FROM '.$table.'';
		$requete = mysql_query($requete);
		while($result = mysql_fetch_array($requete)){
			$pseudo = $result['pseudo'];
			$requete1 = mysql_query("SELECT * FROM membre WHERE login = '$pseudo'");
			while($result1 = mysql_fetch_array($requete1)){
				$id = $result1['id'];
				$requete2 = 'UPDATE '.$table.' SET pseudo = "'.$id.'" WHERE pseudo = "'.$pseudo.'"';
				mysql_query($requete2);
			}
		}
		
		mysql_query('ALTER TABLE '.$table.' change pseudo id_membre TEXT NOT NULL');
	}
	echo "Succes";
}
?>
<form id="maj" method="POST">
	<select name="executer">
		<option value="listes_public">listes mot</option>
		<option value="commentaires">commentaires</option>
		<option value="revise">revise</option>
		<option value="combiner">combiner</option>
		<option value="vote">vote</option>
		<option value="favoris">favoris</option>
		<option value="mdp_oublie">mdp_oublie</option>
	</select>
	<input type="submit" value="executer"/>
</form>