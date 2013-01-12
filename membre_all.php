<?php
	if (!isset($_SESSION['login'])) {
		header ('Location: accueil');
		exit();
	} 
?>
<!-- Début de la présentation -->
<div id="presentation1"></div>
<!-- Fin de la présentation -->

<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
			<div id="title">Espace membre </div>

	
<div id="container">
	<div id="col1">
	<h3>Vos combinaisons</h3>
	<?php
	$pseudo = $_SESSION['login'];
	$query = getCombinaisonByPseudoLimit15($pseudo);
	$y = 1;
	if(sizeof($query) == 0) {
		echo 'Aucune combinaison créée. <br> <a href="gerer_public">Commencer maintenant</a> !';
	}
	else {
		foreach($query as $resultat1) {
			$titre = $resultat1->titre();
			$id = $resultat1->id_liste();
			$liste = $resultat1->liste();
			echo "$y.Combinaison de $titre - "  
			?>
			<form method="post" action="combiner"> 
			<input type="hidden" name="id" value="<?php echo $id ?>" />
			<input type="hidden" name="liste1" value="<?php echo $liste ?>" />
			<input type="hidden" name="id_combi" value="<?php echo $resultat1->id() ?>" />
			<input type="submit" name="combiner" value="Réviser cette combinaison" />
			</form>
			<br> 
			<?php
			$y++;
		}
	}
	?>
	</div>
	<div id="col2outer"> 
		<div id="col2mid">
		<p>
		<h3>20 dernières listes révisées</h3><?php
		$requete = getRevisionsByPseudoLimit20($pseudo);
		$i = 1;
		if(sizeof($requete) == 0) {
			echo 'Aucune liste révisée.<br><a href="?page=gerer_public">Commencer maintenant</a> !';
		}
		else {
			foreach($requete as $resultat) {
				if($resultat->id_liste() == 'no') {
					$id_liste = 'Mots entrés par vous pour une utilisation unique';
				}
				else {
					$id = $resultat->id_liste();
					if(empty($id)){
						$id_liste = 'Mots entrés par vous pour une utilisation unique';
					}
					else {
						$query = getListeById($id);
						foreach($query as $query_r){
							$titre = $query_r->titre();
						}
						$id_liste = '<a href="afficher?id='.$id.'">'.$titre.'</a>';
					}
				}
				?><?php echo $i ?>. <?php echo $id_liste ?> - <b>Moyenne de la révision: <?php echo $resultat->moyenne() ?>%</b> - <small>Revisé le <?php echo $resultat->date()?>. </small><br /><br /> <?php
				$i++;
			}
		}
		?>
		</div>
		<div id="col2side"> 
			<h3>Favoris</h3>
				<?php
				$membre = $_SESSION['login'];
				$requete_favoris = getFavoriByPseudoLimit50($membre);
				$nombre = sizeof($requete_favoris);
				if($nombre == 0){
					echo 'Vous n\'avez aucune liste en favoris.';
				}
				else {
					$i = '1';
					foreach($requete_favoris as $rendu) {
						$liste = $rendu->id_liste();
						$requete_listes_r = getListeById($liste);
						foreach($requete_listes_r as $requete_listes){
							echo ''.$i.'. ';
							?><a href="afficher?id=<?php echo $requete_listes->id() ?>"><?php echo $requete_listes->titre() ?></a> - <small><?php echo $requete_listes->categorie() ?></small><br /><?php
							$i++;
						}
					}
				}
			?>		
		</div>
	</div>
</div> 
</div></div></div>