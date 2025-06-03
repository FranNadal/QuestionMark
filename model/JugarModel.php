<?php

class JugarModel
{
private $database;


    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getCantidadPreguntas()
    {
        // Cambio tabla 'pregunta' por 'preguntas_juego'
        $sql = "SELECT COUNT(*) as total FROM preguntas_juego";
        $result = $this->database->fetchOne($sql);
        return $result['total'];
    }
    public function getPreguntaById($id_pregunta)
    {
        // Cambio tabla 'pregunta' por join entre preguntas_juego y respuestas_juego
        $sql = "SELECT p.*, r.opcion_a, r.opcion_b, r.opcion_c, r.opcion_d, r.respuesta_correcta
                FROM preguntas_juego p
                JOIN respuestas_juego r ON p.id_pregunta = r.id_pregunta
                WHERE p.id_pregunta = ?";
        return $this->database->fetchOne($sql, [$id_pregunta]);
    }


    public function getPreguntaAleatoria($idsMostrados)
    {
        // Cambio tabla 'pregunta' por join entre preguntas_juego y respuestas_juego
        $idsStr = implode(',', $idsMostrados);
        if (!empty($idsMostrados)) {
            $sql = "SELECT p.*, r.opcion_a, r.opcion_b, r.opcion_c, r.opcion_d, r.respuesta_correcta
                    FROM preguntas_juego p
                    JOIN respuestas_juego r ON p.id_pregunta = r.id_pregunta
                    WHERE p.id_pregunta NOT IN ($idsStr)
                    ORDER BY RAND() LIMIT 1";
        } else {
            $sql = "SELECT p.*, r.opcion_a, r.opcion_b, r.opcion_c, r.opcion_d, r.respuesta_correcta
                    FROM preguntas_juego p
                    JOIN respuestas_juego r ON p.id_pregunta = r.id_pregunta
                    ORDER BY RAND() LIMIT 1";
        }
        return $this->database->fetchOne($sql);
    }


    public function getColorCategoria($categoria)
    {
        $colores = [
            'Historia'   => '#FF5733',  // naranja
            'Ciencia'    => '#33B5FF',  // azul
            'Deportes'   => '#0b842e',  // verde
            'Arte'       => '#C700FF',  // violeta
            'Geografía'  => '#FFC300',  // amarillo
            'Matemática' => '#FF33A8',  // rosa fuerte
            'Literatura' => '#FF0000',  // rojo
            'Cultura'    => '#16A085'   // verde azulado
        ];
        return $colores[$categoria] ?? '#999999';  // gris por defecto
    }

    public function guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado)
    {
$sql=("INSERT INTO partida (id_usuario, fecha_inicio, fecha_fin, puntaje, estado) VALUES (?, ?, ?, ?, ?)");
$this->database->execute($sql, [$id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado]);
    }


    public function getPreguntaAleatoriaNoJugadas($id_usuario)
    {
        $sql = "SELECT p.*, r.opcion_a, r.opcion_b, r.opcion_c, r.opcion_d, r.respuesta_correcta
            FROM preguntas_juego p
            JOIN respuestas_juego r ON p.id_pregunta = r.id_pregunta
            WHERE p.id_pregunta NOT IN (
                SELECT id_pregunta FROM preguntas_jugadas WHERE id_usuario = ?
            )
            ORDER BY RAND() LIMIT 1";
        return $this->database->fetchOne($sql, [$id_usuario]);
    }

    public function registrarPreguntaJugada($id_usuario, $id_pregunta)
    {
        $sql = "INSERT INTO preguntas_jugadas (id_usuario, id_pregunta) VALUES (?, ?)";
        $this->database->execute($sql, [$id_usuario, $id_pregunta]);
    }

    public function contarPreguntasJugadas($id_usuario)
    {
        $sql = "SELECT COUNT(*) as total FROM preguntas_jugadas WHERE id_usuario = ?";
        $result = $this->database->fetchOne($sql, [$id_usuario]);
        return $result['total'];
    }

    public function borrarPreguntasJugadas($id_usuario)
    {
        $sql = "DELETE FROM preguntas_jugadas WHERE id_usuario = ?";
        $this->database->execute($sql, [$id_usuario]);
    }
}