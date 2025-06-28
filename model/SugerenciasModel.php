<?php

class SugerenciasModel
{
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function guardarSugerencia($datos)
    {
        $sql = "INSERT INTO preguntas_sugeridas 
        (id_usuario, texto, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, categoria, estado)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente')";

        $params = [
            $datos['id_usuario'],
            $datos['texto'],
            $datos['opcion_a'],
            $datos['opcion_b'],
            $datos['opcion_c'],
            $datos['opcion_d'],
            $datos['respuesta_correcta'],
            $datos['categoria'],
        ];

        $this->database->execute($sql, $params);
    }
}
