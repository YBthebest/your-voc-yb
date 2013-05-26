<?php
$login = @$_SESSION['login'];
$id = @$_GET['id'];
$capchaReponse = "";
$withCapcha = "false";
$capcha = "";
$favoriOption = "{}";
$message = "";	
$voteAdded = false;
$time = time(); 


$listeMotDefinition = getListeById($id);
if(empty($listeMotDefinition)){
	header('Location: accueil');
}

if(isset($_POST['addFavoris'])) {
	createFavori($id, $login);
} else if(isset($_POST['delFavoris'])) {
	deleteFavoriByIdAndMembre($id, $login);
}

if(isset($_POST['note_submit'])) {
	$note = $_POST['note'];
	$checkVote = getVotesByIdAndPseudo($id, $login);
	if(sizeof($checkVote) !== 0){
		$message = 'Vous avez déjà voté pour cette liste.';
	} else if(is_numeric($note)) {
		if($note > 5) {
			$message = 'Un problème est apparu, veuillez réessayer.';
		}
		else if(createVote($id, $note, $login)) {
			$message = "Merci d'avoir voté.";
			$voteAdded = false;
		}
	} else {
		$message = 'Un problème est apparu, veuillez réessayer.';
	}
}

if($login != ""){
	$membre = getMembreByLogin($login);
}
if(isset($listeMotDefinition)) {
	$listeToJson = convertObjectToJS($listeMotDefinition);
	$votes = getVotesById($id);
	$votesSyze = sizeof($votes);
	if($votesSyze > 0){
		$total = 0;
		foreach($votes as $vote) {
			$total += $vote->note();
		}
		$moyenneVote = ($total / $votesSyze);
		$moyenneVote = round($moyenneVote, 2);
		if($voteAdded == true){
			updateNoteInListe($id, $moyenneVote);
		}
	}
	
	if(isset($_SESSION['login'])) {					
		$voteByLogin = getVotesByIdAndPseudo($id, $login);
		$voteByLoginSize = sizeof($voteByLogin);
		
		$sql_favoris = getFavoriByIdAndPseudo($id, $login);
		if(sizeof($sql_favoris) == 0){
			$favoriOption = "{val:'Ajouter aux favoris',name:'addFavoris'}";
		}else{
			$favoriOption = "{val:'Supprimer des favoris',name:'delFavoris'}";
		}
		
		if(isset($_SESSION['login'])) {
			require_once('recaptchalib.php');
			$publickey = "6LdsCMMSAAAAAPx045E5nK50AEwInK8YSva0jLRh";
			$capcha = recaptcha_get_html($publickey);
			$withCapcha = "true";
		}
	}
	
	if(isset($_POST['capcha'])) {
		$email = @$_POST['email'];
		$commentaire = @$_POST['commentaire'];
		if (!empty($login) && !empty($email) && !empty($commentaire) && !empty($time)) {
			require_once('recaptchalib.php');
			$privatekey = "6LdsCMMSAAAAAKYeqj37ims8IdO_mnYM4O_mH608";
			$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
			if ($resp->is_valid) {
				createCommentaire($id, $login, $time, $commentaire);
				$capchaReponse = "Commentaire ajouté avec succès : ".$commentaire;
			}else{
				// What happens when the CAPTCHA was entered incorrectly
				$capchaReponse = "Le captcha n'a pas été entré correctement. Veuillez réessayer.";
			}
		}
	}
	
	$commentaires = getCommentairesById($id);
	$retour = sizeof($commentaires);
	$commToJson = convertArrayObjectToJSArray($commentaires);
}

?>
<script type="text/javascript">
	$(function(){
		$("#login").val('<?php echo @$_SESSION['login'];?>');
		window.listeMot = <?php echo $listeToJson;?>;		

		displayTableListeMot(window.listeMot);
		$("#profilListeMot").attr('href', "profil?m="+listeMot.membre).html(listeMot.membre);
		$("#dateCommentaire").html(listeMot.date);
		$("#commentaireAuteur").html(listeMot.commentaire);
		
		var commentaires = <?php echo $commToJson;?>;
		$("#nbCommentaires").html(commentaires.length);
		$.each(commentaires, function(index, commentaireDef){
			var divComment = '<div id="'+commentaireDef.id+'"><b>'+commentaireDef.commentaire+'</b><br><small>Par <a href="profil?m=commentaireDef.membre;">'+commentaireDef.membre+'</a> le '+commentaireDef.date+'</small></div>';
			$("#commentairesMembres").append(divComment);
		});

		var capchaReponse = "<?php echo $capchaReponse?>";
		if(capchaReponse != ""){
			alert(capchaReponse);
		}
		var favori = <?php echo $favoriOption;?>;
		if(favori.val){
			$("#buttonFav").attr('value', favori.val).attr('name', favori.name);
		}	

		var withCapcha=<?php echo $withCapcha;?>;	
		if(!withCapcha){
			$("captchaForm").remove();
		}

		var message = "<?php echo $message?>";
		if(message != ""){
			alert(message);
		}
	});
	
	function displayTableListeMot(listeMotDefinition){
		if(listeMotDefinition.listeMot){
			$("#nbMots").html(listeMotDefinition.listeMot.length);
			var $table = $("#tableListeMot");
			var listeToHidden = "";
			$.each(listeMotDefinition.listeMot, function(index, data){
				var voc = data.split("=");
				listeToHidden += data + "\n";
				$table.append("<tr><td><b><span style=\"color:white;\">"+voc[0]+"</span></td><td>=</td><td><b><span style=\"color:gray;\">"+voc[1]+"</span></td></tr>");
			});
			if(listeMotDefinition.commentaire != ""){
				$("#commentaireAuteur").append("Commentaire de l'auteur : <span style=\"font-style:italic;\">" + listeMotDefinition.commentaire + "");
			}
			$("#listeMot").val(listeToHidden);
		}
	}
</script>

<div id="presentation1">
</div>

<div id="content">
	<div id="bloc">
		<div id="text-center">
			<div id="title"><a href="">Réviser cette liste</a> </div>
			
			<input id="login" type="hidden" name="membre" />
			
			<div id="titreCategorie">
				<h2><?php echo $listeMotDefinition->titre() ?> <small><?php echo $listeMotDefinition->categorie() ?> =&gt; <i><?php echo $listeMotDefinition->categorie2() ?></i> (<span id="nbMots"></span> mots)</small></h2>
			</div>
			
			<div id="vueVote">
				<?php echo $listeMotDefinition->vue() ?> vues / <?php echo $votesSyze ?> vote<?php echo (($votesSyze > 1)?"s":"").(($votesSyze == 0)?" - Pas assez de vote pour donner une moyenne. ":" - <b>Note: $moyenneVote/5</b>")?>
			</div>
			
			<br>
			
			<a href="#commentairesMembres"><small>Accéder directement aux commentaires</small></a>  
			/  
			<a href="signaler?id=<?php echo $id ?>"><small>Signaler une erreur dans la liste</small></a>
			
			<br>
			
		<?php if(isset($login)){ 
			if($voteByLoginSize == 0){
		?>
			<div id="noter">
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
			</div>
		<?php } ?>	
			<div id="favoris">	
				<form method="post" action="afficher?id=<?php echo $id ?>">
					<input type="hidden" name="membre" value="<?php echo $_SESSION['login'] ?>" />
					<input id="buttonFav" type="submit" name="favoris" value="favoris" />
				</form>
			</div>
		<?php } else { ?>		
			<small><a href="connexion">Se connecter pour noter cette liste et l'ajouter aux favoris</a></small>
		<?php } ?>
			
			<div id="combiner">
				<form method="get" action="new_combiner">
					<input type="hidden" name="id" value="<?php echo $_GET['id']?>" />
					<input type="submit" value="Combiner avec une autre liste" />
				</form>
			</div>
			
			<div id="reviser" style="margin:auto;">				
				<form method="post" action="revise" >	
					<input type="hidden" value="2" name="step" />
					<input type="hidden" value="<?php echo $_GET['id']; ?>" name="id_liste" />
					<input type="hidden" value="" id="listeMot" name="listeMot" />
					Nombre de questions à  reviser (laisser vide pour tout) :
					<input type="text" name='nbQuestion' id="nbQuestion" /><br />
					Dans quel sens voulez-vous réviser cette liste? 
					<select name="sens">
						<option value="1"><?php echo $listeMotDefinition->categorie() ?> - <?php echo $listeMotDefinition->categorie2() ?></option>
						<option value="2"><?php echo $listeMotDefinition->categorie2() ?> - <?php echo $listeMotDefinition->categorie() ?></option>
					</select><br />
					Ne pas compter les fautes de: <br />
					<input type="checkbox" name="majuscules" value="majuscules"  /> Insensible à  la casse (Your-Voc = your-voc)<br />
					<input type="checkbox" name="mfs" value="mfs" checked="checked" /> Redemander un mot faux au bout de quelques questions<br />
					<input type="submit" value="Réviser cette liste" />
					<input type="button" value="Copier la liste dans le presse papier" onclick="copyToClipboard();" />
					<br />
				</form>
				
				<div id="commentaireAuteur"></div>
				
				<div id="listeMot">
					<table  id="tableListeMot" style="text-align:left;border-spacing:10px;margin:auto; border:0;max-width: 30em;">
					</table>
					<small>
						Liste crée par <a id="profilListeMot" href=""></a>  
						le <b><span id="dateCommentaire"></span></b><br />
					</small>
				</div>
				
				<div id="commentaireContainer">
					<h2>Commentaires (<span id="nbCommentaires"></span>)</h2>
					<div id="commentairesMembres">	
					</div>
					
					<div id="captchaContainer" style="margin: auto;width: 500px; margin-top:10px;">
					<?php if(isset($membre)) {?>
						<form method="post" action="afficher?id=<?php echo $id ?>#commentairesMembres" >				
							<input type="hidden" name="pseudo" value="<?php echo $login; ?>"/>							
							<input type="hidden" name="email" value="<?php echo $membre->email(); ?>"/>
							Commentaire ou correction : 
							<textarea rows="10" cols="50" id="commentaireListe" name="commentaire"></textarea>
							<div id="captcha" style="width:350px;margin:auto;">
								<?php echo $capcha;?>
							</div>
							<input type="submit" name="capcha" value="Envoyer" />
						</form>
					<?php } else { ?>
						<h3><b>Veuillez <a href="inscription">vous inscrire</a> ou <a href="connexion">vous connecter</a> pour poster un commentaire.</b></h3>
					<?php } ?>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>