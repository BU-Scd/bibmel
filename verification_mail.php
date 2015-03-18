<?php

/* Ajout par Jeremy Raingeard (Universite Paul Sabatier - Toulouse) ***********************************************/

function verifMail($mail) {
  $Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';

  if(preg_match($Syntaxe,$mail)) {
    return true;
  } else {
    return false;
  }
}
