<?php

class RankingController
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
        $data["ranking"] = $this->model->obtenerDatosRanking();
        $this->view->render("ranking", $data);
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
}