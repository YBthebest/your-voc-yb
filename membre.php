<?php
	if (!isset($_SESSION['login'])) {
		header ('Location: accueil');
		exit();
	} 
?>
<script type="text/javascript">
function validateDelete(){
	return confirm("Voulez-vous vraiment supprimer cette combinaison?");
}
</script>
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
			<div id="title">Espace membre </div>
			<div id="container">
				<div id="col1">
					<h3>Vos 5 dernières combinaisons</h3>
					<?php
					$pseudo = $_SESSION['login'];
					if(isset($_POST['idListeCombi'])){
						$idCombi = $_POST['idListeCombi'];
						$m = getMembreByLogin($pseudo);
						$id_membre = $m->id();
						if(deleteCombinaisonByIdAndMembre($idCombi, $id_membre)){
							echo '<h3>Votre combinaison a bien été supprimée.</h3>';
						}
					}
					$query = getCombinaisonByPseudoLimit5($pseudo);
					$y = 1;
					if(sizeof($query) == 0) {
						echo 'Aucune combinaison créée. <br> <a href="combiner">Commencer maintenant</a> !';
					}
					else {
						foreach($query as $resultat1) {
							$titre = $resultat1->titre();
							$id = $resultat1->id_liste();
							$liste = $resultat1->liste();
							echo "$y. $titre"  
					?>
							<form method="post" action="revise"> 
								<input type="hidden" name="reviseCombi" value="ok" />
								<input type="hidden" name="reviseCombiMots" value="<?php echo $liste ?>" />
								<input type="hidden" name="titreCombi" value="<?php echo $titre ?>" />
								<input type="submit" name="combiner" value="Réviser cette combinaison" />
							</form>
							<form method="post" action="membre" name="supprimerCombi" onsubmit="return validateDelete();">
								<input type="hidden" name="idListeCombi" value="<?php echo $resultat1->id() ?>" />
								<input src="images/delete.png" type=image type="submit" name="supprimer" value="Supprimer cette combinaison" />
							</form>
							<br> 
					<?php
							$y++;
						}
					}
					?>
					<a href="?page=membre-all">Toutes les voir</a><br />
				</div>
				<div id="col2outer"> 
					<div id="col2mid">
						<p>
							<h3>Bienvenue <?php echo htmlentities(trim($_SESSION['login'])); ?>!</h3>
							<strong><a href="ajouter-groupe" >Créer un groupe de révision</a></strong><br/>		
							<a href="gerer-listes" >Gerer et réviser ses listes</a><br/>			
							<a href="entrer-liste" >Entrer une nouvelle liste</a><br/>
							<a href="recherche" >Faire une recherche</a><br/>
							<a href="mdp">Modifier mon mot de passe</a><br/>
							<a href="deconnexion">Déconnexion</a><br/>
						</p>
						<h3>3 dernières listes révisées</h3>
						<?php
						$listeRevisions = getRevisionsByPseudoLimit3($pseudo);
						$i = 1;
						if(sizeof($listeRevisions) == 0) {
							echo 'Aucune liste révisée.<br><a href="?page=gerer-public">Commencer maintenant</a> !';
						} else {
							foreach($listeRevisions as $revision) {
								$idListeMot = $revision->id_liste();
								if(empty($idListeMot) || $idListeMot == 'no') {
									$displayListe = 'Mots entrés par vous pour une utilisation unique';
								} else {
									$listeMot = getListeById($idListeMot);
									if(empty($listeMot)){
										$displayListe = 'Liste supprimée';		
									}else{
										$displayListe = '<a href="afficher?id='.$idListeMot.'">'.$listeMot->titre().'</a>';
									}
								}
								?><?php echo $i ?>. <?php echo $displayListe ?> - <b>Moyenne de la révision: <?php echo $revision->moyenne() ?>%</b> - <small>Revisé le <?php echo $revision->date()?>. </small><br /><br /> <?php
								$i++;
							}
						}
						?>
						<br><a href="?page=membre-all">Tout voir</a><br>		
					</div>
					<div id="col2side">
						<h3>Groupes</h3> 
						<?php 
						$membre = $_SESSION['login'];
						$m = getMembreByLogin($membre);
						$idMembre = $m->id();
						$groupes = getMembreGroupeByIdMembre($idMembre);
						if(empty($groupes)){
							echo 'Vous ne faites partis d\'aucun groupe. <a href="ajouter-groupe">Rejoignez-en un!</a>.';
						}
						else{
							$i = '0';
							foreach($groupes as $groupeMembre){
								if($i == 5){
									break;
								}
								$i++;
								echo ''.$i.'. ';
								$idGroupe = $groupeMembre->idGroupe();
								$result = getGroupeById($idGroupe);
								?><a href="groupe?id=<?php echo $result->id()?>"><?php echo $result->nom()?></a><br /><?php	
							}
						}
						?>
						<h3>Favoris</h3>
						<?php
						$listeFavoris = getFavoriByPseudoLimit20($membre);
						$nombre = sizeof($listeFavoris);
						if($nombre == 0){
							echo "Vous n'avez aucune liste en favoris.";
						}
						else {
							$i = 1;
							foreach($listeFavoris as $favoris) {
								$listeMots = getListeById($favoris->id_liste());
								echo ''.$i++.'. ';
								?><a href="afficher?id=<?php echo $listeMots->id() ?>"><?php echo $listeMots->titre() ?></a> - <small><?php echo $listeMots->categorie() ?> <-> <?php echo $listeMots->categorie2() ?></small><br /><?php
							}
						}
						?>	
						<br><a href="?page=membre-all">Tout voir</a><br>	
					</div>
				</div>
			</div> 
		</div>
	</div>
</div> 