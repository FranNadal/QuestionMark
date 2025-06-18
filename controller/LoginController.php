<?php

class LoginController
{

    private $view;
    private $model;
    public function __construct($model,$view){
        $this->view = $view;
        $this->model = $model;
    }
    public function login(){
        $this->view->render("login", ['error_message' => '']);
    }

    public function show()
    {
            $this->view->render("home");
    }

    private function redirectTo($str)
    {
        header("location:" . $str);
        exit();
    }

    public function doLogin(){
        $email = $_POST['email'];
        $contrasenia = $_POST['contrasenia'];

        $resultado = $this->model->loguearse($email,$contrasenia);

        if (is_string($resultado) ){
            $this->view->render('login', ['error_message' => $resultado]);

        }else{

            $_SESSION['user'] = $resultado["nombre_usuario"];
            $_SESSION['id_usuario'] = $resultado["id_usuario"];

            $this->redirectTo('/home/view');
        }
    }

    public function logout()
    {
        session_destroy();
        $this->redirectTo("/");
    }


}