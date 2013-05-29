<?php
	if (!isset($_SESSION['login'])) {
		header ('Location: accueil');
		exit();
	} 
?>
<script type="text/javascript">
window.listesMots = [];
window.listeCombi = [];
window.selectedElem;

function getListeMot(id){
	for(var i=0; i<window.listesMots.length; i++){
		if(window.listesMots[i].id == id){
			return window.listesMots[i];
		}
	}
	return null;
}

function displayListe(elem, idListe){
	var listeMotDef = getListeMot(idListe);	
	var domResult = '<div id="liste_'+listeMotDef.id+'"><div style="color:#be3737"><a href="afficher?id='+ listeMotDef.id +'">' + listeMotDef.titre + '</a> : </div><br /> <strong>' + listeMotDef.categorie + '<->' + listeMotDef.categorie2 + '</strong><br /><br />';
	for(var i=0; i<listeMotDef.mots.split('\r\n').length; i++){
     domResult += listeMotDef.mots.split('\r\n')[i];
     domResult += '<br />';
    }  
    domResult += "</div>";
    $('#listeMot').html("");
    $('#listeMot').append(domResult);
	 elem.style.color = "#be3737";
	 if(window.selectedElem){
		 window.selectedElem.style.color = "white";
	 }
	 window.selectedElem = elem;
}
function addCombiner(idListe){
	var listeMotDef = getListeMot(idListe);
    $('#result_' + idListe).hide();
    $('#result_' + idListe).css("color","white");
    $('#liste_'+ idListe).remove();
    $('#combinaisons').append('<div id="combi_' + listeMotDef.id + '"><img src="images/delete.png" onclick="deleteCombiner(\'' + listeMotDef.id + '\');"/><a href="afficher?id='+ listeMotDef.id +'">' + listeMotDef.titre + '</a>(<small>' + listeMotDef.categorie + '<->' + listeMotDef.categorie2 + '</small>)<br /></div>');    
   	window.listeCombi.push(idListe);
    checkCombin();
}

function deleteCombiner(idListe){
   	$('#combi_'+idListe).remove();     
    window.listeCombi.remove(idListe);
    $('#result_' + idListe).show();
    checkCombin();
}

function defaultListeByGetId(){
	var idListe = <?php echo (isset($_GET['id']))?$_GET['id']:0; ?>;
    $.getJSON("liste_result.php?id=" + idListe, function(data) {
        $.each(data, function(index, data1) {
        	$('#result').append('<div id="result_'+data1.id+'"><img src="images/add.png" onclick="addCombiner(\'' + data1.id + '\');"/><span class="listeMot" onclick="displayListe(this, \'' + data1.id + '\');">' + data1.titre + '</span></div>');     			
        	window.listesMots.push(data1);      
        });
    	addCombiner(idListe);
  	});
}
defaultListeByGetId();
$(document).ready(function() {
	$("#faq_search_input").watermark("Tappez ici pour commencer la recherche");
	$("#faq_search_input").keyup(function(){
		var faq_search_input = $(this).val();
		var dataString = 'keyword='+ faq_search_input;
		if(faq_search_input.length>1){
			$('#result').empty();
			$('#result').append('<table class="table" cellspacing="0" cellpadding="0">');	
			$('#result').append('<tr><th></th><th>Titre</th></tr>');
			$.getJSON("ajax-search.php?keyword=" + faq_search_input, function(data) {
			    $.each(data, function(index, data1) {					
				    $('#result').append('<tr>');
			    	$('#result').append('<div id="result_'+data1.id+'"><td><img src="images/add.png" onclick="addCombiner(' + data1.id + ');"/></td><td><span class="listeMot" onclick="displayListe(this, \'' + data1.id + '\');">' + data1.titre + '</span>(<small>' + data1.categorie + ' <-> ' + data1.categorie2 + '</small>)</td></div>');     			
					$('#result').append('</tr>');
			    	window.listesMots.push(data1);    
			    });
			});
			$('#result').append('</table>');
		}return false;
	});
});	

function checkCombin() {      
	if(window.listeCombi.length < 2) {
       $("#valider_combin").prop("disabled",true);
	} else if(window.listeCombi.length > 9){
	   $("#valider_combin").prop("disabled",true);		
    } else {
       $("#valider_combin").prop("disabled",false);
    }

}

jQuery(document).ready(function(){   
     checkCombin();
});

function checkSubmit(){
	var e = document.getElementById("todo");
	var value_submit = e.options[e.selectedIndex].text;
	if(value_submit == 'sauvegarder'){
		saveCombin();
	}else{
		reviseCombin();
	}
}
function saveCombin(){
	var idListeCombiTotal = window.listeCombi;
	var mots = '';
	var titre = 'Combinaison de ';
	if(window.listeCombi.length > 1){
		for(var i=0; i<window.listeCombi.length; i++){
			var idListeCombi = window.listeCombi[i];			
			var listeMot = getListeMot(idListeCombi);
			mots += listeMot.mots;
			mots += '\r\n';
			titre += listeMot.titre;
			if(i != (window.listeCombi.length - 1)){
				titre += ' & ';
			}
		};
		var mesValeurs = { idListe: idListeCombiTotal, mots: mots, titre: titre}
		var saveData = $.ajax({
		      type: 'POST',
		      url: "save_combin.php?action=saveCombin",
		      data: mesValeurs,
		      dataType: "text",
		      success: function(resultData) {
			       alert("Votre combinaison a bien été sauvgardée.");
			       setTimeout(function () {
			    	   window.location.href = "membre";
			    	}, 1000);  
			  }
		});
		saveData.error(function() { alert("Un problème a eu lieu. Veuillez réessayer."); });
	}
}
function reviseCombin(){
	var idListeCombiTotal = window.listeCombi;
	var mots = '';
	var id = 'no';
	var titre = 'Combinaison de ';
	if(window.listeCombi.length > 1){
		for(var i=0; i<window.listeCombi.length; i++){
			var idListeCombi = window.listeCombi[i];			
			var listeMot = getListeMot(idListeCombi);
			mots += listeMot.mots;
			mots += '\r\n';
			titre += listeMot.titre;
			if(i != (window.listeCombi.length - 1)){
				titre += ' & ';
			}
		};
	}
	var mydiv = document.getElementById('divTitre').innerHTML = '<form id="reviseCombi" method="post" action="revise"><input name="reviseCombi" type="hidden" value="ok" /><input name="reviseCombiMots" type="hidden" value="'+ mots +'" /><input type="hidden" name="titreCombi" value="'+ titre +'" /></form>';
	f=document.getElementById('reviseCombi');
	if(f){
		f.submit();
	}
}
</script>
<!-- D?but de la pr?sentation -->
<div id="presentation1"></div>
<!-- Fin de la pr?sentation -->
<!-- D?but du contenu -->
<div id="content">
	<div id="bloc">
		<div id="title">Combiner</div>
			<div id="confirm_1"></div>
					<div id="container">
						<div id="divTitre"></div>
						<p>Voulez-vous 
							<select name="todo" id="todo">
								<option value="sauvegarder">sauvegarder</option>
								<option value="reviser">réviser</option>
							</select>
							cette combinaison?
							<input type="submit" value="Go!" id="valider_combin" onClick="checkSubmit()" />
						</p>
						<div class="col">
							<h3>Actuellement en combinaison</h3>
							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">
								<div id="combinaisons"></div>
							</div>
						</div>
						<div class="col">
							<h3>Recherche des listes disponibles</h3>
                            <input  name="query" type="text" id="faq_search_input" size="42" />
   							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">			
								<div id="result"></div>
							</div>
						</div>
						<div class="col">
							<h3>Détail liste séléctionnée</h3>
							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">
								<div id="listeMot"></div>
							</div>
						</div>
					</div>
	</div>
</div>
