<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
$(function(){
	createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe()?>);
});
function OnSelectionChange(select){
	$(".result").html("");
    var selectedOption = select.options[select.selectedIndex];
    $.getJSON("liste_result.php?q=" + $("#categorie").val(), function(data) {
        $.each(data, function(index, data1) {
            $('.result').append("<button id='"+ data1.id +"' onclick='getListe(this.id);'>" + data1.titre + "</button></br />");       
            $('.result').append("<button id='"+ data1.id +"' onclick='addCombiner(this.id);'>Confirmer</button></br />");       			
    	});
  	});  
}
function getListe(clicked_id){
	$(".listeMot").html("");
    $.getJSON("liste_result.php?l=" + clicked_id, function(data) {
        $.each(data, function(index, data1) {
            $('.listeMot').append("Titre:" + data1.titre + "<br />");       
        });
  	}); 
}
function addCombiner(clicked_id){
    $.getJSON("liste_result.php?l=" + clicked_id, function(data) {
        $.each(data, function(index, data1) {
            $('.combinaisons').append("Titre:" + data1.titre + "<br />");       
        });
  	}); 
}
</script>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="title">Combiner</div>
		<div id="container4">
			<div id="container3">
				<div id="container2">
					<div id="container1">
						<div id="col1_">
							<h3>Col 1</h3>
						</div>
						<div id="col2">
							<h3>Col 2</h3>
							<button class="confirm">Confirmer la combinaison</button><br />
							<div class="combinaisons">
							</div>
						</div>
						<div id="col3">
							<h3>Col 3</h3>
							<form name="new_combiner">
								<select id="categorie" name="categorie" onchange="OnSelectionChange(this)"></select>
							</form>
							<div class="result">
							</div>
						</div>
						<div id="col4">
							<h3>Col 4</h3>
							<div class="listeMot">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
