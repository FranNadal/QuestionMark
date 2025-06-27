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
            $this->view->render("home");
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