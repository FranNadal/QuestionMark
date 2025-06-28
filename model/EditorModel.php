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
                pj.id_pregunta AS id,
                pj.texto,
                rp.id_reporte,
                rp.motivo,
                rp.fecha_reporte,
                u.nombre_usuario AS reportado_por,
                u.id_usuario
            FROM reporte_pregunta rp
            INNER JOIN preguntas_juego pj ON rp.id_pregunta = pj.id_pregunta
            INNER JOIN usuario u ON rp.id_usuario = u.id_usuario
            ORDER BY rp.fecha_reporte DESC";

        return $this->database->fetchAll($sql);
    }

    public function rechazarPreguntaReportada($idPregunta, $idUsuario)
    {
        $sqlUpdate = "UPDATE preguntas_juego SET estado = 'activa' WHERE id_pregunta = ?";
        $this->database->execute($sqlUpdate, [$idPregunta]);

        $sql = "DELETE FROM reporte_pregunta WHERE id_pregunta = ? AND id_usuario = ?";
        $this->database->execute($sql, [$idPregunta, $idUsuario]);

    }

    public function aprobarPreguntaReportada($idPregunta)
    {
        $sql = "UPDATE preguntas_juego SET estado = 'desactivada' WHERE id_pregunta = ?";
        $this->database->execute($sql, [$idPregunta]);
    }



    public function obtenerPreguntaPorId($idPregunta)
    {
        $sql = "SELECT id_pregunta, texto FROM preguntas_juego WHERE id_pregunta = ?";
        return $this->database->fetchOne($sql, [$idPregunta]);
    }



    public function obtenerRespuestasPorPregunta($idPregunta)
    {
        $sql = "SELECT * FROM respuestas_juego WHERE id_pregunta = ?";
        $respuesta = $this->database->fetchOne($sql, [$idPregunta]);

        $correcta = $respuesta['respuesta_correcta'];
        $respuesta["correcta_A"] = $correcta === 'A';
        $respuesta["correcta_B"] = $correcta === 'B';
        $respuesta["correcta_C"] = $correcta === 'C';
        $respuesta["correcta_D"] = $correcta === 'D';

        return $respuesta;
    }



    public function modificarRespuestas($idPregunta, $respuestas, $respuestaCorrecta)
    {
        $sql = "UPDATE respuestas_juego 
            SET opcion_a = ?, opcion_b = ?, opcion_c = ?, opcion_d = ?, respuesta_correcta = ?
            WHERE id_pregunta = ?";

        $this->database->execute($sql, [
            $respuestas['opcion_a'],
            $respuestas['opcion_b'],
            $respuestas['opcion_c'],
            $respuestas['opcion_d'],
            $respuestaCorrecta,
            $idPregunta
        ]);
    }

    public function modificarTextoPregunta($idPregunta, $nuevoTexto)
    {
        $sql = "UPDATE preguntas_juego SET texto = ? WHERE id_pregunta = ?";
        $this->database->execute($sql, [$nuevoTexto, $idPregunta]);
    }

//    public function darAltaPregunta($idPregunta)
//    {
//        $sql = "UPDATE preguntas_juego SET estado = 'activa' WHERE id_pregunta = ?";
//        $this->database->execute($sql, [$idPregunta]);
//    }


}