<script type="text/javascript">
$(function(){
	createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe()?>);
});

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

function OnSelectionChange(select){
	$("#result").html("");
    var selectedOption = select.options[select.selectedIndex];
    $.getJSON("liste_result.php?q=" + $("#categorie").val(), function(data) {
        $.each(data, function(index, data1) {
        	$('#result').append('<div id="result_'+data1.id+'"><img src="images/add.png" onclick="addCombiner(\'' + data1.id + '\');"/><span class="listeMot" onclick="displayListe(this, \'' + data1.id + '\');">' + data1.titre + '</span></div>');     			
        	window.listesMots.push(data1);
        	if(window.listeCombi.indexOf(data1.id) == -1){                
               window.listeCombi.push(data1.id);
            }       
        });
  	});  
}
function displayListe(elem, idListe){
	var listeMotDef = getListeMot(idListe);	
	 $('#listeMot').html("");
	 $('#listeMot').append('<div id="liste_'+listeMotDef.id+'"><div style="color:#be3737">' + listeMotDef.titre + " : </div></br><div><pre>" + listeMotDef.mots + "</pre></div></div>");       
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
    $('#combinaisons').append('<div id="combi_' + listeMotDef.id + '"><img src="images/delete.png" onclick="deleteCombiner(\'' + listeMotDef.id + '\');"/>' + listeMotDef.titre + "</div><br />");    
    $('#detail_combiner').append('<div id="detail_' + listeMotDef.id + '"><pre>' + listeMotDef.mots + "</pre></div><br />");   
    window.listeCombi.push(idListe);
}

function deleteCombiner(idListe){
   	$('#combi_'+idListe).remove();    
    $('#detail_'+idListe).remove();  
    window.listeCombi.remove(idListe);
    $('#result_' + idListe).show();
}

function confirm(){
	if( $('#detail_combiner').is(':empty') ) {
		$('#confirm_1').html("");
    	$('#confirm_1').append('Aucune liste sélectionée.'); 
	}
	else{
		$('#confirm_1').html("");
    	$('#confirm_1').append('Votre combinaison va etre sauvegardée.'); 		
	}
}

function defaultListeByGetId(){
	var $_GET = <?php echo json_encode($_GET); ?>;
	var idListe = $_GET['id'];
	if(typeof idListe != 'undefined'){	
		 var listeMotDef = getListeMot(idListe);
		 alert(listeMotDef);	
		 $('#listeMot').html("");
		 $('#listeMot').append('<div id="liste_'+listeMotDef.id+'"><div style="color:#be3737">' + listeMotDef.titre + " : </div></br><div><pre>" + listeMotDef.mots + "</pre></div></div>");       
		 elem.style.color = "#be3737";
		 if(window.selectedElem){
			 window.selectedElem.style.color = "white";
		 }
		 window.selectedElem = elem;
	}
}
//defaultListeByGetId();
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
						<div class="col">
							<h3>Détails listes combinées</h3>
							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">
								<div id="detail_combiner"></div>
							</div>
						</div>
						<div class="col">
							<h3>Titres listes combinées</h3>
							<button class="confirm" onclick="confirm();">Confirmer la combinaison</button>
							<br />
							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">
								<div id="combinaisons"></div>
							</div>
						</div>
						<div class="col">
							<h3>Titres listes disponibles</h3>
							<select id="categorie" name="categorie" onchange="OnSelectionChange(this)"></select>
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
