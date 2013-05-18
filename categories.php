<?php
	$valueSelected = "aucune";
	$id = (isset($_GET['cat']))?intval(addslashes($_GET['cat'])):-1;
	if($id !== -1){
		$id = mysql_real_escape_string($id);
		$categorie = getCategorieById($id);
		$valueSelected = $categorie->nom();
		$listesMot = getListeByCategorie($valueSelected);
	}else{
		$listesMot = getAllListe();
	}
	
	$jsObject = convertArrayObjectToJSArray($listesMot, "listeMot");
	//print_r($jsObject);
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
  createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
  $("#categorie").val('<?php echo $valueSelected;?>');
  
  window.listeMotsDef = {
		liste:<?php echo $jsObject?>,
		currentPage:1,
		nbPerPage:20,
		defaultSort:"titre",
  };
  
  reversableSort(window.listeMotsDef.liste, "categorie", 'titre');
  window.pager = new Pager(window.listeMotsDef, createListeByCateg, slidePage);
  $("#sliderContainer").before(pager.getContainer()); 
  $("#sliderContainer").after(pager.addPagerContainer());  

  createListForSortListeMot(window.listeMotsDef, pager);
  
});

</script>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="title">
			<?php 
			if(isset($valueSelected)){
			if($valueSelected=='aucune'){
				$nom="Catégories";
			}else{
				$nom=$valueSelected;
			}
		}else{
			$nom="Catégories";
		}
		echo $nom;
		?>
		</div>

		<form action="recherche" method="post"
			onsubmit="var req=$('#requete');if(req.val()=='Mots-clés'){req.val('')}">
			<p>
				Catégorie : <select id="categorie" name="categorie"></select> <br />Faire
				la recherche sur : <select name="sur">
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select> <input type="text" id="requete" name="requete"
					value="Mots-clés" size="30" /> <input type="submit"
					value="Recherche" />
			</p>
		</form>
		<a href="entrer_liste">Entrer une nouvelle liste</a><br />

		<div>
			Trier par : <select id="trier"></select>
		</div>
		<br />
		<div id="sliderContainer">
			<div id="sliderList"
				style="position: absolute; width: 100%; height: 100%">
				<div id="listesContainer"></div>
			</div>
		</div>
		<?php
		$groupe = getGroupesCategorie();
		?>
		<div id="left">
			<h2>
				<?php echo $groupe["1"];?>
			</h2>
			<ul type="circle">
				<?php
				foreach(getCategorieByGeneral("1") as $categorie){
					?>
				<li><a href="categories?cat=<?php echo $categorie->id() ?>"><?php echo $categorie->nom()?>
				</a> - <?php 
				$cat = $categorie->nom();
				$retour = getNbListeByCategorie($cat);
				?> (<i><?php echo $retour ?> listes </i>)<br /></li>
				<?php
				}
				?>
			</ul>
			<h2>
				<?php echo $groupe["3"];?>
			</h2>
			<ul type="circle">
				<?php
				foreach(getCategorieByGeneral("3") as $categorie){
						?>
				<li><a href="categories?cat=<?php echo $categorie->id() ?>"><?php echo $categorie->nom()?>
				</a> - <?php 
				$cat = $categorie->nom();
				$retour = getNbListeByCategorie($cat);?> (<i><?php echo $retour ?>
						listes </i>)<br /></li>
				<?php
					}
					?>
			</ul>
		</div>
		<div id="right">
			<h2>
				<?php echo $groupe["2"];?>
			</h2>
			<ul type="circle">
				<?php
				foreach(getCategorieByGeneral("2") as $categorie){
						?>
				<li><a href="categories?cat=<?php echo $categorie->id() ?>"><?php echo $categorie->nom()?>
				</a> - <?php $cat = $categorie->nom();
				$retour = getNbListeByCategorie($cat);?> (<i><?php echo $retour ?>
						listes </i>)<br /></li>
				<?php
					}
					?>
			</ul>
			<h2>
				<?php echo $groupe["4"];?>
			</h2>
			<ul type="circle">
				<?php
				foreach(getCategorieByGeneral("4") as $categorie){
						?>
				<li><a href="categories?cat=<?php echo $categorie->id() ?>"><?php echo $categorie->nom()?>
				</a> - <?php $cat = $categorie->nom();
				$retour = getNbListeByCategorie($cat);?> (<i><?php echo $retour ?>
						listes </i>)<br /></li>
				<?php
					}
					?>
			</ul>
		</div>
	</div>
</div>
