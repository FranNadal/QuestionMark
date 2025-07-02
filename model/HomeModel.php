<?php

class HomeModel
{
    private $database;

    public function __construct($database){
        $this->database = $database;
    }
    public function obtenerNombreUsuario($idUsuario)
    {
        $resultado = $this->database->fetchOne(
            "SELECT nombre_usuario FROM usuario WHERE id_usuario = ?",
            [$idUsuario]
        );
        return $resultado['nombre_usuario'] ?? 'Usuario';
    }

    public function obtenerRankingUsuario($idUsuario)
    {
        $sql = "
        SELECT posicion, puntaje FROM (
            SELECT id_usuario,
                   MAX(puntaje) AS puntaje,
                   RANK() OVER (ORDER BY MAX(puntaje) DESC) AS posicion
            FROM partida
            GROUP BY id_usuario
        ) AS ranking
        WHERE id_usuario = ?
    ";

        $resultado = $this->database->fetchOne($sql, [$idUsuario]);

        return $resultado ?: ['posicion' => 'N/A', 'puntaje' => 0];
    }


    public function obtenerHistorialPartidas($idUsuario)
    {
        return $this->database->fetchAll(
            "SELECT 
            puntaje,
            estado AS resultado,
            DATE_FORMAT(fecha_inicio, '%e/%c/%Y') AS fecha_formateada
         FROM partida
         WHERE id_usuario = ?
         ORDER BY fecha_inicio DESC
         LIMIT 5",
            [$idUsuario]
        );
    }


}