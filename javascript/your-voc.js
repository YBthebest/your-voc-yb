/*
Document : your-voc.js
Created on : 23 avr. 2010
Author : Gannon
Mofify on : 04 oct. 2011
Author : Loïc BIGOT
*/

Array.prototype.indexOf = function(obj){
	var l = this.length;
	for(var i=0; i<l; i++){
		if(this[i] == obj){
			return i;
		}
	}
	return -1;
};

Array.prototype.remove = function(obj){
	var l = this.length;
	var replace = false;
	for(var i=0; i<l; i++){
		if(this[i] == obj){
			replace = true;
		}
		if(replace && i<l){
			this[i] = this[i+1];
		}
	}
	if(replace){
		this.pop();
	}
};

Array.prototype.insert = function(p_index, newObj){
	var l = this.length;
	var precObj = null;
	for(var i=0; i<l; i++){
		if(precObj != null){
			var obj = precObj;
			precObj = this[i];
			this[i] = obj;
		}else if(i == p_index){
			precObj = this[i];
			this[i] = newObj;
		}
	}
	if(precObj != null){
		this.push(precObj);
	}else{
		this.push(newObj);
	}
};
function initRevise(){
	initProperties();
	if(formulaire){
		buttonValidError.style.display = 'none';
		document.onkeypress = onPress ;
		//On cache les question sauf le 1er
		indiceReponseListSort = new Array();
		for( var i =0 ; i < divs.length ; i++){
			if(i == 0){
				divs[i].style.display = 'inline';
				divs[i].getElementsByTagName('input')[0].focus();
			}
			indiceReponseListSort.push(i);
		}
		formulaire.indiceReponse = 0 ;
		formulaire.bon = 0 ;
		formulaire.faux = 0 ;
	}
}
function initProperties(){
	window.formulaire = document.getElementById('formulaire');
	window.divs = getElementsByClass('QuestionMot','span');
	window.infoScore = document.getElementById('infoScore');
	window.buttonValidError = document.getElementById('valideError');
	window.buttonSoumettre = document.getElementById('soumettre');
	window.prevReponse = null;
}
function onPress(e){
	e = e || window.event;
	if(e.keyCode == 13) { // Touche entrée
		// On ne valide pas le formulaire
		if(e.preventDefault) {
			e.preventDefault();
		} else {
			e.returnValue = false; // Pour ie
		}
		// Mais Passe a la suite
		nextMots();
		return false ;
	}
}
function isValideReponse(reponse, solution){
	var listeSoluce = solution.split("/");
	for(var i=0; i<listeSoluce.length; i++){
		if(reponse == listeSoluce[i]){
			return true;
		}
	}
	return false;
}

function nextMots(){
	var blockQuestion = getBlockQuestion(indiceReponseListSort[0]);
	if(!caseSensitive){
		blockQuestion.reponse = blockQuestion.reponse.toLowerCase();
		blockQuestion.solution = blockQuestion.solution.toLowerCase();
	}
	withWrong = false;
	if( blockQuestion.reponse != ""){
		cacheElement(infoScore);
		if(isValideReponse(blockQuestion.reponse, blockQuestion.solution)){
			cacheElement(buttonValidError);
			formulaire.bon++ ;
			afficheScoreJuste(blockQuestion.question, blockQuestion.solution);
		}else{
			withWrong = true;
			montreElement(buttonValidError);
			formulaire.faux++ ;
			if(modeFullSuccess){
				var indice = 3;
				if(indiceReponseListSort.length < (indice+1)){
					indice = indiceReponseListSort.length - 1;
				}
				indiceReponseListSort.insert(3, blockQuestion.index);
			}
			afficheScoreFaux(blockQuestion.reponse, blockQuestion.question, blockQuestion.solution);
		}
		indiceReponseListSort.shift();
	}
	manageDisplay(blockQuestion.index, indiceReponseListSort[0], withWrong);
	prevReponse = blockQuestion;
	if(indiceReponseListSort.length == 0){
		montreElement(buttonSoumettre);
	}
}
function getBlockQuestion(p_index){
	var inpS = divs[p_index].getElementsByTagName('input');
	var blockQuestion = {
		index : p_index,
		block : inpS,
		reponse : inpS[0].value,
		question : inpS[1].value,
		solution : inpS[2].value
	};
	return blockQuestion;
}
function cacheElement(p_elem){
	p_elem.style.display = 'none';
}
function montreElement(p_elem){
	p_elem.style.display = 'inline';
}
function afficheScoreJuste(question, solution){
	infoScore.innerHTML = "<div id=\"revision_juste\"><span style=\"color: White;\">" + question+ " = " + solution + "</span></div>";
	displayNote();
}
function afficheScoreFaux(reponse, question, solution){
	infoScore.innerHTML = "<div id=\"revision_faux\"> <span style=\"color: White;\">''" + reponse + "'' ne veut pas dire ''"+ question +"''. La bonne réponse était: ''" + solution + "''</span></div>" ;
	displayNote();
}
function valideReponseFausse(){
	prevReponse.block[0].value = prevReponse.solution;
	formulaire.faux-- ;
	formulaire.bon++ ;
	if(modeFullSuccess){
		indiceReponseListSort.remove(prevReponse.index);
	}
	cacheElement(buttonValidError);
	afficheScoreJuste(prevReponse.question, prevReponse.solution);
	if(indiceReponseListSort.length == 0){
		montreElement(buttonSoumettre);
	}else{
		var blockQuestion = getBlockQuestion(indiceReponseListSort[0]);
		blockQuestion.block[0].value = "";
		nextMots();
	}
}
function manageDisplay(indiceCache, indiceMontre, withWrong){
	if(divs[indiceCache]){
		if(withWrong){
			var nbFaux = parseInt($("#nbFaux_"+indiceCache).val());
			$("#nbFaux_"+indiceCache).val(nbFaux + 1);
		}
		divs[indiceCache].style.display = 'none' ;
	}
	if(divs[indiceMontre]){
		divs[indiceMontre].style.display = 'inline' ;
		divs[indiceMontre].getElementsByTagName('input')[0].focus();
	}
}
function displayNote(){
	var faux = formulaire.faux;
	var pourCent = ((formulaire.bon/(formulaire.bon+faux))*100) ;
	infoScore.innerHTML += "<br/><span style='color: #096A09;'>"
		+ formulaire.bon + " mots justes</span> et <span style='color: #E61700;'>"
		+ (faux) + " mots faux</span>.<br />"
		+ "Moyenne : " + pourCent.toFixed(2) + " %";
	montreElement(infoScore);
}
function hasClass(elt,classe){
	var className = elt.className ;
	return className.match(new RegExp("(\\s|^)" + classe + "(\\s|$)"));
}
function getElementsByClass(classe,elementTag) {
	var elemList = document.getElementsByTagName(elementTag);
	var resultats = new Array();
	for(var i=0; i<elemList.length; i++){
		if( hasClass(elemList[i] , classe ) ){
		resultats.push(elemList[i]);
		}
	}
	return resultats;
}
function soumettre(){
	document.formulaire.submit();
}
function validerListe(){
	var listeMots = document.getElementById('newListe').value.split("\n");
	for(var i=0; i<listeMots.length; i++){
		if(listeMots[i].replace(new RegExp("( )*"), "").length==0){
			alert("Merci de ne pas mettre de ligne blanche dans votre liste.");
			return false;
		}else if(!listeMots[i].match("=")){
			alert("Merci de bien mettre un '=' pour chaque mot afin de pouvoir soumettre votre liste. ");
			return false;
		}
	}
	return true;
}

function createElem(defElement){
	var elem = null;
	if(!defElement.tag){
		alert("Pour créer un élément du DOM, passer un objet avec la propriété tag");
	}else if(defElement.tag == "text"){
		elem = document.createTextNode(defElement.text);
	}else{	
		elem = document.createElement(defElement.tag);
		for(attr in defElement){
			if(attr != 'tag'){
				elem[attr] = defElement[attr];
			}
		}
	}
	return elem;
}

function removeElem(elem){
	if(elem && elem.parentNode){
		elem.parentNode.removeChild(elem);
	}
}

function createOptions(selectElem, optionsListeDef){	
	for(var i=0; i<optionsListeDef.length; i++){
		var optionsDef = optionsListeDef[i];
		if(optionsDef.options){
			if(optionsDef.label){
				var optionGroup = createElem({tag:'optgroup', label:optionsDef.label});
				selectElem.appendChild(optionGroup);
			}			
			for(var o=0; o<optionsDef.options.length; o++){
				var optionDef = optionsDef.options[o];
				var text = "";
				var value = "";
				if(!optionDef.text && !optionDef.value){
					text = optionDef;
					value = optionDef;
				}else{
					text = optionDef.text;
					value = optionDef.value;
				}
				var option = createElem({tag:'option', value:value});
				option.appendChild(createElem({tag:'text', text:text}));
				selectElem.appendChild(option);
			}
		}
	}
}

function createListeButtonCharSpec(parentElement){
	var charSpecTab = ['ö','ä','ü','Ä','Ö','Ü','é','è','à','ç','É','È','ô','À','î','ê','Ê'];
	var container = createElem({tag:'div', id:'specialCharContainer'});
	for(var i=0; i<charSpecTab.length; i++){
		var buttonChar = createElem({tag:'input', type:'button', value:charSpecTab[i], name:i+1, onclick:"toucheclavier(this.value);"});
		container.appendChild(buttonChar);
	}
	parentElement.appendChild(container);
}

function createListeSelect(idSelect, optionsDef, indexSelected){
	var selectCateg = $("#" + idSelect)[0];
	createOptions(selectCateg, optionsDef);
	if(!indexSelected){
		indexSelected = 0;
	}
	selectCateg.options[indexSelected].selected = "true";
	return selectCateg;
}

function createListeSelectWithDefault (idSelect, optionsDef, indexSelected){
	optionsDef.unshift({options:[{value:"aucun", text:"Toutes"}]});	
	return createListeSelect(idSelect, optionsDef, indexSelected);
}


Array.prototype.sortByProperties = function(properties){
	
};

function dynamicSort(property) {
    var sortOrder = 1;
    if(property[0] === "-") {
        sortOrder = -1;
        property = property.substr(1, property.length - 1);
    }
    return function (a,b) {
    	var exp = new RegExp("^[0-9]+(.|,)?[0-9]*$");
    	var valueA = a[property];
    	var valueB = b[property];
    	if(exp.test(valueA) && exp.test(valueB)){
    		valueA = valueA.replace(",",".");
    		valueB = valueB.replace(",",".");
    		valueA = parseFloat(valueA);
    		valueB = parseFloat(valueB);
    	}
        var result = (valueA < valueB) ? -1 : (valueA > valueB) ? 1 : 0;
        return result * sortOrder;
    };
}


function dynamicSortMultiple(properties) {
    var props = properties;
    return function (obj1, obj2) {
        var i = 0, result = 0, numberOfProperties = props.length;
        while(result === 0 && i < numberOfProperties) {
            result = dynamicSort(props[i])(obj1, obj2);
            i++;
        }
        return result;
    };
}

function getNbPage(liste, nbPerPage){
	return parseInt(liste.length/nbPerPage  + 0.9);
}

Pager = function(listeDefintion) {		
	this.liste = listeDefintion.liste;
	this.nbPerPage = listeDefintion.nbPerPage;
	this.currentPage = 0;
	this.nbPage = getNbPage(this.liste, this.nbPerPage);
	this.idContainerListe = this.initContainerListe(listeDefintion.idListeContainer);	
	this.$containersPager = this.initContainerPager(listeDefintion.classPagerContainer);
	this.containerListeCreator = (listeDefintion.containerListeCreator)?listeDefintion.containerListeCreator:this.defaultContainerListeCreator;
	this.elementCreator = (listeDefintion.elementCreator)?listeDefintion.elementCreator:this.defaultElementCreator;
	this.pageChanger = (listeDefintion.pageChanger)?listeDefintion.pageChanger:this.defaultPageChanger;
	this.defaultSelectorStyle = "list-style:none;float:left; text-align:center; margin:2px; cursor:pointer; height:20px; color:#be3737";	
	this.addPagerContainer();
	this.select(1);
};

Pager.prototype.initContainerListe = function (id){
	var idContainer = (id)?id:"idPagerListeContainer";
	containerListe = $("#"+idContainer);
	if(containerListe.length == 0){
		containerListe = $('<div id="'+idContainer+'" />');
		$('body').append(containerListe);
	}
	return idContainer;
};

Pager.prototype.initContainerPager = function (classe){
	var classPager = (classe)?classe:"classPagerContainer";
	containersPager = $("."+classPager);
	if(containersPager.length == 0 &&  this.nbPage > 1){
		containersPager = $('<div class="'+classPager+'" />');
		$("#"+this.idContainerListe).before(containersPager);
	}
	return containersPager;
};

Pager.prototype.addPagerContainer = function (){
	if(this.nbPage > 1){
		for(var i=0; i<this.$containersPager.length; i++){
			var $container = $('<ul id="pagineur'+i+'" style="height:20px;margin: 10px 0px;padding: 0px;"></ul>');
			this.initSelectors($container);
			$(this.$containersPager[i]).append($container);
			//this.select(this.currentPage);		
		}
	}
};

Pager.prototype.initSelectors = function($container){
	this.addSelector("<< ", "first", 1, $container);
	for(var i=1; i<= this.nbPage; i++){
		this.addSelector(i, "page"+i, i, $container);		
	}
	this.addSelector(" >>", "last", this.nbPage, $container);
};

Pager.prototype.select = function(index){
	for(var i=0; i<this.$containersPager.length; i++){
		var $parent = $(this.$containersPager[i]);
		var elemToSelect = $parent.find('.page'+index).get(0);
		if(this.currentPage != index){
			var elemSelected = $parent.find('.page'+this.currentPage).get(0);
			if(elemSelected){
				this.unSelectElem(elemSelected);				
			}
			if(index == 1){
				this.selectElem($parent.find('.first').get(0));	
			}else if(this.currentPage == 1){
				this.unSelectElem($parent.find('.first').get(0));
			}
			if(index == this.nbPage){
				this.selectElem($parent.find('.last').get(0));	
			}else if(elemToSelect.value == this.nbPage){
				this.unSelectElem($parent.find('.last').get(0));
			}
		}
		elemToSelect.innerHTML = "[ "+elemToSelect.value+" ]";		
		this.selectElem(elemToSelect);
		
	}
	this.currentPage = index;
	this.displaySelectedPage();
};

Pager.prototype.unSelectElem = function(elem){
	elem.innerHTML = elem.texte;
	elem.style.cssText = this.defaultSelectorStyle;	
	elem.isSelected = false;
};

Pager.prototype.selectElem = function(elem){
	elem.style.color = "white";
	elem.style.cursor = "";		
	elem.isSelected = true;
};

Pager.prototype.addSelector = function(text, name, numPage, $parent){
	var pageSelector = createElem({tag:"li", className:name, texte:text, numPage:numPage});
	pageSelector.style.cssText = this.defaultSelectorStyle;
	pageSelector.appendChild(createElem({tag:"text", text:text}));
	var pager = this;
	pageSelector.onclick = function (){
		if(!this.isSelected){
			pager.select(this.numPage);			
			//pagineListesMot(page);
		}
	};
	$parent.append(pageSelector);
};

Pager.prototype.displaySelectedPage = function(){
	var start = (this.currentPage-1) * this.nbPerPage;
	var end = start + this.nbPerPage;
	if(start >= this.liste.length){
		end = this.liste.length-1;
	}
	this.pageChanger({listeObject:this.liste.slice(start, end), startIndex:start+1, elementCreator:this.elementCreator, idContainerListe:this.idContainerListe});
};

Pager.prototype.defaultContainerListeCreator = function (){
	alert("Creator container liste DOM not yet implemented");
};

Pager.prototype.defaultPageChanger = function(){
	alert("Creator page changer DOM not yet implemented");
};

Pager.prototype.defaultElementCreator = function(){
	alert("Creator Element DOM not yet implemented");
};


function createListSort(selectList, listToSort, defaultSelected, pager){
	var selectTri = createListeSelect("trier", selectList);
	defaultSelected = defaultSelected||selectList.options[0].value;
    selectTri.onchange = function(){
    	var value = this.options[this.selectedIndex].value;
  	  	var alternate = defaultSelected;
  	  	var index=0;
  	  	while(value == alternate){
  	  		value = this.options[index++].value;
  	  	}
  	  	reversableSort(listToSort, value, alternate);
  	  	//pagineListesMot(listToSort, 1, nbLimite);
  	  	pager.select(1);
    };
}

function createListForSortListeMot(listMotToSort, pager){
	var listeTri = [{options:[
		{value:"categorie",text:"catégorie"},
		{value:"titre",text:"titre"},
		{value:"-note",text:"note"},
		{value:"-vue",text:"popularité"},
		{value:"membre",text:"auteur"},
		{value:"-timestamp",text:"date"}
	]}];
	createListSort(listeTri, listMotToSort.liste, listMotToSort.defaultSort, pager);
}


function pagineListesMot(domObjectDefine){
	$("#"+domObjectDefine.idContainerListe).html("");
	createListeByCateg(domObjectDefine);
}

function createListeByCateg(domObjectDefine){	
	var div = createElem({tag:"div"});
	var startIndexUsed = (domObjectDefine.startIndex)?domObjectDefine.startIndex:1;
	var elementCreator = (domObjectDefine.elementCreator)?domObjectDefine.elementCreator:createListeMotElement;
	for(var i=0; i<domObjectDefine.listeObject.length; i++){
		var listElemt = elementCreator(domObjectDefine.listeObject[i], startIndexUsed + i);
		div.appendChild(listElemt);
	}
	$("#"+domObjectDefine.idContainerListe).append(div);	
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
		listeMotDef.membre+'</a> le '+listeMotDef.date+'('+listeMotDef.timestamp+')</small><br /><br/>';
	div.innerHTML = elem;
	return div;
}

function createListeMotElementRecherche(listeMotDef, index){
	var div = createElem({tag:"div"});
	index = (!index)?"":index;
	var note = (listeMotDef.note!="")?'Note: '+listeMotDef.note+'/5':"Pas de note";
	var elem = index+'.<b>'+
		listeMotDef.categorie+'<->'+listeMotDef.categorie2+
		': </b> <a href="afficher?id='+listeMotDef.id+'">'+
		listeMotDef.titre+'</a> (' + note + ' et '+
		listeMotDef.vue+' vues)<br /><small> par <a href="profil?m='+listeMotDef.membre+'">'+
		listeMotDef.membre+'</a> le '+listeMotDef.date+'('+listeMotDef.timestamp+')</small><br /><br/>';
	div.innerHTML = elem;
	return div;
}

function slidePage(domObjectDefine){
	var $elemContainer = $("#"+domObjectDefine.idContainerListe);
	var $sliderContainer = $("#sliderContainer");
	if($sliderContainer.length == 0){
		$sliderContainer = $('<div id="sliderContainer" />');
		$sliderListe = $('<div id="sliderList" style="position: absolute; width: 100%; height: 100%" />');
		$sliderContainer.append($sliderListe);
		$elemContainer.before($sliderContainer);
		$sliderListe.append($elemContainer);
	}	
	if($elemContainer.children().length > 0){
		$elemContainer.css('width', $elemContainer[0].offsetWidth);
		/*	var slider = $("#sliderList");
			slider.css(elemContainer.css('width'));
			slider.css("overflow","hidden");
			slider.css("height",elemContainer[0].offsetHeight);*/
		$elemContainer.css("float","left");
		$elemContainer.css("position", "absolute");
		var nextContainer = $elemContainer.clone();
		nextContainer.css('left', $elemContainer[0].offsetWidth);
		$elemContainer.attr("id","#listesContainerToDelete");	
		nextContainer.appendTo("#sliderList");
		domObjectDefine.containerListe = nextContainer;
		pagineListesMot(domObjectDefine);
		$elemContainer.animate({
			'left' : '-' + $elemContainer[0].offsetWidth + 'px'
		});
		nextContainer.animate({
	        'left' : '0px'
		},function(){
			$elemContainer.remove();
		});
		$("#pagineur").focus();
	}else{
		pagineListesMot(domObjectDefine);
		$sliderContainer.css("height", $elemContainer.css("height"));
	}		
}

function reversableSort(liste){
	var i = 1;
	var props = new Array();
	var reverse = false;
	for(i; i<arguments.length; i++){
		if(i == 1 && reverse){
			arguments[i] = "-" + arguments[i];
		}
		props.push(arguments[i]);
	}
	liste.sort(dynamicSortMultiple(props));
}
function copyToClipboard(){
	var listeMots = document.getElementById('listeMot').value;
	if(window.clipboardData){
		window.clipboardData.setData("Text", listeMots);
	}else{
		//alert('Copie dans le presse papier impossible!/n Nous allons vous afficher un champ avec la liste sélectionné§. /nFaites Ctrl+c pour la mettre dans le presse papier.');
		selectToCopy();
	}
}
function getClipboard(){
	var texte = "";
	if(window.clipboardData){
		texte = window.clipboardData.getData('Text');
	}
	return texte;
}
function alertClipboard(){
	alert(getClipboard());
}
function selectToCopy(){
	var listeMots = document.getElementById('listeMot').value;
	var tabMots= listeMots.split("\n");
	var indexMostLong = 0;
	for(var i=0; i<tabMots.length;i++){
		if(tabMots[i].length>indexMostLong){
			indexMostLong = tabMots[i].length;
		}
	}
	var cachePage = document.createElement("div");
	cachePage.id="cachePage";
	cachePage.style.cssText = "position:absolute; top:0px; left:0px; z-index:999; width:100%; height:"+document.body.offsetHeight+"px; background-color:gray; opacity : 0.7;-moz-opacity : 0.7;-ms-filter:alpha(opacity=70);filter:alpha(opacity=70);";
	var divContainer = document.createElement("div");  
	divContainer.id = "blockToCopy";
	var divClose = document.createElement("div");
	divClose.innerHTML = "Cliquez ici pour fermer.";
 	divClose.style.cssText = "cursor:pointer; background-color:orange; border:3px solid #7E3117;width:"+(indexMostLong*7)+"px"; 
 	divClose.onmouseover = function(){this.style.backgroundColor = "#CC6600";};
 	divClose.onmouseout = function(){this.style.backgroundColor = "orange";};
 
 	var texteArea = document.createElement("textarea");  
 	texteArea.id = "listeToCopy";
 	texteArea.value = listeMots;  
 	texteArea.rows = tabMots.length;
 	texteArea.style.cssText = "overflow:auto;width:"+(indexMostLong*7)+"px;margin:0;";
	divContainer.style.cssText = "position:absolute; top:" + (document.documentElement.scrollTop + 100) +"px; left:40%;z-index:1000; width:"+(indexMostLong*8)+"px";
	document.body.appendChild(cachePage);
	document.body.appendChild(divContainer);
	divContainer.appendChild(divClose);
	divContainer.appendChild(texteArea);
	divClose.onclick = function (){
       closeCachePage();
	};
	cachePage.onclick = function (){
       closeCachePage();
	};
	texteArea.select();
}

function closeCachePage(){
    var cachePage = document.getElementById("cachePage");
  	var divContainer = document.getElementById("blockToCopy");
  	cachePage.parentNode.removeChild(cachePage);
  	divContainer.parentNode.removeChild(divContainer);
}
