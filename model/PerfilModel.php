<?php

class PerfilModel
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerPerfil($usuario){
        $sql = "SELECT * FROM usuario WHERE nombre_usuario = ?";
        return $this->database->fetchOne($sql, [$usuario]);
    }

    public function actualizarPerfil($usuario, $datos)
    {
        $sql = "UPDATE usuario SET 
                ano_nacimiento = ?,
                sexo = ?,
                pais = ?,
                ciudad = ?,
                email = ?";

        $params = [
            $datos['ano_nacimiento'],
            $datos['sexo'],
            $datos['pais'],
            $datos['ciudad'],
            $datos['email']
        ];

        if (isset($datos['foto_perfil'])) {
            $sql .= ", foto_perfil = ?";
            $params[] = $datos['foto_perfil'];
        }

        $sql .= " WHERE nombre_usuario = ?";
        $params[] = $usuario;

        return $this->database->execute($sql, $params);
    }

}