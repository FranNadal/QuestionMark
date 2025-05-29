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

        //guardo en la session la fecha  de inicio de la partida
        if (!isset($_SESSION['fecha_inicio_partida'])) {
            $_SESSION['fecha_inicio_partida'] = date('Y-m-d H:i:s');
        }

        // Si no hay puntaje inicializado, lo ponemos en 0
        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }
        // Si no hay preguntas mostradas , creo la sesion vacia
        if (!isset($_SESSION['preguntas_mostradas'])) {
            $_SESSION['preguntas_mostradas'] = [];
        }

        $totalPreguntas = $this->model->getCantidadPreguntas();

        // Si ya se mostraron todas, reseteamos
        if (count($_SESSION['preguntas_mostradas']) >= $totalPreguntas) {
            $_SESSION['preguntas_mostradas'] = [];
        }
        // Obtener una pregunta aleatoria que no se haya mostrado
        $pregunta = $this->model->getPreguntaAleatoria($_SESSION['preguntas_mostradas']);

        if (!$pregunta) {
            $this->view->render("error", ["mensaje" => "No hay preguntas disponibles."]);
            return;
        }
        // Guardar este ID como mostrado
        $_SESSION['preguntas_mostradas'][] = $pregunta['id_pregunta'];

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
        session_start();
        //guardo en la session la fecha  de inicio de la partida
        if (!isset($_SESSION['fecha_inicio_partida'])) {
            $_SESSION['fecha_inicio_partida'] = date('Y-m-d H:i:s');
        }

        // Si no hay puntaje inicializado, lo ponemos en 0
        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }
        $id_pregunta = $_POST['id_pregunta'] ?? null;
        $respuesta = $_POST['respuesta'] ?? null;

        if (!$id_pregunta || !$respuesta) {
            header("Location: /jugar");
            exit;
        }

        $pregunta = $this->model->getPreguntaById($id_pregunta);

        if (!$pregunta) {
            header("Location: /jugar");
            exit;
        }

        if (strtoupper($pregunta['respuesta_correcta']) === strtoupper($respuesta)) {
            $_SESSION['puntaje'] += 1;
            header("Location: /QuestionMark/jugar/view");
            exit;
        }else{
            // Guarda partida al perder
            $id_usuario = $_SESSION['id_usuario'];
            $fecha_inicio = $_SESSION['fecha_inicio_partida'];
            $fecha_fin = date('Y-m-d H:i:s');
            $puntaje = $_SESSION['puntaje'];
            $estado = 'terminada';
            $this->model->guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado);

            // Prepara datos para el view
            $datos = [
                'puntaje_final' => $puntaje,
                'mostrar_modal_fin' => true
            ];
            // Limpia sesión
            unset($_SESSION['puntaje']);
            unset($_SESSION['fecha_inicio_partida']);
            unset($_SESSION['preguntas_mostradas']);
            // Muestro el modal
            $this->view->render('jugar', $datos);
        }



    }

    public function reporte(){

    }
}