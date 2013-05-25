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