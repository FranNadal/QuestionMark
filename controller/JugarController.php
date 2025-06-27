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

    public function ruleta()
    {
        if (isset($_GET['categoria'])) {
            $_SESSION['categoria_seleccionada'] = $_GET['categoria'];
            header('Location: /jugar/view');
            exit;
        }

        // Capturar mensajes de sesión
        $mensajeExito = $_SESSION['mensaje_exito'] ?? null;
        $mensajeError = $_SESSION['mensaje_error'] ?? null;

        // Limpiar después de usarlos
        unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

        // Pasarlos a la vista
        $this->view->render('ruleta', [
            'mensaje_exito' => $mensajeExito,
            'mensaje_error' => $mensajeError
        ]);
    }



    public function view() {

        if (!isset($_SESSION['fecha_inicio_partida'])) {
            $_SESSION['fecha_inicio_partida'] = date('Y-m-d H:i:s');
        }

        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        $categoria = $_SESSION['categoria_seleccionada'] ?? null;
        if (!$categoria) {
            header('Location: /jugar/ruleta');
            exit;
        }

        $id_usuario = $_SESSION['id_usuario'];
        $dificultad = $this->model->determinarDificultadParaUsuario($id_usuario);

        if (!$this->model->hayPreguntasPorCategoriaYDificultad($categoria, $dificultad, $id_usuario)) {
            $this->model->borrarPreguntasJugadas($id_usuario);

            $dificultad = $this->model->determinarDificultadParaUsuario($id_usuario);

            if (!$this->model->hayPreguntasPorCategoriaYDificultad($categoria, $dificultad, $id_usuario)) {
                header('Location: /jugar/ruleta');
                exit;
            }
        }

        $pregunta = $this->model->getPreguntaPorCategoriaYDificultad($categoria, $dificultad, $id_usuario);

        if (!$pregunta) {
            header('Location: /jugar/ruleta');
            exit;
        }

        $this->model->registrarPreguntaJugada($id_usuario, $pregunta['id_pregunta']);

        $_SESSION['tiempo_inicio_pregunta'] = time();

        $this->view->render("jugar", [
            "pregunta" => $pregunta,
            "puntaje_actual" => $_SESSION['puntaje'],
            "color_categoria" => $this->model->getColorCategoria($categoria)
        ]);
    }




    public function responder()
    {

        if (isset($_SESSION['tiempo_inicio_pregunta'])) {
            $tiempo = time() - $_SESSION['tiempo_inicio_pregunta'];
            if ($tiempo > $this->tiempoLimite) {
                $this->terminarPartidaPorTiempo();
                return;
            }
        }

        $id_usuario  = $_SESSION['id_usuario'];
        $id_pregunta = $_POST['id_pregunta'] ?? null;
        $respuesta   = $_POST['respuesta']   ?? null;

        if (!$id_pregunta || !$respuesta) {
            header('Location: /jugar/ruleta');
            exit;
        }

        $pregunta = $this->model->getPreguntaById($id_pregunta);
        if (!$pregunta) {
            $this->terminarPartida();
            return;
        }

        $acierto = strtoupper($pregunta['respuesta_correcta']) === strtoupper($respuesta);

        $respuesta_correcta = strtoupper($pregunta['respuesta_correcta']);
        $texto_respuesta_correcta = '';

        switch ($respuesta_correcta) {
            case 'A':
                $texto_respuesta_correcta = $pregunta['opcion_a'];
                break;
            case 'B':
                $texto_respuesta_correcta = $pregunta['opcion_b'];
                break;
            case 'C':
                $texto_respuesta_correcta = $pregunta['opcion_c'];
                break;
            case 'D':
                $texto_respuesta_correcta = $pregunta['opcion_d'];
                break;
        }

        $this->model->actualizarEstadisticasUsuario($id_usuario, $acierto);
        $this->model->actualizarEstadisticasPregunta($id_pregunta, $acierto);

        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }
        if ($acierto) {
            $_SESSION['puntaje']++;
            $_SESSION['tiempo_inicio_pregunta'] = time();  // reinicia tiempo para la próxima


            $this->view->render('jugarResultadoCorrecto', [
                'pregunta'          => $pregunta,
                'puntaje_actual'    => $_SESSION['puntaje'],
                'color_categoria'   => $this->model->getColorCategoria($_SESSION['categoria_seleccionada']),
                'es_correcta'       => $acierto,
                'respuesta_correcta'        => $respuesta_correcta,
                'texto_respuesta_correcta'  => $texto_respuesta_correcta,
            ]);

        }

        if (!$acierto) {
            $this->terminarPartida();
        }
    }


    private function terminarPartidaConMensaje($mensaje) {

        $id_usuario = $_SESSION['id_usuario'];
        $fecha_inicio = $_SESSION['fecha_inicio_partida'];
        $fecha_fin = date('Y-m-d H:i:s');
        $puntaje = $_SESSION['puntaje'];
        $estado = 'terminada';

        $this->model->guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado);
        $this->model->borrarPreguntasJugadas($id_usuario);

        unset($_SESSION['puntaje']);
        unset($_SESSION['fecha_inicio_partida']);
        unset($_SESSION['tiempo_inicio_pregunta']);
        unset($_SESSION['preguntas_mostradas']);

        $this->view->render('jugarResultadoIncorrecto', [
            'puntaje_final' => $puntaje,
            'mostrar_modal_fin' => true,
            'mensaje_fin' => $mensaje
        ]);
    }

    private function terminarPartida() {
        $this->terminarPartidaConMensaje('¡Respuesta incorrecta!');
    }

    private function terminarPartidaPorTiempo() {
        $this->terminarPartidaConMensaje('¡Se acabó el tiempo!');
    }



    public function reporte() {
        if (!isset($_POST['id_pregunta']) || !isset($_POST['motivo']) || empty(trim($_POST['motivo']))) {
            // Si faltan datos o motivo vacío
            $_SESSION['mensaje_error'] = "Debe ingresar un motivo para reportar la pregunta.";
            header("Location: /jugar/ruleta");
            exit();
        }

        $idPregunta = $_POST['id_pregunta'];
        $motivo = trim($_POST['motivo']);
        $idUsuario = $_SESSION['id_usuario'] ?? null;

        if (!$idUsuario) {
            $_SESSION['mensaje_error'] = "Debe iniciar sesión para reportar una pregunta.";
            header("Location: /login");
            exit();
        }

        // Guardar reporte en la base de datos
        try {
            $this->model->reportarPregunta($idPregunta, $idUsuario, $motivo);
            $_SESSION['mensaje_exito'] = "Reporte enviado correctamente.";
        } catch (Exception $e) {
            $_SESSION['mensaje_error'] = "Hubo un error al enviar el reporte.";
        }

        header("Location: /jugar/ruleta"); // O a donde quieras volver
        exit();
    }




}