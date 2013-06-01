<?php
$erreurMessage = "";
if(!isset($_SESSION['login'])) {
	header('Location: http://www.your-voc.com/connexion');
} 

$typeDisplay = "";
$pseudo = $_SESSION['login'];
$valueSelected1 = "null";
$valueSelected2 = "null";


if(isset($_POST['id']) && isset($_POST['type']) && isset($_POST['pseudo']) && $_POST['pseudo'] == $pseudo){
	$typeDisplay = $_POST['type'];
	$id = $_POST['id'];
	if($_POST['type'] == 'modifier' && isset($_POST['mots'])) {
			$titre = $_POST['titre'];
			$valueSelected1 = "'".$_POST['categorie']."'";
			$valueSelected2 = "'".$_POST['categorie2']."'";
	}else if($_POST['type'] == 'supprimer') {
		if(deleteListeByIdAndPseudo($id, $pseudo)) {
			$erreurMessage = 'La liste a été supprimée avec succès.';
		}
		else {
			$erreurMessage =  'Un problème est survenu. Veuillez réessayer.<br />';
		}
	}
}

if(isset($_POST['step2'])) {
	if(isset($_POST['mots'])) {
		$new_mot = mysql_real_escape_string($_POST['mots']);
		if(!empty($new_mot)){
			$categorie = mysql_real_escape_string($_POST['categorie']);
			$categorie2 = mysql_real_escape_string($_POST['categorie2']);
			$new_titre = mysql_real_escape_string($_POST['titre']);
			if(!empty($new_titre)){
				$id2 = mysql_real_escape_string($_POST['id']);
				$pseudo2 = mysql_real_escape_string($_POST['pseudo']);
				$commentaire2 = mysql_real_escape_string($_POST['commentaire']);
				if(updateListe($new_mot, $categorie, $categorie2, $new_titre, $id2, $pseudo2, $commentaire2)) {
					$erreurMessage =  '<h4>Votre liste a été modifiée avec succès.</h4><br />';
				}
				else {
					$erreurMessage =  '<h4>Un problème est survenu. Veuillez réessayer.</h4><br />';
				}
			}else{
				$erreurMessage = '<h4>Veuillez remplir le champ "titre".</h4>';
			}
		}else{
			$erreurMessage = '<h4>Veuillez remplir le champ "mots".</h4>';
		}
	}
}

$liste = getListeByPseudo($pseudo);
usort($liste, function ($a, $b){
    return strcmp($b->timestamp(), $a->timestamp());
   });
?>

<script type="text/javascript">
	$(function(){
		var valueSelected1 = <?php echo $valueSelected1;?>;
		var valueSelected2 = <?php echo $valueSelected2;?>;
		if(valueSelected1 != null && valueSelected2 != null){			
			createListeSelect("categorie", <?php echo getJsCategorieListe();?>, 1);
			createListeSelect("categorie2", <?php echo getJsCategorieListe();?>, 2);
			$("categorie").val(valueSelected1);
			$("categorie2").val(valueSelected2);
		}
		$("#erreurMessage").html(<?php echo "'".$erreurMessage."'";?>);
		$("#new_mot").keypress(function(e){
			var nbRows = ($(this).val().split("=").length) + 2;
			if(e.which == 13 && nbRows > $(this).attr("rows")){
				$(this).attr("rows",nbRows);
			}
		});
	});
 
	function validateDelete(){
		return confirm("Voulez-vous vraiment supprimer cette liste?");
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
    	    <div id="title">Gérer ses listes </div>
    	    	
    	    <div id="erreurMessage"></div>
				
			<?php if($typeDisplay == 'modifier') { 
				$nbMLigne =  substr_count($_POST['mots'], '=') + 2;
				?>
				<div id="modifier">
					<h3>Modifier votre liste :</h3><br />
					<form name="modif" method="post" onsubmit="return validerListe('mots','titre');">
						<input type="hidden" name="step2" /><br />
						<input type="hidden" name="pseudo" value="<?php echo $_POST['pseudo']; ?>" />
						<input type="hidden" name="id" value="<?php echo $_POST['id'];  ?>" />
						<table>
							<tr>
								<td style="vertical-align:top;text-align:right;width:300px;">Titre : </td>
								<td style="text-align:left;"><input type="text" id="titre" name="titre" size="60" value="<?php echo $titre;?>" /></td>
							</tr>
							<tr>
								<td style="vertical-align:top;text-align:right;width:300px;">Commentaire concernant la liste : </td>
								<td style="text-align:left;"><textarea name="commentaire" rows="2" cols="50"><?php echo $_POST['commentaire']?></textarea></td>
							</tr>
							<tr>
								<td style="vertical-align:top;text-align:right;width:300px;">Liste : </td>
								<td style="text-align:left;"><textarea id="mots" name="mots" rows="<?php echo $nbMLigne;?>" cols="50"><?php echo $_POST['mots']?></textarea></td>
							</tr>
							<tr>
								<td style="vertical-align:top;text-align:right;width:300px;">Langue 1 : </td>
								<td style="text-align:left;"><select id="categorie" name="categorie"></select></td>
							</tr>
							<tr>
								<td style="vertical-align:top;text-align:right;width:300px;">Langue 2 : </td>
								<td style="text-align:left;"><select id="categorie2" name="categorie2"></select></td>
							</tr>
						</table>
						<br>
						<input type="submit" name="valider" value="Modifier" />
					</form>
				</div>
			<?php }?>
			
			
			<div id="vosListes">
				<h2>Vos listes</h2>
				Triées par date d'ajout.<br />
				<a href="entrer-liste" >Entrer une nouvelle liste</a><br />
				<a href="revise" >Réviser quelques mots sans créer une liste</a><br /><br />
				<table style="border:0; border-spacing:10px">
			  		<tr>
						<th>Date d'entrée</th>
						<th>Titre de la liste</th>
						<th>Note</th>
						<th>Vues</th>
						<th>Réviser</th>
						<th>Modifier</th>
						<th>Supprimer</th>
			  		</tr>
				 <?php foreach($liste as $requete) {	?>
					<tr>
						<td><?php echo $requete->date(); ?></td>
						<td>
							<a href="afficher?id=<?php echo $requete->id()?>"><?php echo $requete->titre() ?></a>, 
							<small><?php echo $requete->categorie() ?> &lt;-&gt; <?php echo $requete->categorie2() ?></small>
						</td>
						<td><?php echo $requete->note();?></td>
						<td><?php echo $requete->vue();?></td>
						<td>
							<form method="post" action="afficher?id=<?php echo $requete->id()?>" >
								<input type="hidden" value="<?php echo $requete->id(); ?>" name="id" />
								<input src="images/tick.png" type=image value=submit style="vertical-align:middle">
							</form>
						</td>
						<td>
							<form method="post" action="gerer-listes" >
								<input type="hidden" value="<?php echo $requete->id(); ?>" name="id" />
								<input type="hidden" value="<?php echo $pseudo; ?>" name="pseudo" />
								<?php $listeMot = "";
								$i = 0;
								foreach ($requete->listeMot() as $listeMots){
									if($i > 0){$listeMot .="\r";}
									$listeMot .= $listeMots;
									$i++;
								}
								?>
								<input type="hidden" value="<?php echo $listeMot; ?>" name="mots" />
								<input type="hidden" value="<?php echo $requete->titre(); ?>" name="titre" />
								<input type="hidden" value="<?php echo $requete->categorie(); ?>" name="categorie" />
								<input type="hidden" value="<?php echo $requete->categorie2(); ?>" name="categorie2" />
								<input type="hidden" value="<?php echo $requete->commentaire(); ?>" name="commentaire" />
								<input type="hidden" value="modifier" name="type" />
								<input src="images/edit.png" type=image value=submit style="vertical-align:middle">
							</form>
						</td>
						<td>
							<form method="post" action="gerer-listes" onsubmit="return validateDelete();">
								<input type="hidden" value="<?php echo $requete->id(); ?>" name="id" />
								<input type="hidden" value="<?php echo $pseudo; ?>" name="pseudo" />
								<input type="hidden" value="supprimer" name="type" />
								<input src="images/cancel.png" type=image value=submit style="vertical-align:middle" >					
							</form>
						</td>
					</tr>
				<?php } ?>
				</table>
			</div>
		</div> 
	</div>
</div>