<?php
if (!isset($_SESSION['login'])) {
	header ('Location: accueil');
	exit();
} 
?>
	
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
			<div id="title">Supprimer mon compte - A VERIFIER</div>
			<p>Pour supprimer votre compte, entrez votre mot de passe.<br />
			<h2>ATTENTION: cette action est irréversible. Vos données seront effacées de notre base de données (vos listes seront gardées sous un autre pseudo.)</h2>
			<div id="formulaire_mdp" >
				<form name="form" method="post">
					<p><label for="mdp">Mot de passe :</label> <input type="password" name="mdp" /><br />
					<label for="mdp_confirm">Confirmez-le :</label> <input type="password" name="mdp_confirm" /><br />
					<input type="submit" name="valider" value="Valider" /><br /></p>
				</form> 
			</div>
			<?php
			if(isset($_POST['valider'])) {
				$login = $_SESSION['login'];
				$membre = getMembreByLogin($login);
				$idMembre = $membre->id();
				if($membre->pass() == md5($_POST['mdp'])) {
					if($_POST['mdp'] != $_POST['mdp_confirm']) {
						echo "Les mots de passes ne concordent pas.<br />";
					} else {
						deleteAllCommentairesByIdMembre($idMembre);
						deleteAllDemandeByMembre($idMembre);
						deleteAllDroitByMembre($idMembre);
						deleteAllFavorisByMembre($idMembre);
						echo 'ok';
					}
				} else {
					echo 'Le mot de passe entré est faux. <br />';
				}
			}
			?>
	</div>
</div>