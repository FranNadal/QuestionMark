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

    // Muestra la vista del panel del editor con las preguntas reportadas
    public function viewEditor()
    {
        $preguntas = $this->model->obtenerTodasLasPreguntasReportadas();
        $preguntasSugeridas = $this->model->obtenerTodasLasPreguntasSugeridas();
        $this->view->render("homeEditor", ["preguntas" => $preguntas,
            'preguntas_sugeridas' => $preguntasSugeridas]);
    }

    // Da de alta una pregunta (activarla)
    public function altaPregunta()
    {
        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->darAltaPregunta($id);
        }
        header("Location: /editor/viewEditor");
    }

    // Da de baja una pregunta (desactivarla)
    public function bajaPregunta()
    {
        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->darBajaPregunta($id);
        }
        header("Location: /editor/viewEditor");
    }

    // Modifica el texto de una pregunta
    public function modificarPregunta()
    {
        $idPregunta = $_POST['id_pregunta'] ?? null;
        $nuevoTexto = $_POST['nuevo_texto'] ?? null;
        $respuestas = $_POST['respuestas'] ?? [];
        $idRespuestaCorrecta = $_POST['respuesta_correcta'] ?? null;

        if ($idPregunta && $nuevoTexto && count($respuestas) > 0 && $idRespuestaCorrecta) {
            $this->model->modificarTextoPregunta($idPregunta, $nuevoTexto);
            $this->model->modificarRespuestas($idPregunta, $respuestas, $idRespuestaCorrecta);
            $this->model->darAltaPregunta($idPregunta);
        }
        header("Location: /editor/viewEditor");
    }


    // Aprueba una pregunta sugerida
    public function aprobarSugerida()
    {
        $id = $_POST['id_pregunta'] ?? null;
        if ($id) {
            $this->model->aprobarPreguntaSugerida($id);
        }
        header("Location: /editor/viewEditor");
    }
    public function desaprobarSugerida()
    {
        $idPregunta = $_POST['id_pregunta'] ?? null;

        if ($idPregunta) {
            $this->model->eliminarPreguntaSugerida($idPregunta);
        }

        header("Location: /editor/viewEditor");
        exit;
    }

    // Rechaza un reporte eliminando el reporte hecho por un usuario para una pregunta específica

    public function rechazarReportada()
    {
        $idPregunta = $_POST['id_pregunta'] ?? null;
        $idUsuario = $_POST['id_usuario'] ?? null;

        if ($idPregunta && $idUsuario) {
            $this->model->rechazarPreguntaReportada($idPregunta, $idUsuario);
        }

        header("Location: /editor/viewEditor");
    }

    public function aprobarReportada()
    {
        $idPregunta = $_POST['id_pregunta'] ?? null;
        if ($idPregunta) {
            $this->model->aprobarPreguntaReportada($idPregunta,);
        }

        header("Location: /editor/viewEditor");
    }


//    public function editarPregunta()
//    {
//        $idPregunta = $_GET['id_pregunta'] ?? null;
//        if (!$idPregunta) {
//            header("Location: /editor/viewEditor");
//            exit;
//        }
//
//        // Obtené la pregunta
//        $pregunta = $this->model->obtenerPreguntaPorId($idPregunta);
//
//        // Obtené las respuestas de la pregunta
//        $respuestas = $this->model->obtenerRespuestasPorPregunta($idPregunta);
//
//        // Renderizá la vista con los datos
//        $this->view->render("editarPregunta", [
//            "pregunta" => $pregunta,
//            "respuestas" => $respuestas
//        ]);
//    }

    public function editar()
    {
        $idPregunta = $_GET['id_pregunta'] ?? null;
        if (!$idPregunta) {
            header("Location: /editor/viewEditor");
            exit;
        }

        $pregunta = $this->model->obtenerPreguntaPorId($idPregunta);
        $respuestas = $this->model->obtenerRespuestasPorPregunta($idPregunta);

        $this->view->render("editarPregunta", [
            "pregunta" => $pregunta,
            "respuestas" => $respuestas
        ]);
    }

}

