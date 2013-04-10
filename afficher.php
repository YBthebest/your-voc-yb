<?php
ini_set('display_errors',1);
if(isset($_POST['favoris'])) {
	if(createFavori($_GET['id'], $_POST['membre'])) {
		echo 'Ajouté aux favoris!';
	}
}
if(isset($_POST['retirer'])) {
	$id_liste = mysql_real_escape_string($_GET['id']);
	$membre = $_POST['membre'];
	if(deleteFavoriByIdAndMembre($id_liste, $membre)) {
		echo 'Supprimé des favoris!';
		?><META HTTP-EQUIV="Refresh" CONTENT="1; URL=afficher?id=<?php echo $_GET['id']?>"><?php
				}
			}
			if(isset($_POST['note_submit'])) {
				$id_liste = mysql_real_escape_string($_GET['id']);
				$id = mysql_real_escape_string($_GET['id']);
				$pseudo = $_SESSION['login'];		
				$note = $_POST['note'];
				$checkVote = getVotesByIdAndPseudo($id_liste, $pseudo);
				if(sizeof($checkVote)!= 0){
					echo 'Vous avez déjà voté pour cette liste.';
					die('<meta http-equiv="refresh" content="2">'); 
				}
				if(is_numeric($note)) {
					if($note > 5) {
						echo 'Un problème est apparu, veuillez réessayer.';
					}
					else {
						if(createVote($id_liste, $note, $pseudo)) {
							echo 'Merci d\'avoir voté.';
							$requete_note1 = getVotesById($id);
							$resultat_note1 = sizeof($requete_note1);
							$total = 0;
							foreach($requete_note1 as $vote) {
								$total += $vote->note();
							}
							$resultat_final1 = ($total / $resultat_note1);
							$resultat_final1 = round($resultat_final1, 2);
							updateNoteInListe($id_liste, $resultat_final1);
						}
					}
				}
				else {
					echo 'Un problème est apparu, veuillez réessayer.';
				}
			}
			if(isset($_GET['id']) AND !empty($_GET['id'])) {
				$time = time(); 
				$id = mysql_real_escape_string($_GET['id']);
				$listeMotDefinition = getListeById($id);
				if(!empty($listeMotDefinition)) {
					$listeMotDefinition = $listeMotDefinition[0];
					$listeToJson = json_encode($listeMotDefinition);
					print_r($listeMotDefinition);
					$fonction = $listeMotDefinition;
						$titre = $fonction->titre();
						$categorie = $fonction->categorie();
						$categorie2 = $fonction->categorie2();
						$liste = $fonction->listeMot();
						$vues = $fonction->vue();
						$pseudo = $fonction->membre();
						$date = $fonction->date();
						if($fonction->commentaire() != '') {
							$commentaire = $fonction->commentaire();
						}
						$lignes = 0;
						$lignes = explode("\n", $liste);
						$nombre_lignes = 0;
						$nombre_lignes = count($lignes);
						$mot_present = 0 ;
						$question = array();
						$o = 0;
						echo '<h2>'.$titre.' - <small>'.$categorie.' -> '.$categorie2.' ('.$nombre_lignes.' mots)</small></h2>';
						$requete_note = getVotesById($id);
						$resultat_note = sizeof($requete_note);
						echo ''.$vues.' vues / '.$resultat_note.' votes / ';
						if($resultat_note < 1){
							echo 'Pas assez de vote pour donner une moyenne.  <br />';
						}
						else {
							$total = 0;
							foreach($requete_note as $sql) {
								$total = ($total + $sql->note());
							}
							$resultat_final = ($total / $resultat_note);
							$resultat_final = round($resultat_final, 2);
							echo '<b>Note: '.$resultat_final.'/5</b> .   <br />';
						}
						if(isset($_SESSION['login'])) {
							$pseudo = $_SESSION['login'];
							$query_note = getVotesByIdAndPseudo($id, $pseudo);
							$nombre_vote = sizeof($query_note);
							if($nombre_vote != 0) {
								echo 'Vous avez déjà  voté pour cette liste.<br />';
							}
							else {
								?>
								<form action="afficher?id=<?php echo $_GET['id'] ?>" method="post" >
								<input type="hidden" name="nbMots" id="nbMots" value="<?php echo $nombre_lignes ?>"/>  
								<p><select name="note" id="note">
									   <option value="1">1</option>
									   <option value="2">2</option>
									   <option value="3">3</option>
									   <option value="4">4</option>
									   <option value="5">5</option>
								   </select>
								   <input type="submit" name="note_submit" value="Noter cette liste" />
								   </p></form>
								 <?php
							}
						}
						echo '<a href="#commentaire"><small>Accéder directement aux commentaires</small></a>   /    ';
						?><a href="signaler?id=<?php echo $id ?>"><small>Signaler une erreur dans la liste</small></a><?php
						if(isset($_SESSION['login'])) {
						$membre = $_SESSION['login'];
						$sql_favoris = getFavoriByIdAndPseudo($id, $membre);
						$resultat_fav = sizeof($sql_favoris);
						if($resultat_fav == 0) {
								?>
								<form method="post" action="afficher?id=<?php echo $id ?>">
								<input type="hidden" name="membre" value="<?php echo $_SESSION['login'] ?>" />
								<input type="hidden" name="favoris" value="oui" />
								<input type="submit" value="Ajouter aux favoris" />
								</form>
								<?php
						} elseif($resultat_fav != 0) {
								echo '  /   Cette liste est dans vos favoris.';
								?>
								<form method="post" action="afficher?id=<?php echo $id ?>">
								<input type="hidden" name="membre" value="<?php echo $_SESSION['login'] ?>" />
								<input type="hidden" name="retirer" value="oui" />
								<input type="submit" value="La retirer des favoris?" />
								</form>
								<?php
						}
					}
						else {
							echo '<br /><small><a href="connexion">Se connecter pour noter cette liste et l\'ajouter aux favoris</a></small>';
						}
						?>
						<form method="get" action="new_combiner">
							<input type="hidden" name="id" value="<?php echo $_GET['id']?>" />
							<input type="submit" value="Combiner avec une autre liste" />
						</form>
						<?php
							$lignes = 0;
							$lignes = explode("\n", $liste);
							$nombre_lignes = 0;
							$nombre_lignes = count($lignes);
							$mot_present = 0 ;
							$question = array();
								$o = 0;
							$liste_new = '';
							$liste_new .= '<center><div id="table"><table border=0 cellspacing=20 style="max-width: 30em;">';
							for( $i = 0 ; $i < $nombre_lignes ; $i++) {
								// on separe les 2 mots
								$mot = explode("=", $lignes[$i]);
								// Si utilisateur a correcctement utiliser , il aura 2 mot
								// Si mal fait , on ignore cette ligne
								if( count($mot) == 2 ) {
									// On retire les espace que utilisateur a peut etre laisser
									$mot[0] = trim($mot[0]);    //l1
									$mot[1] = trim($mot[1]);	//l2
									
				
									$liste_new .= '
													<tr>
														<td><b><span style="color: white">'.$mot[0].'</span></b></td>
														<td>=</td>
														<td><b><span style="color: gray">'.$mot[1].'</b></td>
													</tr>
													';
								}
							}
							$liste_new .= '</table></div>';	
						echo "<form method=\"post\" action=\"revise\" >				
									<p><input type=\"hidden\" value=\"2\" name=\"step\" />
									<input type=\"hidden\" value=\"".$_GET['id']."\" name=\"id_liste\" />
									<input type=\"hidden\" value=\"".$liste."\" name=\"new_mot\" />
									Nombre de questions à  reviser (laisser vide pour tout) :
									<input type=\"text\" name='nbQuestion' id=\"nbQuestion\" /><br />
									Dans quel sens voulez-vous réviser cette liste? 
									<select name=\"sens\">
										<option value=\"1\">".$categorie."-".$categorie2."</option>
										<option value=\"2\">".$categorie2."-".$categorie."</option>
									</select><br />
									Ne pas compter les fautes de: <br />
									<input type=\"checkbox\" name=\"majuscules\" value=\"majuscules\"  /> Insensible à  la casse (Your-Voc = your-voc)<br />
									<input type=\"checkbox\" name=\"mfs\" value=\"mfs\" checked=\"checked\" /> Redemander un mot faux au bout de quelques questions<br />
									<input type=\"submit\" value=\"Réviser cette liste\" />
									<input type=\"button\" value=\"Copier la liste dans le presse papier\" onclick=\"copyToClipboard();\" />
									<br />
									</p></form>";
						
						if(isset($commentaire)) {
							echo '<br /><i>Commentaire de l\'auteur: '.$commentaire.'</i><br />';
						}
						echo $liste_new;
						?><div id="revise"><?php
						echo '<small>Liste envoyée par par ';
						?><a href="profil?m=<?php echo $pseudo?>"><?php echo $pseudo?></a>  
						<?php echo 'le <b>'.$date.'</b><br /></small>';
						echo '<div id="commentaire">';
						$retour = countNbCommentairesById($id);
						echo '<h2>Commentaires ('.$retour.')</h2><br />';
						if($retour != 0) {
							$comm = getCommentairesById($id);
							foreach($comm as $comm_r){
								echo '<b>'.$comm_r->commentaire().'</b><br />';
								echo '<small>Par <a href="profil?m='.$comm_r->membre().'">'.$comm_r->membre().'</a> le '.$comm_r->date().'</small><br /><br />';
							}
						} else {
							echo 'Il n\'y a aucun commentaire pour cette liste.<br /><br />';
						}
				if(isset($_POST['submit'])) {
							if ((isset($_POST['pseudo']) && !empty($_POST['pseudo'])) && (isset($_POST['email']) && !empty($_POST['email'])) && (isset($_POST['commentaire']) && !empty($_POST['commentaire']) && !empty($time))) {
								require_once('recaptchalib.php');
								$privatekey = "6LdsCMMSAAAAAKYeqj37ims8IdO_mnYM4O_mH608";
								$resp = recaptcha_check_answer ($privatekey,
															  $_SERVER["REMOTE_ADDR"],
															  $_POST["recaptcha_challenge_field"],
															  $_POST["recaptcha_response_field"]);
							
								if (!$resp->is_valid) {
								// What happens when the CAPTCHA was entered incorrectly
								echo ("<b>Le captcha n'a pas été entré correctement. Veuillez réessayer. </b><br /><br />");
								} else {
									$email = $_POST['email'];
									$commentaire = $_POST['commentaire'];
									$pseudo = $_POST['pseudo'];
									if(isset($_SESSION['login'])) {
										$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
										if(!preg_match($regex, $email)) {
											 echo 'L\'email entré est invalide.';
										}
										else {
											$id_liste = htmlspecialchars(mysql_real_escape_string($id));
											$pseudo = htmlspecialchars(mysql_real_escape_string($pseudo));
											$time = htmlspecialchars(mysql_real_escape_string($time));
											$commentaire = htmlspecialchars(mysql_real_escape_string($commentaire));
											createCommentaire($id_liste, $pseudo, $time, $commentaire);
											echo 'Votre commentaire a bien été sauvegardé. <br />';	
											?><META HTTP-EQUIV="Refresh" CONTENT="0; URL=afficher?id=<?php echo $_GET['id']?>#commentaire"><?php													
										}
									}			
								}
							}
							else {
								echo '<b>Au moins un champ est vide. Veuillez réessayer.</b>';
							}
				
						}
						?><br />
						<?php 
						if(isset($_SESSION['login'])) {
						?>
						<form method="post" action="afficher?id=<?php echo $id ?>#commentaire">
						<?php
							$pseudo = $_SESSION['login'];
							$result = getMembreByLogin($pseudo);
							foreach($result as $result1){
								echo 'Connecté en tant que <b>'.$_SESSION['login'].'</b>.<br /> Pas vous? <a href="deconnexion">Déconnectez-vous!</a> <input type="hidden" name="pseudo" value='.$pseudo.' />  <input type="hidden" name="email" value="'.$result1->email().'" />';
							}						
							?>
							<br />Commentaire ou correction: <br /><textarea name="commentaire" rows="10" cols="50"></textarea><br />
							<input type="submit" name="submit" value="Envoyer" /></p></form><br /><?php
							}else{
								echo '<h3><b>Veuillez <a href="inscription">vous inscrire</a> ou <a href="connexion">vous connecter</a> pour poster un commentaire.</b></h3>';
							}
						    ?>
							<div></center><?php
				}
				else {
					echo 'Veuillez préciser un id valable svp.';
				}
			}
			else{
				echo 'Veuillez préciser un id valable svp.';
			}

?>
<script type="text/javascript">
	$(function(){
		$("#login").val(<?php echo @$_SESSION['login'];?>);
		var liste = <?php echo str_replace('\\', '\\\\',$listeToJson);?>;		
		displayTableListeMot(liste);

		var commentaires = <?php echo str_replace('\\', '\\\\',$listeToJson);?>;
	})
	
	function displayTableListeMot(listeMotDefinition){
		var listeMot = listeMotDefinition.listeMot.replace(/\\[/]/g, "/");
		listeMotDefinition.listeMot = listeMot.split("\\r\\n");
		var $table = $("#tableListeMot");
		$.each(listeMotDefinition.listeMot, function(index, data){
			var voc = data.split("=");
			$table.append("<tr><td><b><span style=\"color:white;\">"+voc[0]+"</span></td><td>=</td><td><b><span style=\"color:gray;\">"+voc[1]+"</span></td></tr>");
		})
		if(listeMotDefinition.commentaire != ""){
			$("#commentaireAuteur").append("Commentaire de l'auteur : <span style=\"font-style:italic;\">" + listeMotDefinition.commentaire + "");
		}
	}
</script>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
			<div id="title"><a href="">Réviser cette liste</a> </div>
			
			<input id="login" type="hidden" name="membre" />
			
			<div id="titreCategorie">
				<h2><?php echo $titre ?> <small><?php echo $categorie ?> =&gt; <?php echo $categorie2 ?> (<?php echo $nombre_lignes ?> mots)</small></h2>
			</div>
			
			<div id="vueVote">
				<?php echo $vues ?> vues / <?php echo $resultat_note ?> vote<?php echo ($resultat_note > 1)?"s":($resultat_note == 0)?" / Pas assez de vote pour donner une moyenne. ":" / <b>Note: '.$resultat_final.'/5</b>"?>
			</div>
			
			<br>
			
			<a href="#commentaire"><small>Accéder directement aux commentaires</small></a>  
			/  
			<a href="signaler?id=<?php echo $id ?>"><small>Signaler une erreur dans la liste</small></a>
			
			<br>
			<div id="noter">
				<?php if(isset($_SESSION['login'])){ ?>
				<form action="afficher?id=<?php echo $_GET['id'] ?>" method="post" >
					<input type="hidden" name="nbMots" id="nbMots" value="<?php echo $nombre_lignes ?>"/>  
					<p>
						<select name="note" id="note">
						   <option value="1">1</option>
						   <option value="2">2</option>
						   <option value="3">3</option>
						   <option value="4">4</option>
						   <option value="5">5</option>
					   	</select>
					   	<input type="submit" name="note_submit" value="Noter cette liste" />
					</p>
				</form>
				<?php } else { ?>
				<small><a href="connexion">Se connecter pour noter cette liste et l'ajouter aux favoris</a></small>
				<?php } ?>
			</div>
			
			<div id="combiner">
				<form method="get" action="new_combiner">
					<input type="hidden" name="id" value="<?php echo $_GET['id']?>" />
					<input type="submit" value="Combiner avec une autre liste" />
				</form>
			</div>
			
			<div id="favoris">	
				<form method="post" action="afficher?id=<?php echo $id ?>">
					<input type="hidden" name="membre" value="<?php echo $_SESSION['login'] ?>" />
					<input type="hidden" name="favoris" value="oui" />
					<input id="buttonFav" type="button" value="favoris" />
				</form>
			</div>
			
			<div id="reviser" style="margin:auto;">				
				<form method="post" action="revise" >				
					<p>
						<input type="hidden" value="2" name="step" />
						<input type="hidden" value="<?php echo $_GET['id'] ?>" name="id_liste" />
						<input type="hidden" value="<?php echo $liste ?>" name="new_mot" />
						Nombre de questions à  reviser (laisser vide pour tout) :
						<input type="text" name='nbQuestion' id="nbQuestion" /><br />
						Dans quel sens voulez-vous réviser cette liste? 
						<select name="sens">
							<option value="1"><?php echo $categorie ?> - <?php echo $categorie2 ?></option>
							<option value="2"><?php echo $categorie2 ?> - <?php echo $categorie ?></option>
						</select><br />
						Ne pas compter les fautes de: <br />
						<input type="checkbox" name="majuscules" value="majuscules"  /> Insensible à  la casse (Your-Voc = your-voc)<br />
						<input type="checkbox" name="mfs" value="mfs" checked="checked" /> Redemander un mot faux au bout de quelques questions<br />
						<input type="submit" value="Réviser cette liste" />
						<input type="button" value="Copier la liste dans le presse papier" onclick="copyToClipboard();" />
						<br />
					</p>
				</form>
				
				<div id="commentaireAuteur">
					
				</div>
				
				<div id="listeMot">
					<table  id="tableListeMot" style="text-align:left;border-spacing:20px;margin:auto; border:0;max-width: 30em;">
					</table>
				</div>	
				
				<div id="commentairesMembres">
					<small>
						Liste crée par <a href="profil?m=<?php echo $pseudo?>"><?php echo $pseudo?></a>  
						le <b><?php echo $date ?></b><br />
					</small>
					<div id="commentaire">
						<h2>Commentaires (<?php echo $retour ?>)</h2>
						<?php 
						if($retour != 0) {
							$comm = getCommentairesById($id);
							foreach($comm as $comm_r){
							?>
								<div><b><?php echo $comm_r->commentaire(); ?></b></div>
								<small>Par <a href="profil?m=<?php echo $comm_r->membre(); ?>"><?php echo $comm_r->membre(); ?></a> le <?php echo $comm_r->date(); ?></small>
							<?php 
							}
						} else {
							?>
								<div>Il n'y a aucun commentaire pour cette liste</div>
						<?php }?>
					</div>
					<div id="commenter" style="margin: auto;width: 500px;">
						<?php 
							if(isset($_SESSION['login']) &&  $_SESSION['login'] != $listeMotDefinition->membre()) {
						?>
							Commentaire ou correction : 
							<textarea rows="10" cols="50" id="commentaireListe">test</textarea>
							<div id="captcha" style="width:350px;margin:auto;">
						<?php		
								require_once('recaptchalib.php');
								$publickey = "6LdsCMMSAAAAAPx045E5nK50AEwInK8YSva0jLRh";
								echo recaptcha_get_html($publickey);
							}
						?>
							</div>
							<input type="button" name="btnCommenter" onclick="" value="Envoyer" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>