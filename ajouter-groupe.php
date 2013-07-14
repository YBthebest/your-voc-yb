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

$(document).ready(function() {
	$("#faq_search_input").watermark("Tappez ici pour commencer la recherche");
	$("#faq_search_input").keyup(function(){
		var faq_search_input = $(this).val();
		var dataString = 'keyword='+ faq_search_input;
		if(faq_search_input.length>2){
			$('#result').empty();
			$('#result').append('<table class="table" cellspacing="0" cellpadding="0">');	
			$('#result').append('<tr><th></th><th>Titre</th></tr>');
			$.getJSON("ajax-search.php?groupe=" + faq_search_input, function(data) {
			    $.each(data, function(index, data1) {					
				    $('#result').append('<tr>');
			    	$('#result').append('<div id="result_'+data1.id+'"><td><a href=groupe?id=' + data1.id + '&add=ok><img src="images/add.png" /></a></td><td><a href=groupe?id=' + data1.id + '>' + data1.nom + '</a></td></div>');     			
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
								if(strlen($nom) > 4){
									$date = time();
									$createur = $_SESSION['login'];
									$m = getMembreByLogin($createur);
									$idCreateur = $m->id();
									$getGroupe = getGroupeByNom($nom);
									if(empty($getGroupe)){
										createGroupe($nom, $idCreateur, $date);
										$groupe = getGroupeByNomAndCreateur($nom, $idCreateur);
										$id = $groupe->id();
										?><meta http-equiv="Refresh" content="0;url=/groupe?id=<?php echo $id ;?>"><?php
									}
									else{
										echo 'Un groupe avec ce nom exite déjà. Veuillez le rejoindre ou choisir un autre nom.';
									}
								}
								else{
									echo 'Le nom de votre groupe est trop court.';
									?>
									<form name="groupe" method="post">
									<input type="text" name="nom"><br />
									<input type="submit" name="valid" value="Valider"/>
									</form>
									<?php
								}
							}else{
							?>
							<p>Précisez si possible des informations comme le nom du professeur et l'année scolaire.<br /></p>
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
