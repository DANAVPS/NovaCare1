<?php
// app/Controllers/AutorizacionController.php

require_once __DIR__ . '/../Models/AutorizacionModel.php';
require_once __DIR__ . '/../Models/ClienteModel.php';

class AutorizacionController
{
    private $autorizacionModel;
    private $clienteModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->autorizacionModel = new AutorizacionModel();
        $this->clienteModel = new ClienteModel();
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /NovaCareCRM/public/index.php?action=login');
            exit;
        }
    }

    private function redirect($action)
    {
        header("Location: /NovaCareCRM/public/index.php?action={$action}");
        exit;
    }

    private function render($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }

    public function index()
    {
        $estado = $_GET['estado'] ?? null;
        $autorizaciones = $this->autorizacionModel->getAll($estado);
        $stats = $this->autorizacionModel->getStats();

        $this->render('autorizaciones/index', [
            'title' => 'Autorizaciones',
            'autorizaciones' => $autorizaciones,
            'stats' => $stats,
            'estadoFiltro' => $estado
        ]);
    }

    public function create()
    {
        $pacientes = $this->clienteModel->getAll('paciente', 1);

        $this->render('autorizaciones/create', [
            'title' => 'Nueva Autorización',
            'pacientes' => $pacientes
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('autorizaciones');
        }

        $result = $this->autorizacionModel->create($_POST);

        if ($result) {
            $_SESSION['flash_success'] = 'Autorización creada exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al crear la autorización';
        }

        $this->redirect('autorizaciones');
    }

    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $autorizacion = $this->autorizacionModel->getById($id);

        if (!$autorizacion) {
            $_SESSION['flash_error'] = 'Autorización no encontrada';
            $this->redirect('autorizaciones');
        }

        $this->render('autorizaciones/show', [
            'title' => 'Detalle de Autorización',
            'autorizacion' => $autorizacion
        ]);
    }

    public function aprobar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('autorizaciones');
        }

        $id = $_POST['id'] ?? 0;
        $estado = $_POST['estado'] ?? '';
        $motivo = $_POST['motivo_rechazo'] ?? null;
        $cantidadAprobada = $_POST['cantidad_aprobada'] ?? 0;

        if ($estado === 'aprobada') {
            $result = $this->autorizacionModel->aprobarConNotificacion($id, $cantidadAprobada);
            $mensaje = 'Autorización aprobada exitosamente. Se ha enviado una notificación al paciente.';
        } else {
            $result = $this->autorizacionModel->rechazarConNotificacion($id, $motivo);
            $mensaje = 'Autorización rechazada. Se ha enviado una notificación al paciente.';
        }

        if ($result) {
            $_SESSION['flash_success'] = $mensaje;
        } else {
            $_SESSION['flash_error'] = 'Error al procesar la autorización';
        }

        $this->redirect('autorizaciones');
    }
}
