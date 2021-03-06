<?php
	$menuLink = array("accueil" => "Accueil", "gerer-public" => "Toutes les listes", "contact" => "Contact");	
	if(isset($_SESSION['login'])) { 
		$menuLink["membre"] = "Espace Membre";
		$menuLink["gerer-listes"] = "Vos listes";
		$menuLink["deconnexion"] = "Déconnexion";
	} else { 
		$menuLink["categories"] = "Catégories";
		$menuLink["connexion"] = "Connexion";
		$menuLink["inscription"] = "Inscription";
	}
?>
		
<!-- Début du header -->
<div id="header">
	<div id="logo">
		<a href="accueil"><img src="images/logo.png" alt="Your-Voc" title="Your-Voc" /></a>
	</div>
	<nav class="navbar">
		<div class="navbar-inner">
			<div class="container">
				<ul class="nav">			
					<?php foreach ($menuLink as $key=>$link) { ?>		
						<li><a href="<?php echo $key;?>"><?php echo $link;?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</nav>
</div>
<!--Fin du header !-->
<?php
// Le mec n'est pas connecté mais les cookies sont là, on y va !
$id_auto = getIdCookie();
if($id_auto != null){
	$membre = getMembreById($id_auto);
	if ($membre != null) {
		if (isCookieValid($membre)) {
			// On enregistre les informations dans la session
			$_SESSION['login'] = $membre->login();
		}
	}
}
?>