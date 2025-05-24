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
        if (isset($_SESSION['user'])) {
            $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
            $this->view->render("perfil", $data);
        }else{
            $this->redirectTo("/QuestionMark/");
        }

    }

    public function show()
    {
        session_start();
        if (isset($_SESSION["user"])){
            $this->view->render("home");
        }else{
            $this->view->render("inicio");
        }
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }
}