<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function view()
    {
            $this->view->render("home");
    }

    public function show()
    {
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