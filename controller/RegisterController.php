<?php

class RegisterController
{
private $view;
private $model;
public function __construct($model,$view){
    $this->view = $view;
    $this->model = $model;
}

public function register(){
    $this->view->render("register");
}


    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }

public function doRegister(){
$nombre_completo = $_POST['nombre_completo'];
$ano_nacimiento= $_POST['ano_nacimiento'];
$sexo= $_POST['sexo'];
$pais= $_POST['pais'];
$ciudad= $_POST['ciudad'];
$mail = $_POST['email'];
$nombre_usuario = $_POST['nombre_usuario'];
$contrasenia = $_POST['contrasenia'];
$repetirContrasenia = $_POST['repetir_contrasenia'];
$foto_perfil = $_FILES['foto_perfil'] ?? null;




        $resultado = $this->model->register(
            $nombre_completo,
            $ano_nacimiento,
            $sexo,
            $pais,
            $ciudad,
            $mail,
            $nombre_usuario,
            $contrasenia,
            $repetirContrasenia,
            $foto_perfil
        );

        if (is_string($resultado) ){
            $this->view->render('register', ['error_message' => $resultado]);
        }else{
            $this->redirectTo("/QuestionMark/group/success");

    }
}



    public function validate() {
        if (!isset($_GET['token'])) {
            echo "Token inválido.";
            return;
        }

        $token = $_GET['token'];

        $resultado = $this->model->validarCuenta($token); // este método valida el token y actualiza la DB

        if ($resultado === true) {
            $this->view->render("validacion_exitosa"); // muestra una vista de éxito
        } else {
            $this->view->render("validacion_error", ["error_message" => $resultado]); // muestra error
        }
    }








}