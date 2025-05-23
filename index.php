<?php
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();
// Verificamos si vienen los parámetros, si no, usamos los valores por defecto del router
$controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
$method = isset($_GET["method"]) ? $_GET["method"] : null;
$router->go(
    $_GET["controller"],
    $_GET["method"]
);