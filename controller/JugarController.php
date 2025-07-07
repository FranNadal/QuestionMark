<?php

class JugarController
{
    private $view;
    private $model;
    private $tiempoLimite = 10;
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
        $id_usuario  = $_SESSION['id_usuario'];
        $id_pregunta = $_POST['id_pregunta'] ?? null;
        $respuesta   = $_POST['respuesta']   ?? null;

        if (!$id_pregunta || !$respuesta) {
            header('Location: /jugar/ruleta');
            exit;
        }

        // Obtener la pregunta para poder usarla luego (en caso de tiempo agotado o respuesta incorrecta)
        $pregunta = $this->model->getPreguntaById($id_pregunta);

        if (!$pregunta) {
            $this->terminarPartida();
            return;
        }

        // Verificar si se pasó el tiempo límite
        if (isset($_SESSION['tiempo_inicio_pregunta'])) {
            $tiempo = time() - $_SESSION['tiempo_inicio_pregunta'];
            if ($tiempo > $this->tiempoLimite) {
                // Se acabó el tiempo, construir datos para finalizar la partida
                $arrayInfoFinal = [
                    'pregunta'          => $pregunta,
                    'puntaje_actual'    => $_SESSION['puntaje'] ?? 0,
                    'color_categoria'   => $this->model->getColorCategoria($_SESSION['categoria_seleccionada'] ?? 'Cultura'),
                    'es_correcta'       => false,
                    'respuesta_correcta'        => strtoupper($pregunta['respuesta_correcta']),
                    'texto_respuesta_correcta'  => $this->getTextoRespuestaCorrecta($pregunta),
                    'opciones' => $this->getOpcionesConEstilo($pregunta)
                ];

                $this->terminarPartidaPorTiempo($arrayInfoFinal);
                return;
            }
        }

        // Evaluar respuesta
        $acierto = strtoupper($pregunta['respuesta_correcta']) === strtoupper($respuesta);

        // Preparar texto de respuesta correcta para mostrar
        $respuesta_correcta = strtoupper($pregunta['respuesta_correcta']);
        $texto_respuesta_correcta = $this->getTextoRespuestaCorrecta($pregunta);

        // Actualizar estadísticas
        $this->model->actualizarEstadisticasUsuario($id_usuario, $acierto);
        $this->model->actualizarEstadisticasPregunta($id_pregunta, $acierto);

        if (!isset($_SESSION['puntaje'])) {
            $_SESSION['puntaje'] = 0;
        }

        // Preparar opciones para la vista con estilos de correcto/incorrecto
        $opciones = $this->getOpcionesConEstilo($pregunta);

        $arrayInfoFinal = [
            'pregunta'          => $pregunta,
            'puntaje_actual'    => $_SESSION['puntaje'],
            'color_categoria'   => $this->model->getColorCategoria($_SESSION['categoria_seleccionada']),
            'es_correcta'       => $acierto,
            'respuesta_correcta'=> $respuesta_correcta,
            'texto_respuesta_correcta' => $texto_respuesta_correcta,
            'opciones'          => $opciones
        ];

        if ($acierto) {
            $_SESSION['puntaje']++;
            $_SESSION['tiempo_inicio_pregunta'] = time();  // reiniciar tiempo para próxima pregunta

            $this->view->render('jugarResultado', $arrayInfoFinal);
        } else {
            $this->terminarPartida($arrayInfoFinal);
        }
    }

// Métodos auxiliares para texto y opciones (agregalos al controlador)

    private function getTextoRespuestaCorrecta($pregunta) {
        switch (strtoupper($pregunta['respuesta_correcta'])) {
            case 'A': return $pregunta['opcion_a'];
            case 'B': return $pregunta['opcion_b'];
            case 'C': return $pregunta['opcion_c'];
            case 'D': return $pregunta['opcion_d'];
            default: return '';
        }
    }

    private function getOpcionesConEstilo($pregunta) {
        $correcta = strtoupper($pregunta['respuesta_correcta']);
        return [
            ['letra' => 'A', 'texto' => $pregunta['opcion_a'], 'clase' => $correcta === 'A' ? 'opcion-correcta' : 'opcion-incorrecta'],
            ['letra' => 'B', 'texto' => $pregunta['opcion_b'], 'clase' => $correcta === 'B' ? 'opcion-correcta' : 'opcion-incorrecta'],
            ['letra' => 'C', 'texto' => $pregunta['opcion_c'], 'clase' => $correcta === 'C' ? 'opcion-correcta' : 'opcion-incorrecta'],
            ['letra' => 'D', 'texto' => $pregunta['opcion_d'], 'clase' => $correcta === 'D' ? 'opcion-correcta' : 'opcion-incorrecta'],
        ];
    }



    private function terminarPartidaConMensaje($mensaje, $arrayInfoFinal) {

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

        $arrayRespuestaIncorrecta = [
            'puntaje_final' => $puntaje,
            'mostrar_modal_fin' => true,
            'mensaje_fin' => $mensaje
        ];

        $arrayCompleto = array_merge($arrayInfoFinal, $arrayRespuestaIncorrecta);

        $this->view->render('jugarResultado', $arrayCompleto);
    }

    private function terminarPartida($arrayInfoFinal) {
        $this->terminarPartidaConMensaje('¡Respuesta incorrecta!', $arrayInfoFinal);
    }

    private function terminarPartidaPorTiempo($arrayInfoFinal) {
        $this->terminarPartidaConMensaje('¡Se acabó el tiempo!', $arrayInfoFinal);
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