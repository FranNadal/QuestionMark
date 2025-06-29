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

        //es el mensaje de exito de la pregunta sugerida
        $mensaje = $_SESSION['mensaje_exito'] ?? null;
        unset($_SESSION['mensaje_exito']);
        $this->view->render("home", [
            'mensaje_exito' => $mensaje,
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