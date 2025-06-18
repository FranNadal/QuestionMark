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
            $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
            $this->view->render("perfil", $data);
    }
    public function public_view()
    {
        if (!isset($_GET['user'])) {
            echo "Perfil no encontrado.";
            exit;
        }

        $usuario = $_GET['user'];
        $perfil = $this->model->obtenerPerfil($usuario);

        if (!$perfil) {
            echo "Perfil no encontrado.";
            exit;
        }

        $data = $perfil;
        $this->view->render("perfil_publico", $data);
    }

    public function show()
    {
            $this->view->render("home");
    }

    public function editar()
    {
            $data["datos"] = $this->model->obtenerPerfil($_SESSION["user"]);
            $data["edicion_perfil"] = true;
            $this->view->render("perfil", $data);
    }

    public function guardar()
    {
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

                $direccionDeSubida = 'C:/xampp/htdocs/view/img_page/';

                $rutaDestino = $direccionDeSubida . $nuevoNombreArchivo;

                if (move_uploaded_file($rutaTemporal, $rutaDestino)) {
                    // Guardar ruta relativa para usarla en la DB y en la vista
                    $datos['foto_perfil'] = 'view/img_page/' . $nuevoNombreArchivo;
                }
            }
        }

        $this->model->actualizarPerfil($usuario, $datos);
        $this->redirectTo("/perfil/view");

    }


    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }
}
