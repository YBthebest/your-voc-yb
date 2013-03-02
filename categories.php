<?php
$valueSelected = "aucune";
$id = (isset($_GET['cat']))?addslashes($_GET['cat']):-1;
if($id != -1){
	$id = mysql_real_escape_string($id);
	$categorie = getCategorieById($id);
	$listesMot = getListeByCategorie($valueSelected);
	$valueSelected = $categorie->nom();
}else{
	$listesMot = getAllListe();
}
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
			$jsObject .= $property->getName().':"'.mysql_real_escape_string($value).'"';
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
		{value:"membre",text:"auteur"},
		{value:"timestamp",text:"date"},
		{value:"titre",text:"titre"}
	]}];
  var selectTri = createListeSelect("trier", listeTri);
  selectTri.onchange = function(){
	  var value = this.options[this.selectedIndex].value;
	  var alternate = 'titre';
	  if(value == alternate){
		  value = "categorie";
	  }
	  if("note" == value){
		  value = "-"+value;
	  }
	  reversableSort(this, value, 'titre');
  };
  $("#categorie").val('<?php echo $valueSelected;?>');
  window.listeMotsDef = {
		listesMot:<?php echo $jsObject?>,
		currentPage:1,
		nbLimite:20
  }
  reversableSort(selectTri, "categorie", 'titre');
  $("#sliderContainer").after(createPaginationElement(parseInt(window.listeMotsDef.listesMot.length/window.listeMotsDef.nbLimite  + 0.9), window.listeMotsDef.currentPage));

});

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
	elemContainer.css("float","left");
	elemContainer.css("position", "absolute");
	var nextContainer = elemContainer.clone();
	nextContainer.appendTo("#sliderList");
	nextContainer.css('left', elemContainer[0].offsetWidth);
	nextContainer.css('width', elemContainer[0].offsetWidth);
	elemContainer.attr("id","#listesContainerToDelete");
	pagineListesMot(page);
	elemContainer.animate({
		'left' : '-' + elemContainer[0].offsetWidth + 'px'
	});
	nextContainer.animate({
        'left' : '0px'
	},function(){
		elemContainer.remove();
	});
	$("#listesContainer").focus();
}

function reversableSort(button){
	var i = 0;
	var props = new Array();
	var reverse = false;
	for(i; i<arguments.length; i++){
		if(i == 1 && reverse){
			arguments[i] = "-" + arguments[i];
		}
		props.push(arguments[i]);
	}
	window.listeMotsDef.listesMot.sort(dynamicSortMultiple(props));
	pagineListesMot(1);
}

function createPaginationElement(nbPage, currentPage){	
	var pagineur = createElem({tag:"p", id:"pagineur"});		
	pagineur.style.cssText = "height:20px;margin-bottom:20px;";	
	pagineur.childDefaultStyle = "float:left; text-align:center; margin:2px; cursor:pointer; height:20px; color:#be3737";
	pagineur.select = function(elem){
		if(this.selected){
			this.selected.innerHTML = this.selected.value;
			this.selected.style.cssText = this.childDefaultStyle;	
		}
		elem.innerHTML = "[ "+elem.value+" ]";
		elem.style.color = "white";
		this.selected = elem;
	}
	addPaginerChild("<<", "first", 1, pagineur);
	for(var i=1; i<=nbPage; i++){
		pageSelector = addPaginerChild(i, "page_"+i, i, pagineur);
		if(i == currentPage){
			pagineur.select(pageSelector);
		}
	}
	addPaginerChild(">>", "last", nbPage, pagineur);
	
	return pagineur;
}

function addPaginerChild(text, id, numPage, paginer){
	var pageSelector = createElem({tag:"div", id:id});
	pageSelector.style.cssText = paginer.childDefaultStyle;
	pageSelector.appendChild(createElem({tag:"text", text:text}));
	pageSelector.value = text;
	pageSelector.onclick = (function(p_numPage){
		return function (){
			paginer.select($('#page_'+p_numPage)[0]);
			var current = 0;
			if(paginer.selected){
				current = paginer.selected.value
			}
			slidePage(window.listeMotsDef.listesMot, p_numPage, current);
			//pagineListesMot(page);
		};
	})(numPage);
	paginer.appendChild(pageSelector);
	return pageSelector;
}


</script>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="title">Catégories</div>

		<form action="recherche" method="post" onsubmit="var req=$('#requete');if(req.val()=='Mots-clés'){req.val('')}">
			<p>
				Catégorie : <select id="categorie" name="categorie"></select> <br />Faire
				la recherche sur : <select name="sur">
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select> 
				<input type="text" id="requete" name="requete" value="Mots-clés" size="30" />
				<input type="submit" value="Recherche" />
			</p>
		</form>
		<a href="entrer_liste">Entrer une nouvelle liste</a><br />

		<div>
			Trier par : <select id="trier"></select> 
		</div>
		<br />
		<div id="sliderContainer">
			<div id="sliderList" style="position:absolute;">
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
