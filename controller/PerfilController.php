<?php

class PerfilController
{

    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->view = $view;
        $this->model = $model;
    }

    public function view()
    {
        session_start();
        if (isset($_SESSION['user'])) {
            $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
            $this->view->render("perfil", $data);
        } else {
            $this->redirectTo("/QuestionMark/");
        }
    }

    public function show()
    {
        session_start();
        if (isset($_SESSION["user"])) {
            $this->view->render("home");
        } else {
            $this->view->render("inicio");
        }
    }

    public function editar()
    {
        session_start();
        if (isset($_SESSION['user'])) {
            $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
            $data["edicion_perfil"] = true;
            $this->view->render("perfil", $data);
        } else {
            $this->redirectTo("/QuestionMark/");
        }
    }

    public function guardar()
    {
        session_start();
        if (!isset($_SESSION['user'])) {
            $this->redirectTo("/QuestionMark/");
        }

        $usuario = $_SESSION['user'];

        $datos = [
            'ano_nacimiento' => $_POST['ano_nacimiento'] ?? null,
            'sexo' => $_POST['sexo'] ?? null,
            'pais' => $_POST['pais'] ?? '',
            'ciudad' => $_POST['ciudad'] ?? '',
            'email' => $_POST['email'] ?? '',
        ];

        // Manejar la foto subida
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $rutaTemporal = $_FILES['foto']['tmp_name'];
            $nombreArchivo = $_FILES['foto']['name'];
            $extencionDelArchivo = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

            $extencionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp' ];

            if (in_array($extencionDelArchivo, $extencionesPermitidas)) {
                $nuevoNombreArchivo = $usuario . '_' . time() . '.' . $extencionDelArchivo;

                $direccionDeSubida = 'C:/xampp/htdocs/QuestionMark/view/img_page/';
                $rutaDestino = $direccionDeSubida . $nuevoNombreArchivo;

                if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                    // Guardar ruta relativa para usarla en la DB y en la vista
                    $datos['foto_perfil'] = 'view/img_page/' . $nuevoNombreArchivo;
                }
            }
        }

        $this->model->actualizarPerfil($usuario, $datos);
        $this->redirectTo("/QuestionMark/perfil/view");
    }


    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }
}
