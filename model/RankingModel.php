<?php

class RankingModel
{

    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function obtenerDatosRanking(){
        $sql = "SELECT u.nombre_usuario as usuario, MAX(p.puntaje) as puntaje
                FROM partida p 
                JOIN usuario u ON p.id_usuario = u.id_usuario
                GROUP BY u.id_usuario, u.nombre_usuario
                HAVING MAX(p.puntaje) > 0
                ORDER BY puntaje DESC
                LIMIT 10";

        $ranking = $this->database->fetchAll($sql);

        $posicion = 1;
        $prevPuntaje = null;
        $contador = 1; // Cuántos usuarios hemos recorrido

        foreach ($ranking as &$item) {
            if ($prevPuntaje !== null && $item['puntaje'] < $prevPuntaje) {
                $posicion = $contador;
            }

            $item['posicion'] = $posicion;

            $prevPuntaje = $item['puntaje'];
            $contador++;
    }
        return $ranking;
}
}