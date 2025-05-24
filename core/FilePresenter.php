<?php

class FilePresenter
{
    public function __construct()
    {
    }

    public function render($viewName, $data = [])
    {
        require_once("view/" . $viewName . "View.php");
    }

}