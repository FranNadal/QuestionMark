<?php

class InicioController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function view()
    {
        $this->view->render("inicio");
    }

    public function show()
    {
        $this->view->render("inicio");
    }

}