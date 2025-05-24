<?php

class TourController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function listar()
    {
        $data["presentaciones"] = $this->model->getTours();
        $this->view->render("tours", $data);
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

}