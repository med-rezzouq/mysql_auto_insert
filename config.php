<?php
$base = 'test';
$user = 'root';
$pass = '';
try {
  //connexion avec la base de données.
  global $bdd;
  $bdd = new PDO('mysql:host=localhost;dbname=' . $base, $user, $pass);
  //  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // $bdd->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
} catch (Exception $message) {
  die('Erreur : ' . $message->getMessage());
}
