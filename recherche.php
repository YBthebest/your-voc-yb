<?php  
	$critere = "titre";
	if(isset($_POST['critere'])){
		$critere = $_POST['critere'];
	}else if(isset($_GET['critere'])){
		$critere = mysql_real_escape_string($_GET['critere']);
	}
?>

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
  if($("#categorie").length == 1){
	 createListeSelectWithDefault("categorie", <?php echo getJsCategorieListe();?>);
  }

  if($("#critere").length > 0){	  
	 $("#critere option[value='<?php  echo $critere;?>']").attr("selected", "selected");
  }
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
            <div id="title">Recherche </div>
			<?php
			if((isset($_POST['requete']) OR isset($_GET['requete']))) {
				$categorie = (isset($_POST['categorie']))?$_POST['categorie']:htmlspecialchars(mysql_real_escape_string($_GET['categorie']));
				$critere = (isset($_POST['sur']))?$_POST['sur']:htmlspecialchars(mysql_real_escape_string($_GET['sur']));
				$search = (isset($_POST['requete']))?$_POST['requete']:htmlspecialchars(mysql_real_escape_string($_GET['requete']));
				$tri = (isset($_POST['critere']))?$_POST['critere']:htmlspecialchars(mysql_real_escape_string($_GET['critere']));
				$messagesParPage=30; //Nous allons afficher 5 messages par page.
				
				//Une connexion SQL doit être ouverte avant cette ligne...
				$nb_resultats_requete = rechercheByCriteres($categorie, $critere, $search, $tri, "0", "illimite");
				$retour_total = sizeof($nb_resultats_requete);//Nous récupérons le contenu de la requête dans $retour_total
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
				$resultats = rechercheByCriteres($categorie, $critere, $search, $tri, $premiereEntree, $messagesParPage);
				// on utilise la fonction mysql_num_rows pour compter les résultats pour vérifier par après
				if(!empty($resultats)) // si le nombre de résultats est supérieur à 0, on continue
				{
					// maintenant, on va afficher les résultats et la page qui les donne ainsi que leur nombre, avec un peu de code HTML pour faciliter la tâche.
					?>
					<h3>Résultats de votre recherche.</h3>
					<p>Nous avons trouvé <?php echo $retour_total ?> résultat<?php echo ($retour_total > 1)?"s":""; ?>
					dans notre base de données. Voici les listes que nous avons trouvées, classées par <?php echo $tri ?> :<br/>
					<a href="recherche">Faire une nouvelle recherche</a><br />
					<?php
					if(isset($_GET['requete'])){
						$requete = $_GET['requete'];
					} else{
						$requete = $_POST['requete'];
					}
					if(isset($_GET['sur'])){
						$sur = $_GET['sur'];
					} else{
						$sur = $_POST['sur'];
					}
					if(isset($_GET['categorie'])){
						$categorie = $_GET['categorie'];
					} else{
						$categorie = $_POST['categorie'];
					}
					echo '<p align="center">Page : '; //Pour l'affichage, on centre la liste des pages
					for($i=1; $i<=$nombreDePages; $i++) //On fait notre boucle
					{
						//On va faire notre condition
						if($i==$pageActuelle) //Si il s'agit de la page actuelle...
						{
							echo ' [ '.$i.' ] ';
						}
						else //Sinon...
						{
							echo ' <a href="recherche?nb_page='.$i.'&requete='.$search.'&categorie='.$categorie.'&sur='.$critere.'&critere='.$tri.'">'.$i.'</a> ';
						}
					}
					echo '</p>';
					$i = 1;
					?>
					<form method="post" action="recherche?nb_page=<?php echo $i ?>&requete=<?php echo $search ?>&categorie=<?php echo $categorie ?>&sur=<?php echo $critere ?>&critere=<?php echo $tri ?>" >
					<input type="hidden" name="requete" value="<?php echo $requete ?>" />
					<input type="hidden" name="sur" value="<?php echo $sur ?>" />
					<input type="hidden" name="categorie" value="<?php echo $categorie ?>" />
					Trier par :
					<select name="critere" id="critere" onchange='this.form.submit()'>
						<option value="titre">Titre</option>
						<option value="note">Note</option>
						<option value="vue">Popularité</option>
						<option value="pseudo">Auteur</option>
						<option value="date">Date de mise en ligne</option>
					</select>
					</form><br />
					<p>
					</div>
					<?php
					$i = ($premiereEntree + 1);
					foreach($resultats as $donnees) {
						echo "".$i.".";
						?>
						<a href="afficher?id=<?php echo $donnees->id(); ?>"><?php echo $donnees->titre(); ?></a> <small>entré le <?php echo $donnees->date() ?><br/>
						par <a href="profil?m=<?php echo $donnees->membre()?>"><?php echo $donnees->membre() ?></a> dans les catégories <?php echo $donnees->categorie() ?> <-> <?php echo $donnees->categorie2() ?>  (<?php echo $donnees->note() ?>/5) (<?php echo $donnees->vue() ?> vues)</small><br /><br />
						<?php
						$i++;
					}
					?><div id="text-center"><?php
					echo '<p align="center">Page : '; //Pour l'affichage, on centre la liste des pages
					for($i=1; $i<=$nombreDePages; $i++) //On fait notre boucle
					{
						//On va faire notre condition
						if($i==$pageActuelle) //Si il s'agit de la page actuelle...
						{
							echo ' [ '.$i.' ] ';
						}
						else //Sinon...
						{
							echo ' <a href="recherche?nb_page='.$i.'&requete='.$search.'&categorie='.$categorie.'&sur='.$critere.'&critere='.$tri.'">'.$i.'</a> ';
						}
					}
					echo '</p>';
					?><br/>
					<br/>
					<a href="recherche">Faire une nouvelle recherche</a></p>
					<?php
				} // Fini d'afficher les résultats ! Maintenant, nous allons afficher l'éventuelle erreur en cas d'échec de recherche et le formulaire.
				else
				{ // de nouveau, un peu de HTML
					?>
					<h3>Pas de résultats</h3>
					<p>Nous n'avons trouvé aucun résultat pour votre requête "<?php  echo stripslashes($search) ?>". <a href="recherche">Réessayez</a> avec autre chose.</p>
					<?php
				}// Fini d'afficher l'erreur ^^
			}
			else { // et voilà le formulaire, en HTML de nouveau !
				?>
				<p>Vous allez faire une recherche dans notre base de données concernant les listes publiques.</p>
				 <form action="recherche" method="Post">
				 	<p>Sur quelle catégorie souhaitez vous effectuer la recherche?				 
						<select id="categorie" name="categorie"></select>
						Faire la recherche sur : <select name="sur" >
							<option value="titre">le titre des listes</option>
							<option value="mots">le contenu des listes</option>
							<option value="tous">les deux</option>
						</select><br />
						<input type="text" name="requete" value="Mots-clés" size="30"><br />
						<input type="hidden" name="critere" value="vues">
						<input type="submit" value="Recherche">
					</p>
				</form>
				<?php
			}		
			// et voilà, c'est fini !
			?>		
		</div> 
	</div> 
</div>