# Bibmel

Bibmel source code, a tool used by libraries to send emails to users who didn't bring back their books.


Présentation
------------

Bibmel est un logiciel web CASsifié, créé par [Jérôme Bousquié](https://github.com/jbousquie), de l'IUT de Rodez, et modifié par la suite par Jérémy Raingeard, de l'Université Toulouse III - Paul Sabatier. Bibmel permet aux bibliothécaires d'envoyer par mail à leurs usagers les messages de rappel, de préavis ou de réservation à partir d'un fichier texte produit par le progiciel Horizon. Bibmel constitue une alternative web au logiciel Infomail.

Utilisation
-----------

Le bibliothécaire produit le fichier souhaité à partir de son client Horizon et l'enregistre sur son poste de travail Windows - de préférence en .hrz. Il se connecte ensuite au service bibmel de son établissement après s'être authentifié (CAS). Selon la version de son navigateur, il glisse/dépose le fichier Horizon (.hrz) ou le sélectionne dans celui-ci. Le fichier est alors aussitôt transféré au serveur bibmel qui se charge de l'envoi des mails aux destinataires contenus dans le fichier.

Le bibliothécaire peut immédiatement consulter en ligne le contenu du fichier uploadé, ainsi que les messages qui ne seront pas émis faute d'un e-mail de destinataire renseigné. S'il désire imprimer, il utilise simplement le menu Imprimer de son navigateur : bibmel se charge d'insérer des sauts de pages entre chaque message et de préparer la sortie au format A4 portrait.

À l'issue de l'émission des messages aux usagers, un mail de compte-rendu est envoyé au bibliothécaire. Il peut aussi recevoir par ailleurs l'ensemble des retours d'erreur : mails mal renseignés dans le fichier initial, boites destinataires pleines, etc. Par ailleurs, il reçoit la liste éventuelle des messages dont les mails n'ont pu être envoyés, par numéro de série.

Déploiement
-----------

Pré-requis
----------

Bibmel est application CASsifiée web codée php5. Elle requiert donc un serveur web avec php5 installé en mode CLI.
Il est aussi nécessaire de pouvoir accéder à un serveur CAS pour authentifier les accès et à un serveur SMTP pour l'envoi de mails. Les librairies clientes CAS et SMTP sont incluses dans bibmel, il n'y a donc aucune dépendance extérieure.

Bibmel utilise en interne les librairies :

    Swiftmailer
    Php CAS Client

Installation
------------

Dézipper le fichier downloadé. Copier le répertoire dézippé sur le serveur web/php. Bibmel n'utilise pas de base de données, mais stocke temporairement les fichiers uploadés dans un répertoire (par défaut bibmel/files/) auquel il faudra donner les droits en écriture à php (user www-data sur les distributions debian/ubuntu).

Paramétrage
-----------

Note préalable : vérifier les limites de taille de fichier en upload sur les configurations Apache, mod_security et php.ini

La configuration est placée dans un unique fichier : bibmel/conf.php

Explication détaillée du paramétrage et des logs.
