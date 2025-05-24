<?php

class PerfilModel
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPerfil($usuario){
        $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
        return $this->database->fetchOne($sql, [$usuario]);
    }
}