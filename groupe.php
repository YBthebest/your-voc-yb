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
    			if(isset($_POST['join_sub'])){
					if(isset($_SESSION['login'])){
						$m = getMembreByLogin($_SESSION['login']);
						$membre = $m->id();
						$demande = getDemandeByPseudoAndIdGroupe($membre, $id);
						if(!empty($demande)){
							foreach($demande as $result){
								if($result->statut() == ('pending' OR 'rejected')){
									echo "Vous avez déjà fait une demande pour rejoindre ce groupe.";
								}
								elseif($result->statut() == 'accepted'){hhj
									?><meta http-equiv="refresh" content="0;URL='/membre'" /> <?php
								}
							}
						}
						else{
							if(createDemande($id, $membre)){
								echo "Votre demande a été envoyée. Veuillez attendre l'acceptation ou le refus du groupe.";
							}
						}
					}
				}
				if(isset($_SESSION['login'])){
					$m = getMembreByLogin($_SESSION['login']);
					$idMembre = $m->id();
					$membre = getDemandeByPseudoAndIdGroupe($idMembre, $id);
					if(empty($membre)){
						?><form method="post" id="join"><input type="submit" name="join_sub" value="Rejoindre ce groupe" /></form><?php
					}
				}						
				else{
					?><a href="connexion">Connectez-vous pour rejoindre ce groupe</a><?php
				}
    		}
    	}
    	?>
	</div>
</div>
