<?php
include_once('version.php');

// URL de base de l'appli web (nÃ©cessaire pour les liens inscrits dans les mmails)
// Ne pas oublier le slash final !
// ex : $url_bib="https://webapp.univ.fr/bib/";
$url_bib = "http://scdtlse3.ups-tlse.fr/bibmel/";

// conf serveur CAS
// ex :
//$cas_server = 'cas.iut-rodez.fr';
//$cas_path = '/cas';
//$cas_port = 443;
$cas_server = '';
$cas_path = '/cas';
$cas_port = 443;

// emplacement de php sur le systÃ¨me, nÃ©cessaire pour lancer php en ligne de commande
// lors de l'envoi des mails en arriÃ¨re-plan de l'appli web.
// ex : $php_path = '/usr/bin/php';
$php_path = '/usr/bin/php';

// emplacement du script mailer.php : script qui envoie les mails en arriÃ¨re-plan
// ex : $mailer_path = '/home/jerome/public_html/bib/mailer.php';
$mailer_path = '/var/www/bibmel/mailer.php';

// utilisateurs CAS autorisÃ©s Ã  uploader
// ex : $users = array('toto', 'titi');
$users_autorises = array('');

// serveur smtp, port et le cas Ã©chÃ©ant login/password (laisser vide si non nÃ©cessaires)
// $encrypt = "" ou "ssl" ou "tls". NÃ©cessite OpenSSL sur le serveur php.
// $smtp_server = "smtp.googlemail.com";
// $smtp_port = 465;
// $smtp_login = "my_account@gmail.com";
// $smtp_paswword = "MaudePasse";
// $encrypt = "tls";
$smtp_server = "";
$smtp_port = 25;
$smtp_login = "";
$smtp_password = "";
$encrypt = "";

// **********************************************************************
// Les variables ci-dessous sont utilisÃ©es si elles ne sont pas prÃ©sentes
// dans l'entÃªte de chaque message du fichier Horizon

// Variables du mail Ã  destination des usagers/lecteurs
// ----------------------------------------------------
 
// sujet du mail envoyÃ© aux lecteurs si non renseignÃ© dans Horizon
// ex : $subject_defaut = 'Le CRDOC vous informe sur vos emprunts et rÃ©servations';
$subject_defaut = 'La BU vous informe sur vos emprunts et réservations';

// from_mail par dÃ©faut si non renseignÃ© dans Horizon : from dans le mail
// ex : $from_mail_defaut = 'crdoc@iut-rodez.fr';
$from_mail_defaut = "";

// from_name par dÃ©faut si non renseignÃ© dans Horizon : nom associÃ© au from
// ex : $from_name_defaut = 'CRDOC IUT';
$from_name_defaut = "";

// reply_to par dÃ©faut si non renseignÃ© dans Horizon : adresse de rÃ©ponse
// Cette adresse peut Ãªtre utilisÃ©e par le bibliothÃ©caire pour rÃ©cupÃ©rer les erreurs
// de retour de livraison des mails : adresses erronÃ©es, etc
// ex : $reply_to_defaut = 'crdoc@iut-rodez.fr';
$reply_to_defaut = "";


// Variables du mail de compte-rendu pour le bibliothÃ©caire
// --------------------------------------------------------

// sujet du mail de compte-rendu
// ex : $mail_subject_report = "compte-rendu d'envoi du fichier Horizon";
$mail_subject_report = "Compte-rendu d'envoi du fichier Horizon";

// mail destinataire du compte-rendu. Peut-Ãªtre une liste
//ex : $mail_report = 'jerome.bousquie@iut-rodez.fr';
$mail_report = "";

// taille max en octets autorisÃ©e au fichier en upload
// note : ne pas oublier de configurer aussi cette limite dans le php.ini
// ex : $maxsize = 2000000;
$maxsize = 2000000;

// chemin du rÃ©pertoire de stockage des fichiers uploadÃ©s
// ne pas oublier de donner les droits d'Ã©criture et de lecture sur ce dossier Ã  php
// peut Ãªtre un path relatif Ã  la base de l'appli
// ex : $path_upload = 'files';
$path_upload = 'files';

// extensions de fichiers uploadÃ©s autorisÃ©es
// ex : $extensions = array('txt', 'jpg');
$extensions = array('hrz', 'txt');

// seuil en nombre de jours de suppression des fichiers uploadÃ©s
// 9999 = pas de limite. 
// Ne pas mettre zÃ©ro sous peine de supprimer le fichier en cours de traitement.
// ex : $seuil = 30;
$seuil = 30;

// nom du fichier de logo dans le rÃ©pertoire /images
// ex : $logo = 'vr_tn_IUT_Rodez.png';
$logo = 'logoups.jpg';

// time zone : http://fr2.php.net/manual/fr/timezones.php, nÃ©cessaire pour Ã©viter les WARN PHP
// ex : $time_zone = 'Europe/Paris';
$time_zone = 'Europe/Paris';

?>
