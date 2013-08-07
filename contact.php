<!-- Début de la présentation -->
<div id="presentation1">
</div>
<!-- Fin de la présentation -->
<!-- Début du contenu -->

<div id="content">
	<div id="bloc">
    	<div id="title">Contact </div>
		<?php
		$ok_mail = '';
		$erreur = '';
		if(isset($_POST['submit'])){
			require_once('recaptchalib.php');
			$privatekey = "6LdsCMMSAAAAAKYeqj37ims8IdO_mnYM4O_mH608";
			$resp = recaptcha_check_answer ($privatekey,
			  $_SERVER["REMOTE_ADDR"],
			  $_POST["recaptcha_challenge_field"],
			  $_POST["recaptcha_response_field"]);
		
			if (!$resp->is_valid) {
				// What happens when the CAPTCHA was entered incorrectly
				$erreur.="Le captcha n'a pas été entré correctement. Veuillez réessayer. <br /><br />";
			} else {
				$erreur="";
				// Nettoyage des entrées
				while(list($var,$val)=each($_POST)){
				if(!is_array($val)){
					$$var=strip_tags($val);
				}else{
					while(list($arvar,$arval)=each($val)){
							$$var[$arvar]=strip_tags($arval);
						}
					}
				}
				// Formatage des entrées
				$f_1=trim(ucwords(mb_ereg_replace("[^a-zA-Z0-9éèàäö\ -]", "", $f_1)));
				$f_1 = mysql_real_escape_string($f_1);
				$f_2=strip_tags(trim($f_2));
				$f_2 = mysql_real_escape_string($f_2);
				// Verification des champs
				if(strlen($f_1)<2){
					$erreur.="Le champ &laquo; Nom &raquo; est vide ou incomplet.";
					$errf_1=1;
				}
				if(strlen($f_2)<2){
					$erreur.="Le champ &laquo; E-Mail &raquo; est vide ou incomplet.";
					$errf_2=1;
				}else{
					$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
					if(!preg_match($regex, $f_2)){
						$erreur.="La syntaxe de votre adresse e-mail n'est pas correcte.";
						$errf_2=1;
					}
				}
				if(strlen($f_3)<2){
					$erreur.="Le champ &laquo; Votre demande &raquo; est vide ou incomplet.";
					$errf_3=1;
				}
				if($erreur==""){
					// Création du message
					$titre="Message de votre site";
					$tete="From: ".$f_2."\n";
					$corps="Nom : ".$f_1."\n";
					$corps.="E-Mail : ".$f_2."\n";
					$corps.="Votre demande : ".$f_3."\n";
					if(mail("yannickbloem@hotmail.com", $titre, stripslashes($corps), $tete)){
						$ok_mail="true";
					}else{
						$erreur.="Une erreur est survenue lors de l'envoi du message, veuillez refaire une tentative.";
					}
				}
			}
		}
		
		if(!isset($f_1))
		{
			$f_1="";
			$f_2="";
			$f_3="";
		}
		if(isset($ok_mail) && $ok_mail=="true"){ ?>
				<div class="alert alert-success"> Le message ci-dessous nous a bien été transmis, et nous vous en remercions.<br />
				<blockquote>
				<?php echo nl2br(stripslashes($corps));?>
				</blockquote>
				Nous allons y donner suite dans les meilleurs délais.<br>A bientôt.</div>
		<?php 
		}else{ ?>			
			<div class="container">
			<script type="text/javascript">
            	var RecaptchaOptions = {
                theme : 'custom',
                custom_theme_widget: 'recaptcha_widget'
            };
            </script>
				<form class="form-horizontal well span7" method="post">
			 	<?php 
				if(!empty($erreur)){
					echo '<div class="alert alert-error"> '.$erreur.'</div>';
				}
				?>
				  <span class="help-block">Si vous avez des bugs, erreurs ou améliorations à nous signaler, merci d'utiliser le formulaire de contact ci-dessous !</span>
					<div class="control-group">
						<div class="controls">
							<input type="hidden" name="submit" value="submit" />
							<input type="text" name="f_1" id="nom" value='<?php echo stripslashes($f_1);?>' placeholder="Nom" required>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="email" name="f_2" id="email" value='<?php echo stripslashes($f_2);?>' placeholder="Email" required>
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<textarea name="f_3" rows="10" placeholder="Votre demande" required><?php echo stripslashes($f_3);?></textarea>
						</div>
					</div>
		            <div id="recaptcha_widget" style="display:none">		
		            	<div class="control-group">
		                	<div class="controls">
		                    	<a id="recaptcha_image" href="#" class="thumbnail"></a>
		                    </div>
		                </div>		
		                <div class="control-group">	                       
		                	<div class="controls">
		                    	<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="input-recaptcha" />
		                        <a class="btn" href="javascript:Recaptcha.reload()"><i class="icon-refresh"></i></a>
		                        <a class="btn recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio')"><i title="Get an audio CAPTCHA" class="icon-headphones"></i></a>
		                        <a class="btn recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image')"><i title="Get an image CAPTCHA" class="icon-picture"></i></a>
		                        <a class="btn" href="javascript:Recaptcha.showhelp()"><i class="icon-question-sign"></i></a>
		                        <button type="submit" class="btn btn-primary btn-small"><i class="icon-envelope icon-white"></i>Envoyer</button>
		                    </div>							
						</div>
					</div>
				</form>
			</div>
			<?php 
			$recaptcha_noscript_url = 'http://api.recaptcha.net/noscript?k=6LdsCMMSAAAAAPx045E5nK50AEwInK8YSva0jLRh';
			?>
            <noscript>
	            <iframe src="<?php echo $recaptcha_noscript_url; ?>" height="300" width="500" frameborder="0"></iframe><br>
	            <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
	            <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
            </noscript>
			<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=6LdsCMMSAAAAAPx045E5nK50AEwInK8YSva0jLRh"></script>			
			<?php
		};
		?> 
	</div>
</div>
