<?php

class HomeController
{
    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function view()
    {
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        //es el mensaje de exito de la pregunta sugerida
        $mensaje = $_SESSION['mensaje_exito'] ?? null;
        unset($_SESSION['mensaje_exito']);
        $nombre = $this->model->obtenerNombreUsuario($idUsuario);
        $ranking = $this->model->obtenerRankingUsuario($idUsuario);
        $partidas = $this->model->obtenerHistorialPartidas($idUsuario);

        $this->view->render("home", [
            'mensaje_exito' => $mensaje,
            'nombre_usuario' => $nombre,
            'posicion_ranking' => $ranking['posicion'],
            'puntaje' => $ranking['puntaje'],
            'partidas' => $partidas
        ]);
    }

    public function show()
    {
            $this->view->render("home");
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }
    public function vieweditor()
    {
        $this->view->render("homeEditor");
    }
}