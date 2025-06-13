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
        if (isset($_SESSION['user'])) {
            $this->view->render("home");
        }else{
            $this->redirectTo("/QuestionMark/");

        }
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