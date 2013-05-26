<?php  
$critere = "titre";
if(isset($_POST['critere'])){
	$critere = mysql_real_escape_string($_POST['critere']);
}else if(isset($_GET['critere'])){
	$critere = mysql_real_escape_string($_GET['critere']);
}
$jsObject = "[]";
$search = (isset($_POST['requete']))?$_POST['requete']:@$_GET['requete'];
$search = htmlspecialchars($search);
$search = mysql_real_escape_string($search);
if(!empty($search)){
	$categorie = (isset($_POST['categorie']))?$_POST['categorie']:@$_GET['categorie'];
	$categorie = mysql_real_escape_string($categorie);
	$sur = (isset($_POST['sur']))?$_POST['sur']:@$_GET['sur'];
	$sur = mysql_real_escape_string($sur);
	
	$listesMot = rechercheByCriteres($categorie, $sur, $search, $critere, "0", "illimite");
	$jsObject = convertArrayObjectToJSArray($listesMot, "listeMot");
}
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
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div>
			<div id="title">Recherche</div>
			<?php
			if(!empty($search)) {
				if(!empty($listesMot)) {
			?>
				<div id="text-center">
					<h3>Résultats de votre recherche : </h3>
					<p>
						Nous avons trouvé <span id="totalListe"></span> dans notre base de données. 
						<br />
						Voici les listes que nous avons trouvées, classées par <span id="critere"><?php echo $critere ?></span>.
						<br /> 
						<a href="recherche">Faire une nouvelle recherche</a>
					<p>
				</div>	
				
				Trier Par : <select id="trier" style="margin:20px 0px;"></select>
				<div class="pagerContainer"></div>
				<div id="listesContainer"></div>
				<div class="pagerContainer"></div>
			<?php
				} else {
			?>
					<h3>Pas de résultats</h3>
					<p>
						Nous n'avons trouvé aucun résultat pour votre requête "<?php  echo stripslashes($search) ?>". <a href="recherche">Réessayez</a> avec autre chose.
					</p>
			<?php
				}
			} else { // et voilà le formulaire, en HTML de nouveau !
			?>
					<p>Vous allez faire une recherche dans notre base de données
						concernant les listes publiques.</p>
					<form action="recherche" method="Post">
						<p>
							Sur quelle catégorie souhaitez vous effectuer la recherche? 
							<select id="categorie" name="categorie"></select> 
							Faire la recherche sur :
							<select name="sur">
								<option value="titre">le titre des listes</option>
								<option value="mots">le contenu des listes</option>
								<option value="tous">les deux</option>
							</select>
							<br /> 
							<input type="text" name="requete" value="Mots-clés" size="30">
							<br /> 
							<input type="hidden" name="critere" value="vues">
							<input type="submit" value="Recherche">
						</p>
					</form>
			<?php } ?>		
		</div>
		
	</div>
</div>
