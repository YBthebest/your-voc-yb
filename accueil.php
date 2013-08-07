<script type="text/javascript">
$(function(){
	createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe()?>);
});

</script>
        <!-- Début de la présentation -->
        <div id="presentation">
            <div id="center">
                <a href="ccm"><img src="images/image.png" alt="Presentation" title="Your-Voc" class="desk"/></a>
            </div>
        </div>
        <!-- Fin de la présentation -->

        <!-- Début du contenu -->
        <div id="content">
            <div id="bloc">
            	<div id="title"><a href="ccm">Your-Voc, c'est quoi ?</a></div>
				<p class="lead">
					<h3>Avouez-le, vous l'avez tous connu. Un test arrive très rapidement, mais vous ne savez pas comment réviser et vous manquez de motivation.</h3>
					Et bien, Your-Voc est fait pour vous. Créé par quelqu'un comme vous, pour vous, il vous aidera très facilement à apprendre votre vocabulaire sans y perdre des heures. Vous pouvez passer du temps sur l'ordinateur, Facebook et compagnie, et réviser en même temps. <br>
					C'est une méthode déjà utilisée, testée et approuvée dans pleins d'autres pays, et elle débarque désormais en français pour vous, gratuitement.<br />
					Commencez donc par chercher une liste ou bien par créer votre propre liste.<br /><br />
				</p>
			</div>

            <div id="bloc">
				<div class="container">
					<div class="row">
						<div class="span4">
							<h3><a href="categories">Catégories</a></h3>
							<ul type="circle">
								<?php
									$allCat = getCategoriesWithNbListe(7);
									foreach($allCat as $key=>$cat) {
								?>
										<li><a href="categories?cat=<?php echo $cat->id() ?>"><?php echo  $cat->nom() ?></a> - <i><?php echo $cat->nbListe() ?> listes </i></li><br>
								<?php 
									} 
								?>
							</ul>
							<a href="categories">Plus de catégories</a><br /><br />
						</div> 
						<div class="span4"> 	
							<a href="entrer-liste"><img src="images/entrerliste.png" alt="enter liste" /></a>
							<div id="text-center">
								<b><h2>ou chercher une liste :</h2></b>
								<form action="recherche" method="Post">
									<p>
										<b>Catégorie :</b>
										<select id="categorie" name="categorie"></select>
										<br><br>
										<b>Mots-clés :</b>
										<input type="text" name="requete" size="30" title="Mots-clés" ><br /><br />
										<b>Faire la recherche sur:</b><select name="sur" >
											<option value="titre">le titre des listes</option>
											<option value="mots">le contenu des listes</option>
											<option value="tous">les deux</option>
										</select><br />
										<input type="hidden" name="critere" value="vues">
										<br><br>
										<input type="submit" value="Recherche">
									</p>
								</form>
							</div> 
						</div> 
						<div class="span4">
							<?php $defaultNbListe = 3; ?>
							<h3><a href="gerer-public">Derniers ajouts</a></h3>					
							<ul type="circle">
							<?php
								$listeMotArray = getListesMotDefinitionByDate($defaultNbListe);								
								foreach($listeMotArray as $listeMot) {
							?>
									<li><b><?php echo $listeMot->categorie(); ?> -> <?php echo $listeMot->categorie2(); ?>: </b><br /><a href="afficher?id=<?php echo $listeMot->id(); ?>"><?php echo $listeMot->titre(); ?></a> <small>par <a href="profil?m=<?php echo $listeMot->membre(); ?>"><?php echo $listeMot->membre();?></a></small></li>
							<?php } ?>
							</ul>
							<h3>Thèmes</h3>
							<ul type="circle">
								<li><a href="recherche?nb_page=1&requete=sport&categorie=aucun&sur=titre&critere=note">Le sport</a></li>
								<li><a href="recherche?nb_page=1&requete=tourisme&categorie=aucun&sur=titre&critere=vues">Le tourisme - Les voyages</a></li>
								<li><a href="recherche?nb_page=1&requete=restaurant&categorie=aucun&sur=titre&critere=note">Le restaurant</a></li>
								<li><a href="recherche?nb_page=1&requete=musique&categorie=aucun&sur=titre&critere=note">La musique</a></li>
							</ul>
						</div> 
					</div>
				</div>
            </div>
        </div>
        <!-- Fin du contenu -->

        <div id="clear"></div>