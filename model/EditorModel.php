<?php

class EditorModel
{
    private $database;


    public function __construct($database)
    {
        $this->database = $database;
    }


    public function obtenerTodasLasPreguntasReportadas() {
        $sql = "SELECT 
                pj.id_pregunta,
                pj.texto,
                rp.id_reporte,
                rp.motivo,
                rp.fecha_reporte,
                u.nombre_usuario AS reportado_por
            FROM reporte_pregunta rp
            INNER JOIN preguntas_juego pj ON rp.id_pregunta = pj.id_pregunta
            INNER JOIN usuario u ON rp.id_usuario = u.id_usuario
            ORDER BY rp.fecha_reporte DESC";

        return $this->database->fetchAll($sql); // sin params, ya que no hay placeholders
    }



}