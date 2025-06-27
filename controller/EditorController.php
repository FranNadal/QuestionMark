<?php

class EditorController
{




    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->view = $view;
        $this->model = $model;
    }




    public function viewEditor()
    {



        $preguntas = $this->model->obtenerTodasLasPreguntasReportadas();
        $this->view->render("homeEditor",["preguntas" => $preguntas]);
    }

    public function altaPregunta()
    {


        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->darAltaPregunta($id);
        }
        header("Location: /editor/viewEditor");
    }

    public function bajaPregunta()
    {


        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->darBajaPregunta($id);
        }
        header("Location: /editor/viewEditor");
    }

    public function modificarPregunta()
    {


        $id = $_POST['id_pregunta'] ?? null;
        $texto = $_POST['nuevo_texto'] ?? null;
        if ($id && $texto) {
            $this->model->modificarTextoPregunta($id, $texto);
        }
        header("Location: /editor/viewEditor");
    }

    public function aprobarSugerida()
    {


        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->aprobarPreguntaSugerida($id);
        }
        header("Location: /editor/viewEditor");
    }

    public function rechazarReportada()
    {


        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->rechazarPreguntaReportada($id);
        }
        header("Location: /editor/viewEditor");
    }



}