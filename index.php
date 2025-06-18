<?php
session_start();
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();


$controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
$method = isset($_GET["method"]) ? $_GET["method"] : null;

if (!isset($_SESSION['id_usuario'])) {
    if (($controller != "register" && $method != "register") && ($controller != "login" && $method != "login")) {
        $controller = null;
        $method = null;
    }
}

$router->go(
    $controller,
    $method
);
