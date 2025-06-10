<?php

class ApiController
{
    public function ruleta()
    {
        $categorias = [
            'Historia',
            'Ciencia',
            'Deportes',
            'Arte',
            'Geografía',
            'Matemática',
            'Literatura',
            'Cultura'
        ];

        $categoria = $categorias[array_rand($categorias)];

        header('Content-Type: application/json');
        echo json_encode(['categoria' => $categoria]);
    }
}
