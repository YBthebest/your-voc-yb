<?php
	$jsObject = "[]";
	$categorie = (isset($_POST['categorie']))?$_POST['categorie']:@$_GET['categorie'];
	$categorie = mysql_real_escape_string($categorie);
	$sur = (isset($_POST['sur']))?$_POST['sur']:@$_GET['sur'];
	$sur = mysql_real_escape_string($sur);

	$listesMot = rechercheByCriteres("", "titre", "", "vues", "0", "illimite");
	$jsObject = convertArrayObjectToJSArray($listesMot, "listeMot");
?>

<script type="text/javascript">
$(function(){
  	if($("#categorie").length == 1){
	 	createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
  	}
  	
	var liste = <?php echo $jsObject?>;
  	initializeContextResult(liste);
});

function initializeContextResult(listeObject){
  	window.listeMotsDef = {
  			liste:listeObject,
  			currentPage:1,
  			nbPerPage:20,
  			containerListeCreator : createListeByCateg,
  			elementCreator : createListeMotElementRecherche,
  			idListeContainer : "listesContainer",
  			classPagerContainer : "pagerContainer",
  			pageChanger : slidePage,
  			defaultSort:"titre",
  	};
  	var totalListe = listeMotsDef.liste.length ;
  	$("#totalListe").html(totalListe + " résultat" + ((totalListe.length>1)?"s":""));
  	window.pager = new Pager(window.listeMotsDef);
  	createListForSortListeMot("trier", window.listeMotsDef, pager);
}
</script>
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
            <div id="title">Toutes les listes </div>
			<a href="entrer_liste" >Entrer une nouvelle liste</a><br />
			<a href="recherche" >Faire une recherche</a><br />
			<form action="recherche" method="Post">
				<p>
				Catégorie?<select id="categorie" name="categorie"></select>
				<br />Faire la recherche sur : 
				<select name="sur" >
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select>
				<input type="text" name="requete" value="Mots-clés" size="30">
				<input type="submit" value="Recherche">
				</p>
			</form>
		</div>
		
		Trier par : <select id="trier" style="margin:20px 0px;"></select>

		<div class="pagerContainer"></div>
		<div id="listesContainer"></div>
		<div class="pagerContainer"></div>
	</div>
</div>