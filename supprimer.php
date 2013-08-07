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
			<div id="title">Supprimer mon compte</div>
			<div class="alert alert-info">Pour supprimer votre compte, entrez votre mot de passe.</div>
			<div class="alert alert-error">ATTENTION: cette action est irréversible. Vos données seront effacées de notre base de données (vos listes seront gardées sous un autre pseudo.)</div>
			<div class="row">
				<div class="span5">
					<form name="form" method="post" class="well form-inline">
						<div class="control-group">
							<div class="controls">
								<input type="password" name="mdp" placeholder="Mot de passe" required />
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<input type="password" name="mdp_confirm" placeholder="Confirmez-le" required/><br />
								<button type="submit" class="btn btn-primary" name="valider"><i class="icon-remove icon-white"></i>Valider</button>
							</div>
						</div>
					</form> 
				</div>
			</div>
			<?php
			if(isset($_POST['valider'])) {
				$login = $_SESSION['login'];
				$membre = getMembreByLogin($login);
				$idMembre = $membre->id();
				if($membre->pass() == md5($_POST['mdp'])) {
					if($_POST['mdp'] != $_POST['mdp_confirm']) {
						echo '<div class="alert alert-error">Les mots de passes ne concordent pas.</div>';
					} else {
						deleteAllCommentairesByIdMembre($idMembre);
						deleteAllDemandeByMembre($idMembre);
						deleteAllDroitByMembre($idMembre);
						deleteAllFavorisByMembre($idMembre);
						deleteAllCombinaisonsByMembre($idMembre);
						deleteAllListesGroupeByMembre($idMembre);
						deleteAllMembresGroupeByMembre($idMembre);
						deleteAllRevisionsByMembre($idMembre);
						deleteAllVotesByMembre($idMembre);
						updateMembreListe($idMembre, '148');
						$getGroupe = getGroupeByIdCreateur($idMembre);
						if(!empty($getGroupe)){
							foreach($getGroupe as $deleteGroupe){
								$idGroupeADelete = $deleteGroupe->id();
								deleteAllMembreGroupe($idGroupeADelete);
								deleteAllDemandeByGroupe($idGroupeADelete);
								deleteAllDroitByGroupe($idGroupeADelete);
								deleteAllListesByGroupe($idGroupeADelete);
								deleteGroupe($idGroupeADelete, $idMembre);
							}
						}
						deleteMembreById($idMembre);
						session_unset();
						session_destroy();
						setcookie('id');
						unset($_COOKIE['id']);
						setcookie('connexion_auto');
						unset($_COOKIE['connexion_auto']);
						header('Location: accueil');
						exit();
						?><META HTTP-EQUIV="Refresh" CONTENT="0; URL=accueil"> <?php
					}
				} else {
					echo '<div class="alert alert-error">Le mot de passe entré est faux.</div>';
				}
			}
			?>
	</div>
</div>