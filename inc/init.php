<?php
//-------------------- CONNEXION BDD ---------------------//
$bdd = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

//-------------------- SESSION ---------------------------//
session_start();

//-------------------- CHEMIN ----------------------------//
define("RACINE_SITE", $_SERVER['DOCUMENT_ROOT'] . "/PHP/10 - boutique/");
// echo RACINE_SITE;
// echo '<pre>'; print_r($_SERVER); echo '</pre>';
// cette constante retourne le chemin physique du doosier boutique sur le serveur
// Lors de l'enregistrement d'images/photos, nous aurons besoin du chemein du dossier photo pour enregistrer la photo

define("URL", 'http://localhost/PHP/10 - boutique/');
// cette constante servira à enregistrer l'URL d'une image/photo dans la BDD, on ne conserve jamais la photo elle même dans la BDD
//echo URL;

//------------------- VARAIBLES --------------------------//
$content = '';

//------------------- INCLUSIONS -------------------------//
require_once 'fonction.php'; 








