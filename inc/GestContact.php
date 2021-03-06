<?php 
if (isset($_SESSION['authentification']))
{
	echo Affichage_Entete($_SESSION['opensim_select']);
	$moteursOK = Securite_Simulateur();
    /* ************************************ */
	//SECURITE MOTEUR
	$btnN1 = "disabled";$btnN2 = "disabled";$btnN3 = "disabled";
	if ($_SESSION['privilege'] == 4) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 4
	if ($_SESSION['privilege'] == 3) {$btnN1 = ""; $btnN2 = ""; $btnN3 = "";} // Niv 3
	if ($_SESSION['privilege'] == 2) {$btnN1 = ""; $btnN2 = "";}              // Niv 2
	if ($moteursOK == "OK" )
	{
		if($_SESSION['privilege'] == 1)
		{$btnN1 = "";$btnN2 = "";$btnN3 = "";}
	}
     //SECURITE MOTEUR
    /* ************************************ */
    
    echo '<h1>'.$osmw_index_9.'</h1>';
    echo '<div class="clearfix"></div>';
    
	
	/* CONFIGURATION */
	$form_action = 'index.php?a=9';
	$message_envoye = "<i class='glyphicon glyphicon-ok'></i> Message envoye avec succes ...";
	$message_non_envoye = "<i class='glyphicon glyphicon-remove'></i> Echec d'envoi du message, veuillez reessayer ...";
	$message_formulaire_invalide = "<i class='glyphicon glyphicon-remove'></i> Erreur dans le formulaire, veuillez reessayer ...";
	$err_formulaire = false;

	$nom     = (isset($_POST['nom']))     ? Rec($_POST['nom'])     : '';
	$email   = (isset($_POST['email']))   ? Rec($_POST['email'])   : '';
	$objet   = (isset($_POST['objet']))   ? Rec($_POST['objet'])   : '';
	$message = (isset($_POST['message'])) ? Rec($_POST['message']) : '';

	if (isset($_POST['envoi']))
	{
		$email = (IsEmail($email)) ? $email : ''; 
		$err_formulaire = (IsEmail($email)) ? false : true;

		if (($nom != '') && ($email != '') && ($objet != '') && ($message != ''))
		{
			$headers = 'From: '.$nom.' <'.$email.'>' . "\r\n";

			// Envoyer une copie au visiteur ?
            if ($_POST['sendcopy'] == true)
            {
                $cible = INI_Conf(0, "destinataire").', '.$email;
            }
			else {$cible = INI_Conf(0, "destinataire");}

			// Remplacement de caracteres speciaux
			$message = html_entity_decode($message);
			$message = str_replace('&#039;', "'", $message);
			$message = str_replace('&#8217;', "'", $message);
			$message = str_replace('<br>', '', $message);
			$message = str_replace('<br />', '', $message);

			// Envoi du mail
			$message = $message.' > Serveur Concerne: '.$hostnameSSH.' > Simulateur Selectionne: '.$_SESSION['opensim_select'].' '.INI_Conf_Moteur($_SESSION['opensim_select'], "version");
			
            if (mail($cible, $objet, $message, $headers))
            {
                echo '<div class="alert alert-success alert-anim">'.$message_envoye.'</div>';
            }
            
            else
            {
                echo '<div class="alert alert-danger alert-anim">'.$message_non_envoye.'</div>';
            }
		}

		else
		{
			echo '<div class="alert alert-danger alert-anim">'.$message_formulaire_invalide.'</div>';
            echo '<a class="btn btn-primary" href="index.php?a=9"><i class="glyphicon glyphicon-envelope"></i> Retour au formulaire</a>';
			$err_formulaire = true;
		}
	}

	if ((!$err_formulaire) || (!isset($_POST['envoi'])))
	{	
		// afficher le formulaire
		echo "\n".'<form class="form-group" id="contact" method="post" action="'.$form_action.'">'."\n";
		echo '  <h4>'.$osmw_label_conatct_coord.'</h4>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="nom">Nom:</label>'."\n";
		echo '          <input class="form-control" type="text" id="nom" name="nom" value="'.stripslashes($nom).'" tabindex="1" />'."\n";
		echo '      </div>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="email">Email:</label>'."\n";
		echo '          <input class="form-control" type="text" id="email" name="email" value="'.stripslashes($email).'" tabindex="2" />'."\n";
		echo '      </div>'."\n";
		echo '  <h4>'.$osmw_label_conatct_msg.'</h4>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="objet">Sujet:</label>'."\n";
		echo '          <input class="form-control" type="text" id="objet" name="objet" value="'.stripslashes($objet).'" tabindex="3" />'."\n";
		echo '      </div>'."\n";
		echo '      <div class="form-group">'."\n";
		echo '          <label for="message">Message:</label>'."\n";
		echo '          <textarea class="form-control" id="message" name="message" tabindex="4" rows="5" >'.stripslashes($message).'</textarea>'."\n";
		echo '      </div>'."\n";
        echo '      <div class="checkbox">'."\n";
        echo '          <label><input type="checkbox" name="sendcopy" value="true" id="Remember"> Send me a copy of this mail</label>'."\n";
        echo '      </div>'."\n";

		echo '  <button class="btn btn-success" type="submit" name="envoi" value="Envoyer"><i class="glyphicon glyphicon-envelope"></i> '.$osmw_btn_msg_send.'</button>'."\n";
		echo '</form>'."\n";
	}
}
else {header('Location: index.php');}	
?>
