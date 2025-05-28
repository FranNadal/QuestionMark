<?php

class JugarController
{
    private $view;
    private $model;
    public function __construct($model,$view){
        $this->view = $view;
        $this->model = $model;
    }

    public function view(){
        session_start();

        // Si no hay puntaje inicializado, lo ponemos en 0
        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        // Obtener una pregunta (la primera o una aleatoria)
        $pregunta = $this->model->getPreguntaAleatoria();

        if (!$pregunta) {
            $this->view->render("error", ["mensaje" => "No hay preguntas disponibles."]);
            return;
        }

        // Obtener el color de la categoría
        $color_categoria = $this->model->getColorCategoria($pregunta['categoria']);

        // Renderizar la vista
        $this->view->render("jugar", [
            "pregunta" => $pregunta,
            "puntaje_actual" => $_SESSION['puntaje'],
            "color_categoria" => $color_categoria
        ]);
    }




    public function responder(){
        $id_pregunta = $_POST['id_pregunta'];
        $respuesta_usuario = $_POST['respuesta'];
    }

    public function reporte(){

    }
}