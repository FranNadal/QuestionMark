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
        $sql = "SELECT COUNT(*) as total FROM pregunta";
        $result = $this->database->fetchOne($sql);
        return $result['total'];
    }
    public function getPreguntaById($id_pregunta)
    {
        $sql = "SELECT * FROM pregunta WHERE id_pregunta = ?";
        return $this->database->fetchOne($sql, [$id_pregunta]);
    }


    public function getPreguntaAleatoria($idsMostrados)
    {
        //tomo el array de idmostrados y lo paso a string separados con coma para usarlo en sql
        $idsStr = implode(',', $idsMostrados);
        if (!empty($idsMostrados)) {
            //si no esta vacio traeme la pregunta que no tenga ese id
            $sql = "SELECT * FROM pregunta WHERE id_pregunta NOT IN ($idsStr) ORDER BY RAND() LIMIT 1";
        } else {
            //y sino traeme la que quieras pq recien arranca
            $sql = "SELECT * FROM pregunta ORDER BY RAND() LIMIT 1";
        }
        return $this->database->fetchOne($sql);
    }


    public function getColorCategoria($categoria)
    {
        $colores = [
            'Historia'   => '#FF5733',  // naranja
            'Ciencia'    => '#33B5FF',  // azul
            'Deportes'   => '#33FF57',  // verde
            'Arte'       => '#C700FF',  // violeta
            'Geografía'  => '#FFC300',  // amarillo
            'Matemática' => '#FF33A8',  // rosa fuerte
            'Literatura' => '#8E44AD',  // púrpura oscuro
            'Cultura'    => '#16A085'   // verde azulado
        ];
        return $colores[$categoria] ?? '#999999';  // gris por defecto
    }

    public function guardarPartida($id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado)
    {
$sql=("INSERT INTO partida (id_usuario, fecha_inicio, fecha_fin, puntaje, estado) VALUES (?, ?, ?, ?, ?)");
$this->database->execute($sql, [$id_usuario, $fecha_inicio, $fecha_fin, $puntaje, $estado]);
    }
}