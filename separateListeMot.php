<?php
if(isset($_POST['OK'])){
	$listes = getAllListe();
	foreach($listes as $result_listes){
		$id_liste = $result_listes->id();
		$liste = $result_listes->listeMot();
		$lignes = 0;
		$lignes = explode("\n", $liste);
		$nombre_lignes = 0;
		$nombre_lignes = count($lignes);
		$mot_present = 0 ;
		$question = array();
		$o = 0;
		for( $i = 0 ; $i < $nombre_lignes ; $i++) {
			// on separe les 2 mots
			$mot = explode("=", $lignes[$i]);
			//Si utilisateur a correcctement utiliser , il aura 2 mot
			// Si mal fait , on ignore cette ligne
			if( count($mot) == 2 ) {
				// On retire les espace que utilisateur a peut etre laisser
				$mot[0] = trim($mot[0]);    //l1
				$mot[1] = trim($mot[1]);	//l2
				if(mysql_query("INSERT INTO mots VALUES('', '$id_liste', '$mot[0]', '$mot[1]')")){
		echo 'ok';
	}
			}
		}
	}
}
?>
<html>
<form method="POST" name="convert">
<input type="button" value="OK" name="OK" />
</form>
</html>
