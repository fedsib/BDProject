<?php 
//Distruggo la sessione corrende e redirigo l'utente all'home page
session_start();
session_destroy();
header("location:index.php"); 
exit;
?>