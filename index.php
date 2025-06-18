<?php
session_start();
function verificarSesionActiva() {
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: /QuestionMark/");
        exit();
    }
}


require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();

$controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
$method = isset($_GET["method"]) ? $_GET["method"] : null;

$router->go(
    $controller,
    $method
);
