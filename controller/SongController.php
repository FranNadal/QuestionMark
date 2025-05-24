<?php

class SongController
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
        $data["canciones"] = $this->model->getSongs();
        $this->view->render("songs", $data);
    }

    public function show()
    {
        if (isset($_SESSION["user"])) {
            $this->view->render("home");
        }
        $this->view->render("inicio");
    }
}