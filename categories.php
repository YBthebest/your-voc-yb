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
  createListeSelectLangue("categorie");
});
</script>
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
  		<div id="title">Catégories </div>
  		<?php
  		if(isset($_GET['cat'])) {
  			$id = addslashes($_GET['cat']);
  			$id = mysql_real_escape_string($id);
  			$fonction = getCategorieById($id);
  			if(empty($fonction)){
  				echo 'Veuillez préciser une catégorie existante.';
  				echo '</div></div>';
  				include("footer.php");
  				die();
  			} 
  		}
  		?>
		<form action="recherche" method="post">
			<p>
				Catégorie? <select id="categorie" name="categorie"></select>
				<br />Faire la recherche sur : <select name="sur" >
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select>
				<input type="text" name="requete" value="Mots-clés" size="30">
				<input type="submit" value="Recherche">
			</p>
		</form>
		<a href="entrer_liste" >Entrer une nouvelle liste</a><br />
		<?php
		if(isset($_POST['ok']) OR isset($_GET['cat'])) {
			if(isset($_GET['cat'])) {
				$categorie = addslashes($_GET['cat']);
				$categorie = mysql_real_escape_string($categorie);
				$fonction = getCategorieById($categorie);
				foreach($fonction as $resultat_fonction){
					$categorie1 = $resultat_fonction->nom();
				}
			}
			echo '<center><h2>'.htmlspecialchars($categorie1).'</h2></center>';
			$listesById = getListeByCategorie($categorie1);
			$nombre = count($listesById);
			if($nombre == 0) {
				echo 'Il n\'y a aucune liste de disponible pour cette catégorie.';
			}
			else {
				$messagesParPage=30; //Nous allons afficher 5 messages par page.
				
				//Une connexion SQL doit être ouverte avant cette ligne...
				$retour_total=getNbListeByCategorie($categorie1); //Nous récupérons le contenu de la requête dans $retour_total
				//On range retour sous la forme d'un tableau.
				$total= $retour_total; //On récupère le total pour le placer dans la variable $total.
				//Nous allons maintenant compter le nombre de pages.
				$nombreDePages=ceil($total/$messagesParPage);
				
				if(isset($_GET['nb_page'])) // Si la variable $_GET['page'] existe...
				{
					if(is_numeric($_GET['nb_page'])) {
						 $pageActuelle=intval(addslashes($_GET['nb_page']));
						 
						 if($pageActuelle>$nombreDePages) // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
						 {
							  $pageActuelle=$nombreDePages;
						 }
					}
					else {
						$pageActuelle=1;
					}
				}
				else // Sinon
				{
				     $pageActuelle=1; // La page actuelle est la n°1    
				}
				
				$premiereEntree=($pageActuelle-1)*$messagesParPage; // On calcul la première entrée à lire
				
				// La requête sql pour récupérer les messages de la page actuelle.
				if(isset($_POST['critere'])){
					$class = $_POST['critere'];
				}
				elseif(isset($_GET['class'])){
					$class = htmlspecialchars(mysql_real_escape_string($_GET['class']));
				}else {
					$class = 'vues';
				}	
				echo '<p align="center">Page : '; //Pour l'affichage, on centre la liste des pages
				for($i=1; $i<=$nombreDePages; $i++) //On fait notre boucle
				{
					//On va faire notre condition
					if($i==$pageActuelle) //Si il s'agit de la page actuelle...
					{
						echo ' [ '.$i.' ] ';
					}
					else
					{
						$cat = $_GET['cat'];
						echo ' <a href="categories?nb_page='.$i.'&class='.$class.'&cat='.$cat.'">'.$i.'</a> ';
					}
				}
				echo '</p>';
				$i = '1';
				$requete_total = rechercheByCriteres($categorie1, 'titre', '', $class, $premiereEntree, $messagesParPage);
				?>
				<form method="post" action="categories?cat=<?php echo $_GET['cat'] ?>&nb_page=<?php echo $i ?>&class=<?php echo $class ?>" >
					<select name="critere" onchange='this.form.submit()'>
						<option>Trier par?</option>
						<option value="note">Trier par note</option>
						<option value="vues">Trier par popularité</option>
						<option value="pseudo">Trier par auteur</option>
						<option value="date">Trier par date de mise en ligne</option>
					</select>
				</form>
				<b>Triées par <?php echo $class ?>.</b><br /><br /><?php
				$i = ($premiereEntree + 1);
				foreach($requete_total as $donnees) {
					echo "".$i.".";
					?>
					<a href="afficher?id=<?php echo $donnees->id(); ?>"><?php echo $donnees->titre(); ?></a> <small>entré le <?php echo $donnees->date() ?><br/>
					par <a href="profil?m=<?php echo $donnees->membre()?>"><?php echo $donnees->membre() ?></a> dans les catégories <?php echo $donnees->categorie() ?> <-> <?php echo $donnees->categorie2() ?>  (<?php echo $donnees->note() ?>/5) (<?php echo $donnees->vue() ?> vues)</small><br /><br />
					<?php
					$i++;
				}
				echo '<p align="center">Page : '; //Pour l'affichage, on centre la liste des pages
				for($i=1; $i<=$nombreDePages; $i++) //On fait notre boucle
				{
					//On va faire notre condition
					if($i==$pageActuelle) //Si il s'agit de la page actuelle...
					{
						echo ' [ '.$i.' ] '; 
					}	
					else
					{
						$cat = $_GET['cat'];
				   	 	echo ' <a href="categories?nb_page='.$i.'&class='.$class.'&cat='.$cat.'">'.$i.'</a> ';
					}
				}
				echo '</p>';
			}
		}		
		$mysql = getCategorieByGeneral('1');
		$mysql2 = getCategorieByGeneral('4');
		$mysql3 = getCategorieByGeneral('2');
		$mysql4 = getCategorieByGeneral('3');
		?>
		<div id="left">
			<h2>Europe</h2>	
			<ul type="circle">
				<?php
				foreach($mysql as $mysql_r){
					?><li><a href="<?php echo $mysql_r->url() ?>"><?php echo $mysql_r->nom()?></a> - 								
					<?php $cat = $mysql_r->nom();
					$retour = getNbListeByCategorie($cat);?>
					(<i><?php echo $retour ?> listes </i>)<br /></li><?php
				}
				?>
			</ul>
			<h2>Europe de l'Est</h2>	
			<ul type="circle">
				<?php
				foreach($mysql2 as $mysql2_r){
					?><li><a href="<?php echo $mysql2_r->url() ?>"><?php echo $mysql2_r->nom()?></a> - 								
					<?php $cat = $mysql2_r->nom();
					$retour = getNbListeByCategorie($cat);?>
					(<i><?php echo $retour ?> listes </i>)<br /></li><?php
				}
				?>
			</ul>		
		</div>
		<div id="right"> 
			<h2>Asie</h2>	
			<ul type="circle">
				<?php
				foreach($mysql3 as $mysql3_r){
					?><li><a href="<?php echo $mysql3_r->url() ?>"><?php echo $mysql3_r->nom()?></a> - 								
					<?php $cat = $mysql3_r->nom();
					$retour = getNbListeByCategorie($cat);?>
					(<i><?php echo $retour ?> listes </i>)<br /></li><?php
				}
				?>
			</ul>
			<h2>Moyen Orient</h2>	
			<ul type="circle">
				<?php
				foreach($mysql4 as $mysql4_r){
					?><li><a href="<?php echo $mysql4_r->url() ?>"><?php echo $mysql4_r->nom()?></a> - 								
					<?php $cat = $mysql4_r->nom();
					$retour = getNbListeByCategorie($cat);?>
					(<i><?php echo $retour ?> listes </i>)<br /></li><?php
				}
				?>
			</ul>
		</div>
	</div>
</div>