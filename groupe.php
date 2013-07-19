<script type="text/javascript">
	function validateDelete(){
		return confirm("Voulez-vous vraiment supprimer ce membre du groupe?");
	}
	function validateAdmin(){
		return confirm("Voulez-vous vraiment passer ce membre du groupe en admin?");
	}
	function validateUnAdmin(){
		return confirm("Voulez-vous vraiment enlever le privilège d'admin à ce membre?");
	}
	function validateQuit(){
		return confirm("Voulez-vous vraiment quitter ce groupe?");
	}
	function validateSupprimer(){
		return confirm("Voulez-vous vraiment supprimer ce groupe? Cette action est irréversible et tous les membres du groupe perdront l'accés au groupe.");
	}	
	function showDivDemandes() {
	    ele = document.getElementById('demande');
	    if(ele.style.display == "block") {
            ele.style.display = "none";
     	}
    	else {
     	   ele.style.display = "block";
    	}
	}
	function showDivTitre() {
	    ele = document.getElementById('titre');
	    if(ele.style.display == "block") {
            ele.style.display = "none";
     	}
    	else {
     	   ele.style.display = "block";
    	}
	}
	function showDivAddListe() {
	    ele = document.getElementById('divAddListe');
	    if(ele.style.display == "block") {
	    	ele.style.display = "none";
	    }
	   	else {
	   	   	ele.style.display = "block";
	   	}   
	}
</script>
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
				if(isset($_SESSION['login'])){
		    		$m = getMembreByLogin($_SESSION['login']);
		   			$membre = $m->id();
		   			$membreGroupe = getMembreByIdGroupeAndMembre($id, $membre);
		   			if(!empty($membreGroupe)){
						if(isset($_POST['titre'])){
							$new_titre = $_POST['titre'];
							if(strlen($new_titre) > 4){
								updateNomGroupe($id, $membre, $new_titre);
								$groupe = getGroupeById($id);
								$nom = $groupe->nom();
								echo 'Le titre du groupe a bien été changé.';
							}
							else{
								echo 'Le nouveau titre donné est trop court. Veuillez recommencer.';
							}
						}
						if(isset($_POST['membre'])){
							if(isset($_POST['accept'])){
								$idMembre = $_POST['membre'];
								updateDemandeByStatut($id, $idMembre, 'accepted');
								$m = getMembreById($idMembre);
								$membre = $m->login();
								createDroit('read', $idMembre, $id);
								$droit = getDroitByIdMembreAndIdGroupe($idMembre, $id);
								foreach($droit as $result){
									$idDroit = $result->id();
								}
								createMembreGroupe($idMembre, $id, $idDroit);
								echo $membre;
								echo ' fait désormais parti de votre groupe.';
							}
							elseif(isset($_POST['reject'])){
								$idMembre = $_POST['membre'];
								updateDemandeByStatut($id, $idMembre, 'rejected');
								echo 'La demande a bien été rejetée.';
							}	
						}
					}
				}
		?>
    	<div id="title"><?php echo $nom; ?></div>
		 <?php
		    if(isset($_SESSION['login'])){
				$m = getMembreByLogin($_SESSION['login']);
				$membre = $m->id();
				$membreGroupe = getMembreByIdGroupeAndMembre($id, $membre);
				if(empty($membreGroupe)){
					if(isset($_POST['join_sub'])){
						$demande = getLastDemandeByPseudoAndIdGroupe($membre, $id);
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
							if(createDemande($id, $membre)){
								$createur = $groupe->idCreateur();
								$membreEmail = getMembreById($id);
								$pseudoCreateur = $membreEmail->login();
								$pseudoDemande = $_SESSION['login'];
								$emailCreateur = $membreEmail->email();
								echo "Votre demande a été envoyée. Veuillez attendre l'acceptation ou le refus du groupe.";
								$to = '"'.$pseudoCreateur.'" <<a href="mailto:'.$emailCreateur.'">'.$emailCreateur.'</a>>';
								$subject = "Your-Voc: un nouveau membre veut rejoindre votre groupe!";
								$message = "".$pseudoDemande." veut rejoindre votre groupe.
								Veuillez cliquer sur le lien suivant pour aller sur votre groupe et accepter ou rejeter sa demande.
								----------------------------
								http://www.your-voc.com/groupe?id=$id
								----------------------------
								Ceci est un e-mail généré automatiquement. Veuillez bien ne pas y répondre.
								
								Merci de votre compréhension et bon apprentisage sur Your-Voc.
								";
								$headers = 'From: "Your-Voc" <<a href="mailto:reset@your-voc.com">reset@your-voc.com</a>>' . PHP_EOL .
								'X-Mailer: PHP/' . phpversion();		
								if (!mail($to, $subject, $message, $headers)) {
								$erreur = "Un problème a apparu. Veuillez-nous contacter. (<a href='contact'>Contact</a>)";
								}
							}
						}
					}								
					$m = getMembreByLogin($_SESSION['login']);
					$idMembre = $m->id();
					$membre = getLastDemandeByPseudoAndIdGroupe($idMembre, $id);
					if(empty($membre)){
						?><form method="post" id="join"><input type="submit" name="join_sub" value="Rejoindre ce groupe" /></form><?php
					}
					else{
						foreach($membre as $result){
							if($result->statut() == 'pending'){
								echo 'Votre demande est en attente. Veuillez patienter.';
								?><form name="annuler_form" method="post">
								<input type="submit" name="annuler" value="Annuler la demande" />
								</form><?php 
							}
							elseif($result->statut() == 'rejected'){
								echo 'Votre demande a été rejetée. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez nous <a href="contact">contacter</a>.';
							}
						}
					}
					if(isset($_POST['annuler'])){
						deleteDemande($id, $idMembre);
						echo 'Votre demande a bien été annulée.';
						?><meta http-equiv="refresh" content="0;URL='/membre'" /> <?php
					}
				}
				else{
					$pseudo = $_SESSION['login'];
					$m = getMembreByLogin($pseudo);
					$pseudo = $m->id();
					$droit = getDroitByIdMembreAndIdGroupe($pseudo, $id);
					foreach($droit as $statut){
						if($statut->libelle() == 'admin'){
							$demandes = getDemandePendingByIdGroupe($id);
							if(!empty($demandes)){
								echo '<h4>Nouvelle demande:</h4>';
								foreach($demandes as $result){
									$idMembre = $result->pseudo();
									$m = getMembreById($idMembre);
									$membre = $m->login();
									echo '<a href="groupe?m='.$membre.'">'.$membre.'</a>';
									?><form id="demande" name="demande" method="post" action="groupe?id=<?php echo $id ?>#tab_2" >
								    	<input type="submit" name="accept" value="Accepter">
										<input type="submit" name="reject" value="Rejeter">
										<input type="hidden" name="membre" value="<?php echo $idMembre ?>">
									</form><?php
									echo '<br />';
								}								
							}				
						}
					}
				}			
			}
			else{
				?><a href="connexion">Connectez-vous pour rejoindre ce groupe</a><?php
			}
	    	?>
    	<div id="wrapper">
		<div id="tabContainer">
			<div class="tabs">
		    	<ul>
		    	    <li id="tab_1">Les listes</li>
		    	    <li id="tab_2">Les membres</li>
			        <?php
			        if(isset($_SESSION['login'])){ 
			        	$m = getMembreByLogin($_SESSION['login']);
			        	$membre = $m->id();
			        	$membreGroupe = getMembreByIdGroupeAndMembre($id, $membre);
			        	if(!empty($membreGroupe)){
			        		?><li id="tab_3">Les options</li><?php 
			        	} 
			        }
			        ?>
		        </ul>
		    </div>
		    <div class="tabscontent">
		    	<div class="tabpage" id="tabpage_1">
		        	<h2>Les listes</h2>
		        	<?php 
		        	if(isset($_SESSION['login'])){
						$pseudo = $_SESSION['login'];
						$m = getMembreByLogin($pseudo);
						$pseudo = $m->id();
						$droit = getDroitByIdMembreAndIdGroupe($pseudo, $id);
						foreach($droit as $statut){
							if($statut->libelle() == 'admin'){
								?>
								<a href="javascript:;" onclick="showDivAddListe();return false;">Ajouter une liste</a>
								<div id="divAddListe" style="display:none;">
									<?php
									echo 'Vos listes:<br />';
									$listesPerso = getListeByPseudo($_SESSION['login']);
									if(!empty($listesPerso)){
										foreach($listesPerso as $resultListes){
											echo '<a href="afficher?id='.$resultListes->id().'">'.$resultListes->titre().'</a><br />';
										}
									}
									?>
								</div>
								<?php 
							}
						}
					}
					?>	        	
		      	</div>
		      	<div class="tabpage" id="tabpage_2">
		      		<h2>Les membres</h2>
				    <?php
					if(isset($_SESSION['login'])){
		    			$m = getMembreByLogin($_SESSION['login']);
		    			$membre = $m->id();
		    			$membreGroupe = getMembreByIdGroupeAndMembre($id, $membre);
		    			if(!empty($membreGroupe)){
								$pseudo = $_SESSION['login'];
								$m = getMembreByLogin($pseudo);
								$pseudo = $m->id();
								$droit = getDroitByIdMembreAndIdGroupe($pseudo, $id);
								foreach($droit as $statut){
									if($statut->libelle() == 'admin'){
										echo '<h3>Demandes de rejoindre le groupe:</h3>';
										$demandes = getDemandePendingByIdGroupe($id);
										if(empty($demandes)){
											echo 'Aucune demande.<br /><br />';
										}
										else{
											foreach($demandes as $result){
												$idMembre = $result->pseudo();
												$m = getMembreById($idMembre);
												$membre = $m->login();
												echo '<a href="groupe?m='.$membre.'">'.$membre.'</a>';
												?><form id="demande" name="demande" method="post" action="groupe?id=<?php echo $id ?>#tab_2">
												<input type="submit" name="accept" value="Accepter">
												<input type="submit" name="reject" value="Rejeter">
												<input type="hidden" name="membre" value="<?php echo $idMembre ?>">
												</form><?php
												echo '<br />';
											}
										}
										$demandeDeleted = getDemandeByStatut('deleted');
										if(!empty($demandeDeleted)){
											echo '<br /><button onclick="javascript:showDivDemandes();">Voir/cacher les membres supprimés</button>';
											echo '<div id="demande" style="display:none;">';
											$i = '0';
											echo '<h4>Redonner la chance à un membre supprimé de rejoindre le groupe.</h4><br />';
											foreach($demandeDeleted as $deleted){
												$i++;
												echo ''.$i.'. ';
												$m = $deleted->pseudo();
												$membre = getMembreById($m);
												$pseudo = $membre->login();
												echo '<a href="groupe?m='.$pseudo.'">'.$pseudo.'</a><br />';
												?>
												<form name="reaccepter" method="post" action="groupe?id=<?php echo $id ?>#tab_2">
													<input type="submit" name="reaccept" value="Ok" />
													<input type="hidden" name="idMembreDeleted" value="<?php echo $deleted->pseudo() ?>" />
													<input type="hidden" name="pseudoDeleted" value="<?php echo $pseudo ?>" />								
												</form>
												<?php 
											}
											echo '</div>';
										}
									}
								}
								if(isset($_POST['idMembreDeleted'])){
									$idMembre = mysql_real_escape_string($_POST['idMembreDeleted']);
									$pseudo = mysql_real_escape_string($_POST['pseudoDeleted']);
									deleteDemande($id, $idMembre);
									echo '<br /><h4>';
									echo $pseudo;
									echo ' a maintenant la chance de pouvoir rejoindre ce groupe à nouveau s\'il le souhaite.</h4>';
								}
								if(isset($_POST['idMembreAdmin'])){
									$idMembreAdmin = mysql_real_escape_string($_POST['idMembreAdmin']);
									updateDroitLibelle($idMembreAdmin, $id, 'admin');
									$m = getMembreById($idMembreAdmin);
									$pseudo = $m->login();
									echo '<br /><h3>';
									echo $pseudo;
									echo ' est désormais un administrateur du groupe.</h3>';
								}
								if(isset($_POST['unadmini'])){
									$idMembreAdmin = mysql_real_escape_string($_POST['idMembreAdminSup']);
									updateDroitLibelle($idMembreAdmin, $id, 'read');
									$m = getMembreById($idMembreAdmin);
									$pseudo = $m->login();
									echo '<br /><h3>';
									echo $pseudo;
									echo ' n\'est désormais plus un administrateur du groupe.</h3>';
								}
								if(isset($_POST['supp'])){
									$idMembre = mysql_real_escape_string($_POST['idMembre']);
									deleteMembreGroupe($idMembre, $id);
									deleteDroit($idMembre, $id);
									updateDemandeByStatut($id, $idMembre, 'deleted');
									echo '<br /><h3>Le membre a bien été supprimé.</h3>';
								}
							}
						}
						echo '<h3>Membres:</h3>';
						$membres = getMembresByIdGroupe($id);
						if(empty($membres)){
							echo 'Aucun membre.';
						}
						else{
							$i = '0';
							foreach($membres as $result){
								$i++;
								$idMembre = $result->idMembre();
								$droit_1 = getDroitByIdMembreAndIdGroupe($idMembre, $id);
								$m = getMembreById($idMembre);
								$membre = $m->login();
								echo ''.$i.'. ';
								echo '<a href="groupe?m='.$membre.'">'.$membre.'</a>';
								if(isset($_SESSION['login'])){
									$m = getMembreByLogin($_SESSION['login']);
									$membre = $m->id();
									$membreGroupe = getMembreByIdGroupeAndMembre($id, $membre);
									if(!empty($membreGroupe)){				
										$pseudo = $_SESSION['login'];
										$m = getMembreByLogin($pseudo);
										$pseudo = $m->id();
										if($idMembre == $pseudo){
											echo '<small><strong> (vous)</strong></small><br />';
										}
										else{
											echo '<br />';
										}
										foreach($droit_1 as $result){
											if($result->libelle() == 'admin'){
												echo '<small><strong> (admin)</strong></small><br />';
											}
										}
										$droit = getDroitByIdMembreAndIdGroupe($pseudo, $id);
										foreach($droit as $statut){
											if($statut->libelle() == 'admin'){
												foreach($droit_1 as $result){
													if($result->libelle() != 'admin'){
														?>
														<form name="admin" method="post" action="groupe?id=<?php echo $id ?>#tab_2" onsubmit="return validateAdmin();">
															<input type="submit" name="admini" value="Passer en admin" />
															<input type="hidden" name="idMembreAdmin" value="<?php echo $idMembre?>" />
														</form>
														<form name="libelle" method="post" action="groupe?id=<?php echo $id ?>#tab_2" onsubmit="return validateDelete();">
															<input type="submit" name="supp" value="Supprimer" />
															<input type="hidden" name="idMembre" value="<?php echo $idMembre?>" />
														</form>
														<?php 
													}
													else{
														$idCreateur = $groupe->idCreateur();
														$m = getMembreByLogin($_SESSION['login']);
														$membre = $m->id();
														if($idMembre != $idCreateur){
															if($membre == $idCreateur){													
																?>
																<form name="unadmin" method="post" action="groupe?id=<?php echo $id ?>#tab_2" onsubmit="return validateUnAdmin();">
																	<input type="submit" name="unadmini" value="Enlever l'admin" />
																	<input type="hidden" name="idMembreAdminSup" value="<?php echo $idMembre?>" />
																</form>	
																<form name="libelle" method="post" action="groupe?id=<?php echo $id ?>#tab_2" onsubmit="return validateDelete();">
																	<input type="submit" name="supp" value="Supprimer" />
																	<input type="hidden" name="idMembre" value="<?php echo $idMembre?>" />
																</form>
																<?php
															}											
														}
													}
												}
											}
										}
									}
								}
								else{
									echo '<br />';
								}
							}
						}
						?>						
		      	</div>
		    	<div class="tabpage" id="tabpage_3">
		        	<h2>Les options</h2>
		        	<?php
		        	if(isset($_SESSION['login'])){	        	
			        	if($groupe->idCreateur() != $membre){
			        		?><form name="quitter_form" method="post" action="groupe?id=<?php echo $id ?>#tab_3" onsubmit="return validateQuit();">
			        			<input type="submit" name="quitter" value="Quitter le groupe" />
			       			</form><?php 
			       		}
			       		else{
							$droitMembres = $groupe->droitMembres();
							if(isset($_POST['droitMembreRead'])){
								$droit = 'write';
								updateDroitMembresGroupe($id, $membre, $droit);
								echo 'Tous les membres peuvent désormais rajouter des listes au groupe.';
							}
							elseif(isset($_POST['writeChecked'])){
								$droit = 'read';
								updateDroitMembresGroupe($id, $membre, $droit);
								echo 'Les membres ne peuvent désormais plus rajouter des listes au groupe.';
							}
							elseif($droitMembres == 'write'){
								?><form name="droit2" method="post" action="groupe?id=<?php echo $id ?>#tab_3">Est-ce qu'un membre simple peut rajouter des listes? <input type="checkbox" name="droitMembreWrite" value="write" onclick="this.form.submit(); " checked><input type="hidden" name="writeChecked" /></form><?php
							}
							elseif($droitMembres == 'read'){
								?><form name="droit1" method="post" action="groupe?id=<?php echo $id ?>#tab_3">Est-ce qu'un membre simple peut rajouter des listes? <input type="checkbox" name="droitMembreRead" value="read" onclick="this.form.submit();"></form><?php
							}
							?><form name="supprimer_form" method="post" action="groupe?id=<?php echo $id ?>#tab_3" onsubmit="return validateSupprimer();">
			        			<input type="submit" name="supp_groupe" value="Supprimer ce groupe" />
			       			</form><?php
			       			echo '<button onclick="javascript:showDivTitre();">Changer le titre du groupe</button>';
			       			echo '<div id="titre" style="display:none;">';
			       			$titre = $groupe->nom();
			       			?>
			       			<form name="modif_titre" method="post" action="groupe?id=<?php echo $id ?>#tab_3">
			   		        	<input type="text" name="titre" value="<?php echo $titre ?>" />
			   		        	<input type="submit" name="valider_titre" value="Confirmer" />
			   	        	</form>
			   	        	</div>
			   	        	<?php 	
			    			if(isset($_POST['supp_groupe'])){
			    				deleteAllMembreGroupe($id);
			    				deleteAllDemandeByGroupe($id);
			    				deleteAllDroitByGroupe($id);
			    				$idCreateur = $groupe->idCreateur();
			    				deleteGroupe($id, $idCreateur);
			    				?><META HTTP-EQUIV="Refresh" CONTENT="0; URL=membre"> <?php
			    			}
						}
			       		if(isset($_POST['quitter'])){
			       			deleteMembreGroupe($pseudo, $id);
			        		deleteDroit($pseudo, $id);
			        		deleteDemande($id, $pseudo);
			       			?><META HTTP-EQUIV="Refresh" CONTENT="0; URL=membre"> <?php
			        	}
			      	}			        	
			        ?>
		      	</div>
		    </div>
		</div>
		</div>
		<script src="javascript/tabs.js"></script>
    	<?php 
    		}
    	}
    	?>
	</div>
</div>
