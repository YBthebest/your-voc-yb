<?php
if(isset($_SESSION['login'])) {
	header('Location: membre');
	exit();
}
if(isset($_GET['t'])){
	$token = mysql_real_escape_string($_GET['t']);
	$mdpOublie = getMdpOublieByToken($token);
	if(!empty($mdpOublie)){
		foreach($mdpOublie as $resultMdp){
			$id_membre = $resultMdp->idMembre();
			$dateExpire = $resultMdp->dateExpire();
			$used = $resultMdp->used();
			if($used == 'no'){
				if($dateExpire > time()){
					$modify = '
					<p>Veuillez à présent entrez le nouveau mot de passe que vous voulez utiliser sur votre compte <?php echo $pseudo ?>.<br />
					Ne l\'oubliez pas cette fois-ci!</p><br />
					<form id="modify" method="post">
						Nouveau mot de passe: <input type="password" name="mdp" /><br />
						Confirmation: <input type="password" name="mdp_confirm" /><br />
						<input type="hidden" name="id_membre" value="'.$id_membre.'" />
						<input type="hidden" name="token" value="'.$token.'" />
						<input type="submit" name="valid" value="Valider" />
					</form>';
				}
				else{
					$erreur = "Votre clé d'activation n'est plus valable. Veuilez faire une nouvelle demande.";
				}
			}
			else{
				$erreur = "Votre clé d'activation a déjà été utilisée. Veuillez refaire une demande.";
			}
		}
	}
	else{
		$erreur = "La clé entrée n'est pas valable.";
	}
}
if(isset($_POST['valid'])){
	$mdp = mysql_real_escape_string($_POST['mdp']);
	$confirm = mysql_real_escape_string($_POST['mdp_confirm']);
	if($mdp == $confirm){
		$membre = getMembreById($id_membre);
		$pseudo = $membre->login();
		$mdp = md5($mdp);
		$id_membre = mysql_real_escape_string($_POST['id_membre']);
		$token = mysql_real_escape_string($_POST['token']);
		if(updateMdpByLogin($mdp, $pseudo)){
			updateUsedByTokenAndPseudo($token, $id_membre);
			$success = "Votre mot de passe a bien été mis à jour. Ne l'oubliez plus! Bonne journée.";
		}else{
			$erreur = "Un problème est survenu. Veuillez réessayer.";
		}
	}
	else{
		$erreur = 'Les deux mots de passe ne correspondent pas.';
	}
}
if(isset($_POST['confirm'])){
	require_once('recaptchalib.php');
	$privatekey = "6LdsCMMSAAAAAKYeqj37ims8IdO_mnYM4O_mH608";
	$resp = recaptcha_check_answer ($privatekey,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);
		
	if (!$resp->is_valid) {
		// What happens when the CAPTCHA was entered incorrectly
		$erreur = "Le captcha n'a pas été entré correctement. Veuillez réessayer.";
	}
	else{
		$email = mysql_real_escape_string($_POST['email']);
		$pseudo = mysql_real_escape_string($_POST['pseudo']);
		$date = time();
		$dateExpire = (time() + 3600);
		$tokenCode = bin2hex(openssl_random_pseudo_bytes('12'));
		if(isValidMail($email)){
			$requete = getMembreByLogin($pseudo);
			if(!empty($requete)){
				$m = getMembreByLogin($pseudo);
				$id_membre = $m->id();
				$email_reponse = $requete->email();
				if($email_reponse == $email){
					$token = getTokenNotUsedByPseudo($id_membre);
					if(!empty($token)){
						foreach($token as $resultToken){
							if(time() < ($resultToken->date() + 600)){
								$erreur = "Vous avez fait une demande de nouveau mot de passe il y a moins de 10 minutes. <br />Veuillez re-vérifier votre boite de réception ou attendre quelques minutes.";
								break;							
							}
							else{
								createToken($id_membre, $tokenCode, $date, $dateExpire);	
								$to = '"'.$pseudo.'" <<a href="mailto:'.$email.'">'.$email.'</a>>';
								$subject = "Your-Voc: mot de passe oublié";
								$message = "Voici les instructions à suivre pour créer un nouveau mot de passe:
								Veuillez suivre le lien suivant, qui ne sera valable que pendant une heure. Ce temps passé, il vous faudra recommencer le procesus entier.
								----------------------------
								http://www.your-voc.com/forgot?t=$tokenCode
								----------------------------
								Ceci est un e-mail généré automatiquement. Veuillez bien ne pas y répondre.
								
								Merci de votre compréhension et bon apprentisage sur Your-Voc.
								";
								$headers = 'From: "Your-Voc" <<a href="mailto:reset@your-voc.com">reset@your-voc.com</a>>' . PHP_EOL .
								'X-Mailer: PHP/' . phpversion();
								
								if (!mail($to, $subject, $message, $headers)) {
									$erreur = "Un problème a apparu. Veuillez-nous contacter. (<a href='contact'>Contact</a>)";
								}else{
									$success = "Vous allez désormais recevoir un e-mail contenant les instructions à suivre pour créer un nouveau mot de passe. <br />Dans une heure, l'email ne sera plus valide.";
								}
								break;
							}
						}					
					}
					else{
						createToken($id_membre, $tokenCode, $date, $dateExpire);
						$to = '"'.$pseudo.'" <<a href="mailto:'.$email.'">'.$email.'</a>>';
						$subject = "Your-Voc: mot de passe oublié";
						$message = "Voici les instructions à suivre pour créer un nouveau mot de passe:
						Veuillez suivre le lien suivant, qui ne sera valable que pendant une heure. Ce temps passé, il vous faudra recommencer le procesus entier.
						----------------------------
						http://www.your-voc.com/forgot?t=$tokenCode
						----------------------------
						Ceci est un e-mail généré automatiquement. Veuillez bien ne pas y répondre.
								
						Merci de votre compréhension et bon apprentisage sur Your-Voc.
						";
						$headers = 'From: "Your-Voc" <<a href="mailto:reset@your-voc.com">reset@your-voc.com</a>>' . PHP_EOL .
						'X-Mailer: PHP/' . phpversion();
							
						if (!mail($to, $subject, $message, $headers)) {
							$erreur = "Un problème a apparu. Veuillez-nous contacter. (<a href='contact'>Contact</a>)";
						}else{
							$success = "Vous allez désormais recevoir un e-mail contenant les instructions à suivre pour créer un nouveau mot de passe. <br />Dans une heure, l'email ne sera plus valide.";							
						}
					}
				}
				else{
					$erreur = "L'email renseigné n'est pas en accord avec le pseudo enseigné.";
				}
			}
			else{
				$erreur = 'Aucun compte est associé à ce pseudo.';
			}
		}
		else{
			$erreur = "L'email rentré n'est pas valide.";
		}
	}
}
?>
<div id="presentation1"></div>
<div id="content">
	<div id="bloc">
		<div id="title">Mot de passe oublié</div>
		<?php 
		if(isset($erreur)){
			echo '<span style="color:red"><strong>'.$erreur.'</strong></span>';
		}
		if(isset($success)){
			echo '<span style="color:green"><strong>'.$success.'</strong></span>';
		}elseif(isset($modify)){
			echo $modify;
		}else{
		?>
		<p>Si vous avez oublié votre mot de passe, veuillez renseigner l'email et le pseudo avec lesquels vous vous étiez enregistrés précédemment.<br />
		Un e-mail contenant les instructions à suivre vous sera ensuite envoyé.</p>
		<form id="mdp_oublie" method="post">
			Pseudo: <input type="text" name="pseudo" /><br />
			Email: <input type="text" name="email" /><br />
			<?php  require_once('recaptchalib.php');
			$publickey = "6LdsCMMSAAAAAPx045E5nK50AEwInK8YSva0jLRh"; // you got this from the signup page
			echo recaptcha_get_html($publickey);
			?>
			<input type="submit" name="confirm" value="Ok" />
		</form>
		<?php 
		}
		?>
	</div>
</div>