<?php
// Pour fonctionner, PHP doit être installé en mode CLI : php -v pour vérifier.

// si le script mailer.php est appelé via le serveur web, on renvoie sur le menu initial et on quitte
// sinon, on est en mode ligne de commandes CLI
if (!(defined('STDIN'))) { header("Location: index.php"); exit; }

// inclusion de la librairie Swift Mailer
include_once (dirname(__FILE__) . '/Swift/lib/swift_required.php');
include_once('conf.php');
include_once('verification_mail.php');

date_default_timezone_set($time_zone);
$file = $argv[1];
$nom_fic = basename($file);
$user = substr($nom_fic, 0, strrpos($nom_fic, '-' ) );
$ts_fic = substr($nom_fic, strrpos($nom_fic, '-' )+1 );

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

$resboucle = '';
$fichierMess = fopen('num_mail.txt', 'a+');

/******************************************************************************************************************/

if (file_exists($file)) {

  $lignes = file($file);
  $nb_lignes = count($lignes);
  $i = 0;

  $horizon_separator_lf = chr(31).chr(29).chr(10); // fin de message dans le fichier Horizon : LF only
  $horizon_separator_crlf = chr(31).chr(29).chr(13).chr(10); // fin de message dans le fichier Horizon : CR/LF

  $header = '';
  $corps = '';
  $msg_non_envoyes = false;
  $nb_msg_envoyes = 0;

  $transport = Swift_SmtpTransport::newInstance($smtp_server, $smtp_port, $encrypt);
  if ($smtp_login !== "" && $smtp_password !== "") {
    $transport->setUsername($smtp_login);
    $transport->setPassword($smtp_password);
    }

$mailer = Swift_Mailer::newInstance($transport);

  while ($i < $nb_lignes) {
    $header = explode(chr(31), $lignes[$i]);
    $start_of_header = explode(chr(1), $header[0]);
    $emprunteur = $start_of_header[2];
    $subject = $header[2];

    // Attention les variables de mails peuvent parfois être vides !
    $mail = $header[1];
    $from_name = $header[3];
    $from_mail = $header[4];
    $reply_to = $header[5];
    $bcc = $header[6];
    $i++;
    while ( ($i < $nb_lignes) && ($lignes[$i] !== $horizon_separator_lf) && ($lignes[$i] !== $horizon_separator_crlf ) ) {
      // si le fichier n'est pas au format Horizon, $i incrémente : il faut arrêter dans tous les cas sur EOF
      $corps = $corps.htmlspecialchars(utf8_encode($lignes[$i]));
      $i++;
      }
    // si le mail du destinataire n'est pas renseigné : tag
    if ($mail == "") {  $msg_non_envoyes = true; }


/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

    else if (verifMail($mail) == false) {
      // Ajoute les numeros de messages non envoyes dans le fichier
      $headernettoye = explode(chr(1), $header[0]);
      fputs($fichierMess, $headernettoye[0]);
      $msg_non_envoyes = true;
    }

/******************************************************************************************************************/

    else {
      // le mail du destinataire est connu :  envoi du message
      if ($from_mail == "") { $from_mail = $from_mail_defaut; }
      if ($from_name == "") { $from_name = $from_name_defaut; }
      if ($subject == "") { $subject = $subject_defaut; }
      if ($reply_to == "") { $reply_to = $reply_to_defaut; }
      $message = Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom(array($from_mail => $from_name))
        ->setTo($mail)
        ->setbody($corps)
        ;
      if ($bcc !== "") {$message->setBcc($bcc); }
      $envoi = $mailer->send($message);
      $nb_msg_envoyes++;
      echo("[$user] envoi à $mail\n");
    }
    $corps = '';
    $i++;
  }
  if ($nb_lignes == 0) { echo("Le fichier reçu $file est vide. Aucun message envoyé.\n"); }
  else {
    $report = $report."<p id='titre'>Bibmel : compte-rendu n° $ts_fic</p>";
    $report = $report."<p class='corps'>$nb_msg_envoyes messages de rappel, de relance ou réservation viennent d'être envoyés avec le compte <i>$user</i>.<br/>";
    $report = $report."Ces messages sont <a href='".$url_bib."affiche.php?fic=$nom_fic&en=yes'>consultables en ligne</a>.</p>";

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

    if ( $msg_non_envoyes ) {
      fclose($fichierMess);
      $fichierMess = fopen('num_mail.txt', 'r');
      $numero_mail = fgets($fichierMess);
      $separation = explode(chr(32), $numero_mail);
      $compteur = count($separation) - 1;

/******************************************************************************************************************/

      $report = $report."<p class='corps'>Certains messages du fichier initial n'ont pu �tre envoy�s faute d'adresse e-mail renseign�e.<br/>Vous pouvez consulter <a href='".$url_bib."affiche.php?fic=$nom_fic&en=no'>ces messages non envoy�s en ligne</a>.</p>";

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/
      $report = $report.'<p><br/>Liste des messages non envoyables du fichier precite : <br/></p>';
      for ($j = 1; $j <= $compteur; $j++) {
        $resboucle = $resboucle."<p>Message numero : ".$separation[$j]."<br/></p>";
      }
      fclose($fichierMess);
    }

/******************************************************************************************************************/

    $report = $report . $resboucle . "<br/><br/><p id='pied'><i>Vous avez reçu ce message automatique parce que vous faites partie de la liste des gestionnaires destinataires. Merci de ne pas y répondre.</i></p>";
    $report_msg = Swift_Message::newInstance()
        ->setSubject($mail_subject_report." : ".$ts_fic)
        ->setFrom($from_mail_defaut)
        ->setTo($mail_report)
        ->setContentType('text/html')
        ->setbody($report);
    $envoi = $mailer->send($report_msg);
    echo("Total : $nb_msg_envoyes mails émis.\n");
    echo("Compte-rendu envoyé à $mail_report.");
  }
}
else {
  echo("Le fichier $file n'existe pas ou est inaccessible.");
}

// Dans tous les cas, on purge les anciens fichiers Horizon et leur log
$now = mktime();
if ($dir = opendir($path_upload)) {
  while (false !== ($fic_dir = readdir($dir))) {
    if (($fic_dir !== 'index.html') && ($fic_dir !== 'index.php') && ($fic_dir !== '.') && ($fic_dir !== '..')) {
      $ts_fic_dir = (int)rtrim(substr($fic_dir, strrpos($fic_dir, '-' )+1 ), '.log');
      $delai = $seuil * 86400;
      if ( $ts_fic_dir + $delai < $now ) { // si le ts du fichier est plus ancien que $seuil jours, on le supprime
        if (file_exists($path_upload.'/'.$fic_dir)) {
          unlink($path_upload.'/'.$fic_dir);
          echo("[$user] ancien fichier $fic_dir suppprimé.\n");
        }
      }
    }
  }
  closedir($dir);
}

?>
