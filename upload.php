<?php
include_once('conf.php');
include_once('logcas.php');
//include_once('cosanscass.php');

// fonction Erreur($_FILES['fichier'], $maxsize, $extensions)
// retourne une string vide si pas d'erreur, le message d'erreur sinon
function Erreur($file, $maxsize, $extensions) {
  $erreur = '';
  $err_code = $file['error'];
  switch ($err_code) {
    case UPLOAD_ERR_NO_FILE:
      $erreur = "Fichier manquant";
      break;
    case UPLOAD_ERR_INI_SIZE:
      $erreur = "Taille du fichier supérieure à celle autorisée dans php.ini";
      break;
    case UPLOAD_ERR_FORM_SIZE:
      $erreur = "Taille du fichier supérieure à celle autorisée dans le formulaire web";
      break;
    case UPLOAD_ERR_PARTIAL:
      $erreur = "Fichier transféré partiellement";
      break;
  }
  if ( $file['size'] > $maxsize ) { $erreur = "Taille du fichier supérieure à celle autorisée par l'application"; }
  $extension_upload = strtolower(  substr(  strrchr($file['name'], '.')  ,1)  );
  if ( !(in_array($extension_upload,$extensions)) ) { $erreur = "Extension du fichier non autorisée"; }
  if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){ $erreur = "Méthode HTTP non autorisée"; }
  return $erreur;
}

// fonction Renomme($fic, $rep, $nom) : renomme et déplace le fichier temporaire uploadé vers $rep
// si $fic est null, prend le contenu de l'entrée standard php://input comme données du fichier
function Renomme($fic, $rep, $nom) {
  $dest = $rep.'/'.$nom;
  if (!(is_null($fic))) {
    return move_uploaded_file($fic, $dest);
  }
  else {
   return file_put_contents($dest, file_get_contents('php://input'));  
  }
}

// fonction Nomme($base) : retourne un nom de fichier composé de la $base et d'un timestamp
function Nomme($base) {
  $nom = $base.'-'.(string)(time());
  return $nom;
}

// Renvoie, si possible, le nom du fichier original
function OriginalFileName($file) {
  if  (is_null($file)) { return $_SERVER['HTTP_X_FILENAME']; }  
  else return $file['name'];
}

$cas_user = phpCAS::getUser();
$nom_fic = Nomme($cas_user);

// si pas autorisé, on renvoie sur index qui gère l'autorisation
if (!(Autorise($cas_user, $users_autorises))) { header('Location: index.php'); exit(); }

$fn = (isset($_SERVER['HTTP_X_FILENAME']) ? $_SERVER['HTTP_X_FILENAME'] : false);  
if ($fn) { 
   // AJAX call  : pas moyen de tester simplement ici les erreurs sur le POST, $_POST étant vide
   // $file = null;
    $file = $_FILES['fichier'];
    $ajaxcall = true;
    $msg = "";
}  
else {
  // accès classique : POST via formulaire web
  $file = $_FILES['fichier'];
  $msg = Erreur($file, $maxsize, $extensions);
  $ajaxcall = false;
}


// si pas d'erreur d'upload, on déplace et on renomme le fichier
// puis on appelle en arrière-plan le script d'envoi de mails
if ( $msg == "") {

  $filename_ori = OriginalFileName($file); 
  if ( Renomme($file['tmp_name'],$path_upload, $nom_fic) ) {
    $msg = "Le fichier <strong>".$filename_ori."</strong> a bien été téléchargé.<br/> Vous pouvez le <a href='affiche.php?fic=$nom_fic' onclick='window.open(this.href); return false;'>consulter en ligne</a>.<br/>";
    $msg = $msg."Un e-mail de compte-rendu sera envoyé à <i>$mail_report</i> quand les messages de préavis, de relance ou de réservation auront été envoyés aux lecteurs concernés.";
    $fic = $path_upload.'/'.$nom_fic;
    // lancement du mailer en arrière-plan
    $script = $php_path." -f ".$mailer_path." -- ".$fic;
    exec($script." >> ".$fic.".log 2>&1 &");
    }
  else  {
    $msg = "Erreur système : le fichier n'a pas pu être renommé/déplacé.<br/>Vérifiez les droits et la place disponibles dans le répertoire $path_upload .<br/>";
  }
}

// si ajaxcall, on renvoie uniquement le message, sinon on présente le message dans une page HTML
if ($ajaxcall) { echo $msg; exit; }
else {
?>
<!doctype html>
<html>
<head>
<title>Bibmel <?php echo($version); ?></title>
<meta charset="utf-8">
<meta name="author" content="Jérôme Bousquié - IUT de Rodez" />
<link rel="stylesheet" type='text/css' href='default.css' title='style' />
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
<div id='logo'><img src="images/<?php echo($logo); ?>"/></div>
<div id='titre' title='Copyleft Jérôme Bousquié - IUT de Rodez'>BIBMEL</div>
<div id='corps'>
<?php echo($msg); ?>
</div>
<br/><br/><br/><br/><br/>
<a href="index.php">Retour</a>
<div id="version"><?php echo($version); ?></div>
</body>
</html>
<?php
}
?>
