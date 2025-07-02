<?php
session_start();
require_once("Configuration.php");
$configuration = new Configuration();
$router = $configuration->getRouter();

$controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
$method = isset($_GET["method"]) ? $_GET["method"] : null;

// Control de acceso básico
if (!isset($_SESSION['id_usuario'])) {
    // No logueado, solo permite register y login
    if (!(($controller == "register" && $method == "register") ||
        ($controller == "login" && in_array($method, ['login', 'doLogin'])))) {
        $controller = "login";
        $method = "login";  // redirigir a login
    }
} else {
    // Usuario logueado, validamos rol
    $rol = $_SESSION['rol'];

    // Defino qué controladores/metodos son accesibles para cada rol
    $accesos = [
        'administrador' => [
            'admin' => ['viewAdmin', 'generatePdf', 'filtroEstadisticas'],
            // otros controladores y métodos permitidos para admin
        ],
        'editor' => [
            'editor' => ['viewEditor', 'aprobarPregunta' , 'altaPregunta', 'bajaPregunta', 'modificarPregunta', 'aprobarSugerida','desaprobarSugerida','rechazarReportada','aprobarReportada','editar'],
            // otros controladores y métodos que puede usar editor
        ],
        'usuario' => [
            'home' => ['view','show'],
            'jugar' => ['ruleta', 'view', 'responder', 'reporte'],
            'perfil' => ['view', 'public_view', 'show', 'editar', 'guardar'],
            'ranking' => ['view', 'show'],
            'sugerencias' => ['sugerirPregunta', 'guardarSugerencia'],
        ],
        'loggeado' => [
            'login' => ['logout'],
        ],
    ];


    // Si el controlador no está definido para el rol, o el método no está permitido, lo bloqueamos
    if ($controller !== null && $method !== null) {
        $puedeAcceder = false;

        if (isset($accesos[$rol][$controller]) && in_array($method, $accesos[$rol][$controller])) {
            $puedeAcceder = true;
        }

        if (!$puedeAcceder && isset($accesos['loggeado'][$controller]) && in_array($method, $accesos['loggeado'][$controller])) {
            $puedeAcceder = true;
        }

        if (!$puedeAcceder) {
            // Redirigir según rol
            switch ($rol) {
                case 'administrador':
                    $controller = "admin";
                    $method = "viewAdmin";
                    break;
                case 'editor':
                    $controller = "editor";
                    $method = "viewEditor";
                    break;
                case 'usuario':
                    $controller = "perfil";
                    $method = "view";
                    break;
                default:
                    $controller = "login";
                    $method = "login";
            }
        }
    }

}

$router->go(
    $controller,
    $method
);

