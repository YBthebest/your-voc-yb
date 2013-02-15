<?php
$valueSelected = "";
$jsObject = "";
if(isset($_GET['cat'])) {
	$id = addslashes($_GET['cat']);
	$id = mysql_real_escape_string($id);
	$categorie = getCategorieById($id);
	if(empty($categorie)){
		echo 'Veuillez préciser une catégorie existante.';
	}
	$valueSelected = $categorie->nom();
	$listesMot = getListeByCategorie($valueSelected);
	$jsObject = "[";
	$i = 0;
	$size = sizeof($listesMot);
	foreach($listesMot as $listeMot){
		$i++;
		$jsObject .= convertToJavascriptObject($listeMot);
		if($i < $size){
			$jsObject .= ",";
		}
	}
	$jsObject .= "]";
}

function convertToJavascriptObject($phpObject){
	$ref = new ReflectionObject($phpObject);
	$pros = $ref->getProperties(ReflectionProperty::IS_PRIVATE);
	$jsObject = "{";
	$i=0;
	$size = sizeof($pros);
	foreach ($pros as $property) {
		$i++;
		$property->setAccessible(true);
		$value = $property->getValue($phpObject);
		if(!preg_match('/\\n/', $value)){
			$jsObject .= $property->getName().':"'.$value.'"';
			if($i < $size){
				$jsObject .= ",";
			}
		}
	}
	$jsObject .= "}";
	return $jsObject;
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
  createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
	var listeTri = [{options:[
		{value:"categorie",text:"catégorie"},
		{value:"note",text:"note"},
		{value:"auteur",text:"auteur"},
		{value:"date",text:"date"},
		{value:"titre",text:"titre"}
	]}];
  var selectTri = createListeSelect("trier", listeTri);
  selectTri.onchange = function(){
	  var value = this.options[this.selectedIndex].value;
	  var alternate = 'titre';
	  if(value == alternate){
		  value = "categorie";
	  }
	  reversableSort(this, value, 'titre');
  };
  $("#categorie").val('<?php echo $valueSelected;?>');

  window.listeMotsDef = {
		listesMot:<?php echo $jsObject?>,
		currentPage:1,
		nbLimite:20
  }
  createListeByCateg(window.listeMotsDef.listesMot, window.listeMotsDef.currentPage , window.listeMotsDef.nbLimite);
});

function createListeByCateg(listesVocabulaires, currentPage, limite){
	var div = createElem({tag:"div"});
	limite = (listesVocabulaires.length>=limite)?limite:listesVocabulaires.length;
	var start = (currentPage-1)*limite;
	var position = start+limite;
	position = (position<=listesVocabulaires.length)?position:position-(position-listesVocabulaires.length);
	for(var i=start; i<position; i++){
		var listElemt = createListeElement(listesVocabulaires[i], i+1);
		div.appendChild(listElemt);
	}
	$("#listesContainer").append(div);
	div.appendChild(createPaginationElement(parseInt(listesVocabulaires.length/limite  + 0.9) , currentPage));
	return div;
}

function createListeElement(listeMotDef, index){
	var div = createElem({tag:"div"});
	index = (!index)?"":index;
	var elem = index+'.<b>'+
		listeMotDef.categorie+'<->'+listeMotDef.categorie2+
		': </b> <a href="afficher?id='+listeMotDef.id+'">'+
		listeMotDef.titre+'</a> (Note: '+listeMotDef.note+'/5 et '+
		listeMotDef.vue+' vues)<br /><small> par <a href="profil?m='+listeMotDef.membre+'">'+
		listeMotDef.membre+'</a> le '+listeMotDef.date+'</small><br /><br/>';
	div.innerHTML = elem;
	return div;
}

function pagineListesMot(page){
	$("#listesContainer").html("");
	createListeByCateg(window.listeMotsDef.listesMot, page, window.listeMotsDef.nbLimite);
	window.scrollTo(0,0);
}

function reversableSort(button){
	var i = 0;
	var reverse = false;	
	if(button){
		i = 1;
		reverse = (button.reverse)?button.reverse:false;
		button.reverse = !reverse;
		if(button.value.indexOf("/\\") != -1){
			button.value = button.value.replace("/\\","\\/");
		}else if(button.value.indexOf("\\/") != -1){
			button.value = button.value.replace("\\/","/\\");
		}
	}
	var props = new Array();
	for(i; i<arguments.length; i++){
		if(i == 1 && reverse){
			arguments[i] = "-" + arguments[i]
		}
		props.push(arguments[i]);
	}
	window.listeMotsDef.listesMot.sort(dynamicSortMultiple(props));
	pagineListesMot(1);
}

</script>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="title">Catégories</div>

		<form action="recherche" method="post">
			<p>
				Catégorie : <select id="categorie" name="categorie"></select> <br />Faire
				la recherche sur : <select name="sur">
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select> <input type="text" name="requete" value="Mots-clés"
					size="30"> <input type="submit" value="Recherche">
			</p>
		</form>
		<a href="entrer_liste">Entrer une nouvelle liste</a><br />

		<div>
			Trier par : <select id="trier"></select> 
		</div>
		<br />
		<div id="listesContainer"></div>
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
				<li><a href="<?php echo $categorie->url() ?>"><?php echo $categorie->nom()?>
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
				<li><a href="<?php echo $categorie->url() ?>"><?php echo $categorie->nom()?>
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
				<li><a href="<?php echo $categorie->url() ?>"><?php echo $categorie->nom()?>
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
				<li><a href="<?php echo $categorie->url() ?>"><?php echo $categorie->nom()?>
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
