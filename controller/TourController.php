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
        if (isset($_SESSION["user"])) {
            $this->view->render("home");
        }
        $this->view->render("inicio");
    }

}