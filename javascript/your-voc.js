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
}

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
}

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
}
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
	if( blockQuestion.reponse != ""){
		cacheElement(infoScore);
		if(isValideReponse(blockQuestion.reponse, blockQuestion.solution)){
			cacheElement(buttonValidError);
			formulaire.bon++ ;
			afficheScoreJuste(blockQuestion.question, blockQuestion.solution);
		}else{
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
	manageDisplay(blockQuestion.index, indiceReponseListSort[0]);
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
function manageDisplay(indiceCache, indiceMontre){
	if(divs[indiceCache]){
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
		+ "Moyenne : " + pourCent.toFixed(2) + " %" ;
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
				elem.setAttribute(attr, defElement[attr]);
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
    }
}