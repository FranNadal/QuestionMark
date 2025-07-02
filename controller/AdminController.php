<?php

class AdminController
{
    private $view;
    private $model;

    public function __construct($model, $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function viewAdmin($filtro = null)
    {
        $datos = $this->model->obtenerResumenEstadisticas($filtro);
        $datos['usuarios_por_pais'] = $this->model->obtenerUsuariosPorPais($filtro);
        $datos['porcentaje_aciertos_usuarios'] = $this->model->obtenerPorcentajeAciertosPorUsuario();
        $datos['partidas_por_estado'] = $this->model->obtenerPartidasPorEstado($filtro);

        if ($filtro) {
            // Si se pasa filtro, responder con JSON (AJAX)
            header('Content-Type: application/json');
            echo json_encode($datos);
            exit;
        } else {
            // Si no, renderizar la vista completa (primer carga)
            $this->view->render("homeAdmin", $datos);
        }
    }


    public function generatePdf()
    {
        $body = json_decode(file_get_contents("php://input"), true);
        $tipo = $body['type'];

        $pdfContent = $this->model->generarReportePDF($tipo);

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=reporte_$tipo.pdf");
        echo $pdfContent;
    }

    public function filtroEstadisticas()
    {
        // En AdminController::filtroEstadisticas()
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);
        $filtro = $input['filtro'] ?? null;


        $datos = $this->model->obtenerResumenEstadisticas($filtro);
        $datos['usuarios_por_pais'] = $this->model->obtenerUsuariosPorPais($filtro);
        $datos['partidas_por_estado'] = $this->model->obtenerPartidasPorEstado($filtro);

        header("Content-Type: application/json");
        echo json_encode($datos);
    }




}
