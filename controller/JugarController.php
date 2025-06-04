<?php

class JugarController
{
    private $view;
    private $model;
    private $tiempoLimite = 60;
    public function __construct($model,$view){
        $this->view = $view;
        $this->model = $model;
    }
    private function verificarSesionActiva() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['id_usuario'])) {
            header("Location: /QuestionMark/");
            exit();
        }
    }

    public function view(){
        $this->verificarSesionActiva();

        if (!isset($_SESSION['fecha_inicio_partida'])) {
            $_SESSION['fecha_inicio_partida'] = date('Y-m-d H:i:s');
        }

        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        $id_usuario = $_SESSION['id_usuario'];

        $totalPreguntas = $this->model->getCantidadPreguntas();
        $jugadas = $this->model->contarPreguntasJugadas($id_usuario);

        if ($jugadas >= $totalPreguntas) {
            $this->model->borrarPreguntasJugadas($id_usuario);
        }

        $pregunta = $this->model->getPreguntaAleatoriaNoJugadas($id_usuario);

        if (!$pregunta) {
            $this->view->render("error", ["mensaje" => "No hay preguntas disponibles."]);
            return;
        }

        $this->model->registrarPreguntaJugada($id_usuario, $pregunta['id_pregunta']);

        $color_categoria = $this->model->getColorCategoria($pregunta['categoria']);

        $_SESSION['tiempo_inicio_pregunta'] = time();

        $this->view->render("jugar", [
            "pregunta" => $pregunta,
            "puntaje_actual" => $_SESSION['puntaje'],
            "color_categoria" => $color_categoria
        ]);
    }




    public function responder(){
        $this->verificarSesionActiva();

        if (isset($_SESSION['tiempo_inicio_pregunta'])) {
            $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio_pregunta'];
            if ($tiempo_transcurrido > $this->tiempoLimite) {
                $this->terminarPartidaPorTiempo();
                exit();
            }
        }

        if (!isset($_SESSION['fecha_inicio_partida'])) {
            $_SESSION['fecha_inicio_partida'] = date('Y-m-d H:i:s');
        }

        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        $id_usuario = $_SESSION['id_usuario'];
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

        $acierto = strtoupper($pregunta['respuesta_correcta']) === strtoupper($respuesta);

        // ⚠️ Ahora sí: Actualizás estadísticas después de determinar si fue acierto
        $this->model->actualizarEstadisticasUsuario($id_usuario, $acierto);
        $this->model->actualizarEstadisticasPregunta($id_pregunta, $acierto);

        if ($acierto) {
            $_SESSION['puntaje'] += 1;
            $_SESSION['tiempo_inicio_pregunta'] = time();

            header("Location: /QuestionMark/jugar/view");
            exit;
        } else {
            $this->terminarPartida();
        }
    }



    private function terminarPartida()
    {
        $this->verificarSesionActiva();
        $id_usuario = $_SESSION['id_usuario'];
        $fecha_inicio = $_SESSION['fecha_inicio_partida'];
        $fecha_fin = date('Y-m-d H:i:s');
        $puntaje = $_SESSION['puntaje'];
        $estado = 'terminada';

        $this->model->guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado);
        $this->model->borrarPreguntasJugadas($id_usuario);

        $datos = [
            'puntaje_final' => $puntaje,
            'mostrar_modal_fin' => true,
            'mensaje_fin' => '¡Respuesta incorrecta!'
        ];

        unset($_SESSION['puntaje']);
        unset($_SESSION['fecha_inicio_partida']);
        unset($_SESSION['tiempo_inicio_pregunta']);
        unset($_SESSION['preguntas_mostradas']);

        $this->view->render('jugar', $datos);
    }

    private function terminarPartidaPorTiempo() {

        $this->verificarSesionActiva();

        $id_usuario = $_SESSION['id_usuario'];
        $fecha_inicio = $_SESSION['fecha_inicio_partida'];
        $fecha_fin = date('Y-m-d H:i:s');
        $puntaje = $_SESSION['puntaje'];
        $estado = 'terminada';

        $this->model->guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado);
        $this->model->borrarPreguntasJugadas($id_usuario);

        $datos = [
            'puntaje_final' => $puntaje,
            'mostrar_modal_fin' => true,
            'mensaje_fin' => '¡Se acabó el tiempo!'
        ];

        unset($_SESSION['puntaje']);
        unset($_SESSION['fecha_inicio_partida']);
        unset($_SESSION['tiempo_inicio_pregunta']);
        unset($_SESSION['preguntas_mostradas']);

        $this->view->render('jugar', $datos);
    }

    public function reporte(){

    }


}