<?php

class HomeController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {
        if (isset($_SESSION["user"])) {
            $this->view->render("home");
        }
        $this->view->render("inicio");
    }
}