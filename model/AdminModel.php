<?php

class AdminModel
{
    private $database;

    public function __construct($db)
    {
        $this->database = $db;
    }

    public function obtenerResumenEstadisticas()
    {
        $usuarios = $this->database->query("SELECT COUNT(*) AS total FROM usuario")->fetch_assoc()['total'];
        $partidas = $this->database->query("SELECT COUNT(*) AS total FROM partida")->fetch_assoc()['total'];
        $preguntas = $this->database->query("SELECT COUNT(*) AS total FROM preguntas_juego")->fetch_assoc()['total'];
        $creadas = $this->database->query("SELECT COUNT(*) AS total FROM preguntas_juego WHERE creada_por IS NOT NULL")->fetch_assoc()['total'];
        $usuariosNuevos = $this->database->query("SELECT COUNT(*) AS total FROM usuario WHERE fecha_registro >= CURDATE() - INTERVAL 7 DAY")->fetch_assoc()['total'];
        $promEdad = $this->database->query("SELECT AVG(YEAR(CURDATE()) - ano_nacimiento) AS promedio FROM usuario")->fetch_assoc()['promedio'];

        $aciertos = $this->database->query("
        SELECT AVG(respuestas_correctas / preguntas_respondidas) * 100 AS ratio 
        FROM usuario_estadisticas 
        WHERE preguntas_respondidas > 0
    ")->fetch_assoc()['ratio'];

        $sexo = $this->database->query("
        SELECT 
            SUM(sexo = 'M') AS totalDeHombres,
            SUM(sexo = 'F') AS totalDeMujeres,
            SUM(sexo = 'N') AS totalDeElles
        FROM usuario
    ")->fetch_assoc();

        $edades = $this->database->query("
        SELECT 
            SUM(YEAR(CURDATE()) - ano_nacimiento < 18) AS cantidad_menores,
            SUM(YEAR(CURDATE()) - ano_nacimiento BETWEEN 18 AND 65) AS cantidad_adultos,
            SUM(YEAR(CURDATE()) - ano_nacimiento > 65) AS cantidad_jubilados
        FROM usuario
    ")->fetch_assoc();

        // NUEVO: total de preguntas respondidas y acertadas (para gráfico de torta)
        $usuarioEstad = $this->database->query("
        SELECT 
            SUM(preguntas_respondidas) AS total_respondidas, 
            SUM(respuestas_correctas) AS total_aciertos
        FROM usuario_estadisticas
    ")->fetch_assoc();

        return array_merge([
            'total_users' => $usuarios,
            'total_games' => $partidas,
            'total_questions' => $preguntas,
            'total_questions_created' => $creadas,
            'total_users_created' => $usuariosNuevos,
            'promedio_edad' => round($promEdad),
            'ratio_aciertos' => round($aciertos, 2),
            'total_preguntas_respondidas' => $usuarioEstad['total_respondidas'] ?? 0,
            'total_respuestas_correctas' => $usuarioEstad['total_aciertos'] ?? 0,
        ], $sexo, $edades);
    }
//    public function obtenerEstadisticasFiltradas($filtro)
//    {
//        $rangos = $this->calcularRangoFechas($filtro);
//        $desde = $rangos['desde'];
//        $hasta = $rangos['hasta'];
//
//        $totalUsuarios = $this->database->query("SELECT COUNT(*) AS total FROM usuario")->fetch_assoc()['total'];
//
//        $usuariosNuevos = $this->database->query("
//        SELECT COUNT(*) AS total
//        FROM usuario
//        WHERE fecha_registro BETWEEN '$desde' AND '$hasta'
//    ")->fetch_assoc()['total'];
//
//        $sexo = $this->database->query("
//        SELECT
//            SUM(sexo = 'M') AS totalDeHombres,
//            SUM(sexo = 'F') AS totalDeMujeres,
//            SUM(sexo = 'N') AS totalDeElles
//        FROM usuario
//        WHERE fecha_registro BETWEEN '$desde' AND '$hasta'
//    ")->fetch_assoc();
//
//        $edades = $this->database->query("
//        SELECT
//            SUM(YEAR(CURDATE()) - ano_nacimiento < 18) AS cantidad_menores,
//            SUM(YEAR(CURDATE()) - ano_nacimiento BETWEEN 18 AND 65) AS cantidad_adultos,
//            SUM(YEAR(CURDATE()) - ano_nacimiento > 65) AS cantidad_jubilados
//        FROM usuario
//        WHERE fecha_registro BETWEEN '$desde' AND '$hasta'
//    ")->fetch_assoc();
//
//        $usuarioEstad = $this->database->query("
//        SELECT
//            SUM(ue.preguntas_respondidas) AS total_respondidas,
//            SUM(ue.respuestas_correctas) AS total_aciertos
//        FROM usuario_estadisticas ue
//        JOIN usuario u ON u.id_usuario = ue.id_usuario
//        WHERE u.fecha_registro BETWEEN '$desde' AND '$hasta'
//    ")->fetch_assoc();
//
//        return array_merge([
//            'total_users' => $totalUsuarios,
//            'total_users_created' => $usuariosNuevos,
//            'total_preguntas_respondidas' => $usuarioEstad['total_respondidas'] ?? 0,
//            'total_respuestas_correctas' => $usuarioEstad['total_aciertos'] ?? 0,
//        ], $sexo, $edades);
//    }
//
//
//    private function calcularRangoFechas($filtro)
//    {
//        $hoy = date('Y-m-d');
//        switch ($filtro) {
//            case 'dia':
//                return ['desde' => $hoy, 'hasta' => $hoy];
//            case 'semana':
//                $inicio = date('Y-m-d', strtotime('monday this week'));
//                return ['desde' => $inicio, 'hasta' => $hoy];
//            case 'anio':
//                $inicio = date('Y') . '-01-01';
//                return ['desde' => $inicio, 'hasta' => $hoy];
//            case 'mes':
//            default:
//                $inicio = date('Y-m-01');
//                return ['desde' => $inicio, 'hasta' => $hoy];
//        }
//    }
    public function obtenerUsuariosPorPais()
    {
        $result = $this->database->query("
        SELECT pais, COUNT(*) AS cantidad
        FROM usuario
        GROUP BY pais
    ");

        $usuariosPorPais = [];
        while ($row = $result->fetch_assoc()) {
            $usuariosPorPais[] = $row;
        }

        return $usuariosPorPais;
    }

    public function obtenerPorcentajeAciertosPorUsuario()
    {
        $result = $this->database->query("
        SELECT u.nombre_usuario, 
               ROUND((ue.respuestas_correctas / ue.preguntas_respondidas) * 100, 2) AS porcentaje
        FROM usuario_estadisticas ue
        JOIN usuario u ON ue.id_usuario = u.id_usuario
        WHERE ue.preguntas_respondidas > 0
        ORDER BY porcentaje DESC
        LIMIT 10
    ");

        $porcentajeUsuarios = [];
        while ($row = $result->fetch_assoc()) {
            $porcentajeUsuarios[] = $row;
        }

        return $porcentajeUsuarios;
    }

    public function generarReportePDF($tipo)
    {
        // Esta función debería usar una librería como TCPDF, Dompdf o mPDF
        // Para este ejemplo devolvemos un PDF básico (puedo ayudarte a generar el real)
        return file_get_contents(__DIR__ . "/pdfs/ejemplo_$tipo.pdf");
    }
}
