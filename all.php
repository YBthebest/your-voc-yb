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
  createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
});
</script>
<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->
<div id="content">
	<div id="bloc">
		<div id="text-center">
            <div id="title">Toutes les listes </div>
			<a href="entrer_liste" >Entrer une nouvelle liste</a><br />
			<a href="recherche" >Faire une recherche</a><br />
			<form action="recherche" method="Post">
				<p>
				Catégorie?<select id="categorie" name="categorie"></select>
				<br />Faire la recherche sur : 
				<select name="sur" >
					<option value="titre">le titre des listes</option>
					<option value="mots">le contenu des listes</option>
					<option value="tous">les deux</option>
				</select>
				<input type="text" name="requete" value="Mots-clés" size="30">
				<input type="submit" value="Recherche">
				</p>
			</form>
		</div>
		<?php
	$messagesParPage=30; //Nous allons afficher 5 messages par page.
				
				//Une connexion SQL doit être ouverte avant cette ligne...
				$retour_total=getNbListe(); //Nous récupérons le contenu de la requête dans $retour_total
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
					if(isValidClass($_POST['critere'])){
						$class = htmlspecialchars(mysql_real_escape_string($_POST['critere']));
					}else {
						$class = 'vues';
					}	
				}
				elseif(isset($_GET['class'])){
					if(isValidClass($_GET['class'])){
						$class = htmlspecialchars(mysql_real_escape_string($_GET['class']));
					}else {
						$class = 'vues';
					}			
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
						echo ' <a href="all?nb_page='.$i.'&class='.$class.'">'.$i.'</a> ';
					}
				}
				echo '</p>';
				$i = '1';
				$requete_total = rechercheByCriteres('', 'titre', '', $class, $premiereEntree, $messagesParPage);
				?>
				<form method="post" action="all?nb_page=<?php echo $i ?>&class=<?php echo $class ?>" >
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
				   	 	echo ' <a href="all?nb_page='.$i.'&class='.$class.'">'.$i.'</a> ';
					}
				}
				echo '</p>';
		?>
	</div>
</div>