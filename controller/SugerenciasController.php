<?php

class SugerenciasController
{
    private $model;
    private $view;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    // Muestra el formulario para sugerir pregunta
    public function sugerirPregunta()
    {
        $this->view->render("sugerencias");
    }

    public function guardarSugerencia()
    {
        // No pongas session_start(); aquí

        $idUsuario = $_SESSION['id_usuario'] ?? null;
        $texto = $_POST['texto'] ?? null;
        $opcionA = $_POST['opcion_a'] ?? null;
        $opcionB = $_POST['opcion_b'] ?? null;
        $opcionC = $_POST['opcion_c'] ?? null;
        $opcionD = $_POST['opcion_d'] ?? null;
        $respuestaCorrecta = $_POST['respuesta_correcta'] ?? null;
        $categoria = $_POST['categoria'] ?? null;

        if (!$idUsuario) {
            die("Error: No hay sesión iniciada o el usuario no está autenticado.");
        }

        if ($texto && $opcionA && $opcionB && $opcionC && $opcionD && $respuestaCorrecta && $categoria) {
            $this->model->guardarSugerencia([
                'id_usuario' => $idUsuario,
                'texto' => $texto,
                'opcion_a' => $opcionA,
                'opcion_b' => $opcionB,
                'opcion_c' => $opcionC,
                'opcion_d' => $opcionD,
                'respuesta_correcta' => $respuestaCorrecta,
                'categoria' => $categoria,
            ]);

            header("Location: /index.php?controller=sugerencias&method=sugerirPregunta&msg=gracias");
            exit;
        } else {
            header("Location: /index.php?controller=sugerencias&method=sugerirPregunta&msg=error");
            exit;
        }
    }

}
