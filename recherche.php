<?php  
$critere = "titre";
if(isset($_POST['critere'])){
	$critere = $_POST['critere'];
}else if(isset($_GET['critere'])){
	$critere = mysql_real_escape_string($_GET['critere']);
}
$jsObject = "[]";
$search = (isset($_POST['requete']))?$_POST['requete']:@$_GET['requete'];
if(!empty($search)){
	$categorie = (isset($_POST['categorie']))?$_POST['categorie']:@$_GET['categorie'];
	$sur = (isset($_POST['sur']))?$_POST['sur']:@$_GET['sur'];

	$messagesParPage=20;
	$listesMot = rechercheByCriteres($categorie, $sur, $search, $critere, "0", "illimite");
	$total= sizeof($listesMot);
	$nombreDePages=ceil($total/$messagesParPage);
	
	// A Supprimer					
		if(isset($_GET['num_page']) && is_numeric($_GET['num_page'])) {
			$pageActuelle=intval($_GET['num_page']);
			// Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
			if($pageActuelle>$nombreDePages) {
				$pageActuelle=$nombreDePages;
			}
		} else {
			$pageActuelle=1; // La page actuelle est la n°1
		}
	$start = ($pageActuelle-1) * $messagesParPage;
	$end = $start + $messagesParPage;
		//$premiereEntree=($pageActuelle-1)*$messagesParPage; // On calcul la première entrée à lire
		//$listesMot = rechercheByCriteres($categorie, $sur, $search, $critere, "0", "illimite");
		
	// Fin A Supprimer
	

	$jsObject = convertArrayObjectToJSArray($listesMot, "listeMot");
	
	$pager = "";
	for($i=1; $i<=$nombreDePages; $i++) {
		if($i==$pageActuelle) {
			$pager .= ' [ '.$i.' ] ';
		} else {
			$pager .= ' <a href="recherche?num_page='.$i.'&requete='.$search.'&categorie='.$categorie.'&sur='.$sur.'&critere='.$critere.'">'.$i.'</a> ';
		}
	}
}
?>

<script type="text/javascript">
$(function(){
	var save='';
  	$('input[type="text"]').each(function(){
    	this.onfocus=function(){
      		save=this.value;
      		this.value='';
    	};
    	this.onblur=function(){
      		this.value= this.value==='' ? save : this.value;
    	};
  	});
  	if($("#categorie").length == 1){
	 	createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
  	}

  	window.listeMotsDef = {
  			liste:<?php echo $jsObject?>,
  			currentPage:1,
  			nbPerPage:20,
  			containerListeCreator : createListeByCateg,
  			elementCreator : createListeMotElementRecherche,
  			pageChanger : slidePage,
  			defaultSort:"titre",
  	  };
  	  
  	  window.pager = new Pager(window.listeMotsDef);
	//$("#sliderContainer").before(pager.getContainer()); 
	//$("#sliderContainer").after(pager.addPagerContainer());  
	
  	if($("#critere").length > 0){	  
	 	$("#critere option[value='<?php  echo $critere;?>']").attr("selected", "selected");
  	}
});
</script>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
			<div id="title">Recherche</div>
			<?php
			if(!empty($search)) {
				if(!empty($listesMot)) {
			?>
				<div>
					<h3>Résultats de votre recherche.</h3>
					<p>
						Nous avons trouvé <?php echo $total ?> résultat<?php echo ($total > 1)?"s":""; ?>
						dans notre base de données. Voici les listes que nous avons
						trouvées, classées par <?php echo $critere ?> :
						<br /> 
						<a href="recherche">Faire une nouvelle recherche</a><br />
						<p align="center">Page :<?php echo $pager ?></p>
				
						<form method="post" action="recherche?nb_page=<?php echo $i ?>&requete=<?php echo $search ?>&categorie=<?php echo $categorie ?>&sur=<?php echo $sur ?>&critere=<?php echo $critere ?>">
							<input type="hidden" name="requete" value="<?php echo $search ?>" />
							<input type="hidden" name="sur" value="<?php echo $sur ?>" /> 
							<input type="hidden" name="categorie" value="<?php echo $categorie ?>" />
							Trier par : 
							<select name="critere" id="critere" onchange='this.form.submit()'>
								<option value="titre">Titre</option>
								<option value="note">Note</option>
								<option value="vue">Popularité</option>
								<option value="pseudo">Auteur</option>
								<option value="date">Date de mise en ligne</option>
							</select>
						</form>
						<br />
					<p>
				</div>				
				<div style="text-align: left;">
					<?php
						$subListe = array_slice($listesMot, $start, $end);
						foreach($subListe as $donnees) {
							echo "".++$start.".";
					?>
							<a href="afficher?id=<?php echo $donnees->id(); ?>"><?php echo $donnees->titre(); ?>
							</a> <small>entré le <?php echo $donnees->date() ?><br /> par <a href="profil?m=<?php echo $donnees->membre()?>"><?php echo $donnees->membre() ?>
							</a> dans les catégories <?php echo $donnees->categorie() ?> <-> <?php echo $donnees->categorie2() ?>
								(<?php echo $donnees->note() ?>/5) (<?php echo $donnees->vue() ?> vues)
							</small><br /> <br />
					<?php
							$i++;
						}
					?>
				</div>
				<div id="text-center">
					<p align="center">Page :<?php echo $pager ?></p>
					<br /> <br /> <a href="recherche">Faire une nouvelle recherche</a>
				</div>
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
		
		<div id="listesContainer"></div>
	</div>
</div>
