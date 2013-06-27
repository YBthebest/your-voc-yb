<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<?php 
		if(isset($_GET['id'])){
			$id = mysql_real_escape_string($_GET['id']);
			$groupe = getGroupeById($id);
			$nom = $groupe->nom();
			$date = $groupe->date();
		}
		?>
    	<div id="title"><?php echo $nom ?></div>
	</div>
</div>
