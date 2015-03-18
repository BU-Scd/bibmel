<?php
include_once('conf.php');
include_once('logcas.php');

$cas_user = phpCAS::getUser();

// si pas autorisé, on renvoie sur index qui gère l'autorisation
if (!(Autorise($cas_user, $users_autorises))) { header('Location: index.php'); }

$en = $_GET['en'];

$fic = $_GET['fic'];

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

$fichierMess = fopen('num_mail.txt', 'r');

/******************************************************************************************************************/

if ( $en == 'no' ) { $non_envoyes = true; } else { $non_envoyes = false; } // pour afficher les non-envoyés uniquement
if ( $en == 'yes' ) { $envoyes = true; } else { $envoyes = false; } // pour afficher les envoyés uniquement

$file = $path_upload.'/'.$fic;
if (file_exists($file)) {

  $user_fic = substr($fic, 0, strrpos($fic, '-' ) );
  $ts_fic = substr($fic, strrpos($fic, '-' )+1 );
  $lignes = file($file);
  $nb_lignes = count($lignes);
  $i = 0;
  $horizon_separator_lf = chr(31).chr(29).chr(10); // fin de message dans le fichier Horizon : LF only
  $horizon_separator_crlf = chr(31).chr(29).chr(13).chr(10); // fin de message dans le fichier Horizon : CR/LF
  $header = '';
  $corps = '';

  if ( $envoyes ) { $msg = "<h3><div id='msg'>Liste des messages envoyés : n° $ts_fic</div></h3>"; }

  if ( $non_envoyes ) {
    $msg = "<h3><div id='msg'>Liste des messages non envoyables du fichier n� $ts_fic : <br/></div></h3>";

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

    // Recupere les numeros de messages non envoyes depuis son fichier cree
    $numero_mail = fgets($fichierMess);
    $separation = explode(chr(32), $numero_mail);
    $compteur = count($separation) - 1;
  }

/******************************************************************************************************************/

  while ($i < $nb_lignes) {
    $header = explode(chr(31), $lignes[$i]);
    $start_of_header = explode(chr(1), $header[0]);
    $emprunteur = $start_of_header[2];
    $mail = $header[1];
    $i++;
    while ( ($i < $nb_lignes) && ($lignes[$i] !== $horizon_separator_lf)  && ($lignes[$i] !== $horizon_separator_crlf) ) {
      // si le fichier n'est pas au format Horizon, $i incrémente : il faut arrêter dans tous les cas sur EOF
      $corps = $corps.str_replace(" ","&nbsp;", htmlspecialchars(utf8_encode($lignes[$i])))."<br/>";
      $i++;
      }
    $affiche = false;
    if (($mail == "") && ($non_envoyes)) { $affiche = true; }  // si on demande uniquement les non-envoyés
    if (($mail !== "") && ($envoyes)) { $affiche = true; }       // si on demande uniquement envoyés
    if ((!$envoyes) && (!$non_envoyes)) { $affiche = true; }    // si on les veut tous
    if ( $affiche ) { $msg = $msg."<div class='mail'>".$corps."</div>"; }
    $corps = '';
    $i++;
  }
  if ($nb_lignes == 0) { $msg="Le fichier demandé est vide. Aucun message à envoyer."; }
}
else {
    $msg = "Le fichier demandé n'existe pas ou n'est pas accessible.";
}
$titre = "Contenu du fichier Horizon";
if  ($non_envoyes) { $titre = "Mails non envoyés";}
if  ($envoyes) { $titre = "Mails envoyés";}

  fclose($fichierMess);
?>
<!doctype html>
<html>
<head>
<title><?php echo($titre); ?> - Bibmel <?php echo($version); ?></title>
<meta charset="utf-8">
<meta name="author" content="Jérôme Bousquié - IUT de Rodez" />
<link rel="stylesheet" type='text/css' href='default.css' title='style' media='screen' />
<link rel="stylesheet" type='text/css' href='print.css' title='style' media='print' />
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
<div id='logo'><img src="images/<?php echo($logo); ?>"/></div>
<div id='titre' title='Copyleft Jérôme Bousquié - IUT de Rodez'>BIBMEL</div>
<div class="ferme"><a href="javascript:window.close();">fermer</a></div><br/>
<?php echo($msg);

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

    echo ("Liste des messages non envoyes pour cause d'erreur : <br/>");

    if(isset($compteur)){
      for ($j = 1; $j <= $compteur; $j++) {
        echo "Message numero : ".$separation[$j]."<br/>";
      }
    }

/******************************************************************************************************************/

?>
<div class="ferme"><a href="javascript:window.close();">fermer</a></div><br/>
</body>
</html>
