<?php
if(isset($_SESSION['login'])) {
	header('Location: membre');
	exit();
}
$login="";
$mdp = "";
setcookie('cookie_test', 'test_cookie',    strtotime("+1 year"), '/');
// on teste si le visiteur a soumis le formulaire de connexion
	if (isset($_POST['login']) && isset($_POST['pass'])) {
		$login = $_POST['login'];
		$mdp = $_POST['pass'];
		$membre = getMembre($login, $mdp);
		if (!is_string($membre)) {
			if(!isset($_COOKIE['cookie_test'])){
				echo '<div class"alert alert-error"><h3>Attention: veuillez s\'il vous plait activer les cookies par défaut dans votre navigateur pour pouvoir vous connecter. Vous allez être redirigé vers la page de connexion.</div>';
				echo '<META HTTP-EQUIV=Refresh CONTENT="5; URL=connexion">';
				echo '</div></div></div>';
				include("footer.php");
				die();
			}
			$_SESSION['login'] = $membre->login();
			if(isset($_POST['auto'])) {
				$_SESSION['id'] = $membre->id();
				initCookie($membre);
			}
			if(isset($_POST['ref'])) {
				echo '<META HTTP-EQUIV=Refresh CONTENT="1; URL='.$_POST['ref'].'">';
				$waitingText = "<div class=\"alert alert-warning\"><h3>Bienvenue ".$membre->login().". Vous allez être redirigé vers la page d'où vous provenez. Bonne visite!</h3></div>";
				
			} else {
				header('Location: membre');
				exit();
			}
		}else{
			$erreur = '<div class="alert span3 alert-danger">'.$membre.'</div>';
		}
	}
?>
<div id="presentation1"></div>
<div id="content">
<div id="bloc">
<div id="title">Connexion</div>
<?php
	if(!isset($waitingText)){	
?>
	<div class="container">
		<div class="row">
		<div class="span5">
			<form class="well form-inline" method="post">
				<input type="text" name="login" value="<?php echo $login ?>" class="input-medium" placeholder="Pseudo" required>
				<input type="password" name="pass" class="input-medium pull-right" placeholder="Mot de passe" required>
				<div class="controls"> <br/>
				  <label class="checkbox" style="color: black">
					<input name="auto" id="options" type="checkbox" value="option1" checked>
					Se souvenir de moi </label>
					<?php
					if(isset($_SERVER['HTTP_REFERER'])) {
						$referer = $_SERVER['HTTP_REFERER'];
					?>
						<input type="hidden" name="ref" value="<?php echo $referer ?>" />
					<?php
					}
					?>
				  <button type="submit" class="btn btn-primary btn-small pull-right" name="connexion" value="connexion"> <i class="icon-user icon-white"></i> Envoyer</button>
				</div>
			</form>
		</div>
		</div>
	</div>
<?php 
	}else{
		echo $waitingText;
	}
?>
<br />
<a href="inscription">Pas encore inscrit ?</a><br />
<a href="forgot">Mot de passe oublié?</a>
<?php if (isset($erreur)) echo '<br /><br />',$erreur; ?>
			</div>
        </div>
        <!-- Fin du contenu -->
