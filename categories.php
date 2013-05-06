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
		listesMot:<?php echo $jsObject?>,
		currentPage:1,
		nbLimite:20
  };
  
  reversableSort(window.listeMotsDef.listesMot, "categorie", 'titre');
  var pager = new Pager(parseInt(window.listeMotsDef.listesMot.length/window.listeMotsDef.nbLimite  + 0.9), window.listeMotsDef.currentPage);
  $("#sliderContainer").before(pager.getContainer()); 
  $("#sliderContainer").after(pager.addPagerContainer());  

  createListeTri();
  
});

function createListeTri(){
	var listeTri = [{options:[
		{value:"categorie",text:"catégorie"},
		{value:"-note",text:"note"},
		{value:"-vue",text:"popularité"},
		{value:"membre",text:"auteur"},
		{value:"-timestamp",text:"date"},
		{value:"titre",text:"titre"}
	]}];
    var selectTri = createListeSelect("trier", listeTri);
    selectTri.onchange = function(){
  	  var value = this.options[this.selectedIndex].value;
  	  var alternate = 'titre';
  	  if(value == alternate){
  		  value = "categorie";
  	  }
  	  reversableSort(window.listeMotsDef.listesMot, value, 'titre');
  	  pagineur.select($("#page_1")[0]);
    };
}

function createListeByCateg(listesVocabulaires, currentPage, limite){
	var div = createElem({tag:"div"});
	limite = (listesVocabulaires.length>=limite)?limite:listesVocabulaires.length;
	var start = (currentPage-1)*limite;
	var position = start+limite;
	position = (position<=listesVocabulaires.length)?position:position-(position-listesVocabulaires.length);
	for(var i=start; i<position; i++){
		var listElemt = createListeMotElement(listesVocabulaires[i], i+1);
		div.appendChild(listElemt);
	}
	$("#listesContainer").append(div);
	$("#sliderContainer").css("height", $("#listesContainer").css("height"));
	return div;
}

function createListeMotElement(listeMotDef, index){
	var div = createElem({tag:"div"});
	index = (!index)?"":index;
	var note = (listeMotDef.note!="")?'Note: '+listeMotDef.note+'/5':"Pas de note";
	var elem = index+'.<b>'+
		listeMotDef.categorie+'<->'+listeMotDef.categorie2+
		': </b> <a href="afficher?id='+listeMotDef.id+'">'+
		listeMotDef.titre+'</a> (' + note + ' et '+
		listeMotDef.vue+' vues)<br /><small> par <a href="profil?m='+listeMotDef.membre+'">'+
		listeMotDef.membre+'</a> le '+listeMotDef.date+'</small><br /><br/>';
	div.innerHTML = elem;
	return div;
}

function pagineListesMot(page){
	$("#listesContainer").html("");
	createListeByCateg(window.listeMotsDef.listesMot, page, window.listeMotsDef.nbLimite);
}


function slidePage(allList, page, index){
	var elemContainer = $("#listesContainer");	
	elemContainer.css('width', elemContainer[0].offsetWidth);
/*	var slider = $("#sliderList");
	slider.css(elemContainer.css('width'));
	slider.css("overflow","hidden");
	slider.css("height",elemContainer[0].offsetHeight);*/
	elemContainer.css("float","left");
	elemContainer.css("position", "absolute");
	var nextContainer = elemContainer.clone();
	nextContainer.css('left', elemContainer[0].offsetWidth);
	elemContainer.attr("id","#listesContainerToDelete");	
	nextContainer.appendTo("#sliderList");
	pagineListesMot(page);
	elemContainer.animate({
		'left' : '-' + elemContainer[0].offsetWidth + 'px'
	});
	nextContainer.animate({
        'left' : '0px'
	},function(){
		elemContainer.remove();
	});
	$("#pagineur").focus();
}

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
