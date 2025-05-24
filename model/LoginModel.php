<?php

class LoginModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }


    public function loguearse($email, $contrasenia){
        $sql = "SELECT * FROM usuario WHERE email = ?";
        $usuario = $this->database->fetchOne($sql, [$email]);

        if (!$usuario) {
            return "El usuario no existe.";
        }

        // Verificar si la cuenta está validada
        if ($usuario['cuenta_validada'] != 1) {
            return "La cuenta no está validada. Por favor revisá tu correo.";
        }

        // Verificar contraseña directamente (sin hash)
        if ($contrasenia !== $usuario['contrasenia']) {
            return "La contraseña es incorrecta.";
        }
        // Login exitoso: podés devolver el usuario
        return $usuario;
    }
}