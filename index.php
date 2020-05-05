<?php
/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once("acc/AuthenticationManager.php");
require_once("model/MotoStorageMySQL.php");

$motoStorage = new MotoStorageMySQL();
$auth = new AuthenticationManager();
$router = new Router();
$router->main($motoStorage,$auth);
?>