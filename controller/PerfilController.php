<?php

class PerfilController
{

    private $view;
    private $model;
    public function __construct($model,$view){
        $this->view = $view;
        $this->model = $model;
    }

    public function view(){
        session_start();
        $_SESSION["user"] = "carlosr";
        $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
        $this->view->render("perfil", $data);
    }

    public function show()
    {
        if (isset($_SESSION["user"])) {
            $this->view->render("home");
        }
        $this->view->render("inicio");
    }
}