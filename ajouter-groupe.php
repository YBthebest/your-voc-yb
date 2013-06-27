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

$(document).ready(function() {
	$("#faq_search_input").watermark("Tappez ici pour commencer la recherche");
	$("#faq_search_input").keyup(function(){
		var faq_search_input = $(this).val();
		var dataString = 'keyword='+ faq_search_input;
		if(faq_search_input.length>1){
			$('#result').empty();
			$('#result').append('<table class="table" cellspacing="0" cellpadding="0">');	
			$('#result').append('<tr><th></th><th>Titre</th></tr>');
			$.getJSON("ajax-search.php?groupe=" + faq_search_input, function(data) {
			    $.each(data, function(index, data1) {					
				    $('#result').append('<tr>');
			    	$('#result').append('<div id="result_'+data1.id+'"><td><img src="images/add.png" onclick="addCombiner(' + data1.id + ');"/></td><td><span class="listeMot"><a href=groupe?id=' + data1.id + '>' + data1.nom + '</a></span></td></div>');     			
					$('#result').append('</tr>');
			    	window.listesMots.push(data1);    
			    });
			});
			$('#result').append('</table>');
		}return false;
	});
});	

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
						<div class="col">
							<h3>Créer votre propre groupe</h3>
							<?php 
							if(isset($_POST['valid'])){
								$nom = mysql_real_escape_string($_POST['nom']);
								$date = time();
								createGroupe($nom, $date);
								echo 'Votre groupe a bien été créé. Merci.';
							}else{
							?>
							<form name="groupe" method="post">
								<input type="text" name="nom"><br />
								<input type="submit" name="valid" value="Valider"/>
							</form>
							<?php
							} 
							?>
						</div>
						<div class="col">
							<h3>Recherche des groupes disponibles</h3>
                            <input  name="query" type="text" id="faq_search_input" size="42" />
   							<div style="border:1px solid #be3737;margin-top:5px;height: 400px; overflow-y: auto;overflow-x: hidden;">			
								<div id="result"></div>
							</div>
						</div>
						<div class="col">
						</div>
					</div>
	</div>
</div>
