<script type="text/javascript">
$(function(){
  var save='';
  $('input[type="text"]').each(function(){
    this.onfocus=function(){
      save=this.value;
      this.value='';
    };
    this.onblur=function(){
      this.value= this.value==='' ? save : this.value;
    };
  });
  createListeSelect("categorie", <?php echo getJsCategorieListe();?>);
});
</script>
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
    	<div id="title">Les listes publiques </div>
			<form action="recherche" method="Post">
				 <p>
				 	Catégorie: <select id="categorie" name="categorie"></select><br />
					Faire la recherche sur : <select name="sur" >
						<option value="titre">le titre des listes</option>
						<option value="mots">le contenu des listes</option>
						<option value="tous">les deux</option>
					</select>
					<input type="text" name="requete" value="Mots-clés" size="30">
					<input type="submit" value="Recherche">
				</p>
			</form>
		<div id="container">
			<div id="col1">
				<div id="title" style="text-align: center">
					<a href="all">Voir toutes les listes</a><br /><br />
				</div>
				<h3>Dernières listes ajoutées</h3>
				<ul type="circle">
					<?php
					$liste = getAllListe();
					$liste = array_reverse($liste);
					$liste = array_slice($liste, 0, 6);
					foreach($liste as $requete) {
						?><li><b><?php echo $requete->categorie() ?> <-> <?php echo $requete->categorie2() ?>: </b><br /><a href="afficher?id=<?php echo $requete->id(); ?>"><?php echo $requete->titre() ?></a><br /><small>par <a href="profil?m=<?php echo $requete->membre()?>"><?php echo $requete->membre()?></a> le <?php echo $requete->date() ?></small></li>
				<?php }
					?>
				</ul>
			</div>
			<div id="col2outer"> 
				<div id="col2mid"> 
					<div id="title" style="text-align: center">
						<a href="entrer_liste" >Entrer une nouvelle liste</a><br /><br />
					</div>
					<h3>Listes les plus populaires</h3>
					<ul type="circle">
						<?php
						$populaire = getListeOrderByVues();
						$populaire = array_reverse($populaire);
						$populaire = array_slice($populaire, 0, 8);
						foreach($populaire as $result) {
							?><li><b><?php echo $result->categorie() ?> <-> <?php echo $result->categorie2() ?>: </b><br /><a href="afficher?id=<?php echo $result->id(); ?>"><?php echo $result->titre() ?></a> - <?php echo $result->vue() ?> vues<br /><small>par <a href="profil?m=<?php echo $result->membre()?>"><?php echo $result->membre()?></a> le <?php echo $result->date() ?></small></li>
					<?php }
					?>
					</ul>
				</div>
				<div id="col2side">
					<div id="title" style="text-align: center">
						<a href="revise" >Réviser quelques mots sans liste</a>					
						<br /><br />
					</div>
					<h3>Catégories</h3>
					<ul type="circle">
						<?php
						$categories = getCategories('12');
						foreach($categories as $requete2) {
							$cat = $requete2->nom();
							?><li><a href="<?php echo $requete2->url() ?>"><?php echo $requete2->nom() ?></a><br />
							<?php $retour = getNbListeByCategorie($cat);?>
							<i><?php echo $retour ?> listes </i></li>
						<?php }
						?>
					</ul>
				</div>
			</div> 
		</div>
	</div>
</div>	