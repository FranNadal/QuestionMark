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

    public function viewAdmin()
    {
        $datos = $this->model->obtenerResumenEstadisticas();
        $datos['usuarios_por_pais'] = $this->model->obtenerUsuariosPorPais();
        $datos['porcentaje_aciertos_usuarios'] = $this->model->obtenerPorcentajeAciertosPorUsuario();

        $this->view->render("homeAdmin", $datos);
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

//    public function filterStats()
//    {
//        $body = json_decode(file_get_contents("php://input"), true);
//        $filtro = $body['filtro'] ?? 'mes';
//
//        $datos = $this->model->obtenerEstadisticasFiltradas($filtro);
//
//        header('Content-Type: application/json');
//        echo json_encode($datos);
//    }

}
