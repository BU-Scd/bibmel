<?php
include_once('conf.php');
include_once('logcas.php');

$cas_user = phpCAS::getUser();

$interdit = "";
if (!(Autorise($cas_user, $users_autorises))) { $interdit = $cas_user. " n'est pas autorisé"; }

// fonction qui renvoie une chaîne de caractères de déclaration d'un tableau javascript à partir d'un tableau php
function TabJS ($tab_php) {
  $tabjs = "[";
  foreach($tab_php as $el) {
    $tabjs = $tabjs."'".$el."',";
    }
    $tabjs = substr($tabjs, 0, -1);
    $tabjs = $tabjs."]";
    return $tabjs;
}
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
<?php 
if ($interdit=="") { ?>
<form method="POST" enctype="multipart/form-data" action="upload.php" id="upload">
<fieldset>
<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="<?php echo $maxsize; ?>" />
<div>
  <label for="fileselect">Fichier Horizon à télécharger :</label>
  <input type="file" id="fileselect" name="fichier" />
  <div id="filedrag">ou glissez-déposez le fichier ici</div>
</div>
<div id="submitbutton">
<br/>
<input type="submit" name="submit" value="Envoyer" />
</div>
</fieldset>
</form>
<input type="hidden" id="extensions"  value="<?php echo(TabJS($extensions)); ?>" />
<div id="messages">  
</div>  
<?php 
  }
else {
echo "<p>".$interdit."</p>";
  }
?>
</div>
<div id="version"><?php echo($version); ?></div>
<script type='text/javascript' src='dragdrop.js'></script>
</body>
</html>
