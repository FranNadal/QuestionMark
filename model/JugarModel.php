<?php

class JugarModel
{
private $database;


    public function __construct($database)
    {
        $this->database = $database;
    }

    public function getPreguntaAleatoria()
    {
        $sql =("SELECT * FROM pregunta ORDER BY RAND() LIMIT 1");
            return  $this->database->fetchOne($sql);
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
}