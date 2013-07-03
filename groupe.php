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
			if(empty($groupe)){
				echo "L'id précisé est invalide.";
			}
			else{
				$nom = $groupe->nom();
				$date = $groupe->date();
		?>
    	<div id="title"><?php echo $nom ?></div>
    	<?php 
    			if(isset($_GET['add'])){
					if(isset($_SESSION['login'])){
						$membre = $_SESSION['login'];
						$demande = getDemandeByPseudoAndIdGroupe($membre, $id);
						if(!empty($demande)){
							foreach($demande as $result){
								if($result->statut() == ('pending' OR 'rejected')){
									echo "Vous avez déjà fait une demande pour rejoindre ce groupe.";
								}
								elseif($result->statut() == 'accepted'){
									?><meta http-equiv="refresh" content="0;URL='/membre'" /> <?php
								}
							}
						}
						else{
							
						}
					}
				}
    		}
    	}
    	?>
	</div>
</div>
