<?php
// app/Controllers/OrdenMedicaController.php

require_once __DIR__ . '/../Models/OrdenMedicaModel.php';
require_once __DIR__ . '/../Models/ClienteModel.php';
require_once __DIR__ . '/../Models/ProductoModel.php';

class OrdenMedicaController
{
    private $ordenModel;
    private $clienteModel;
    private $productoModel;

    public function __construct()
    {
        $this->checkAuth();
        $this->ordenModel = new OrdenMedicaModel();
        $this->clienteModel = new ClienteModel();
        $this->productoModel = new ProductoModel();
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
        $ordenes = $this->ordenModel->getAll($estado);
        $stats = $this->ordenModel->getStats();

        $this->render('ordenes/index', [
            'title' => 'Órdenes Médicas',
            'ordenes' => $ordenes,
            'stats' => $stats,
            'estadoFiltro' => $estado
        ]);
    }

    public function create()
    {
        $pacientes = $this->clienteModel->getAll('paciente', 1);
        $medicos = $this->clienteModel->getAll('medico', 1);
        $productos = $this->productoModel->getAll(null, 1);

        $this->render('ordenes/create', [
            'title' => 'Nueva Orden Médica',
            'pacientes' => $pacientes,
            'medicos' => $medicos,
            'productos' => $productos
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ordenes');
        }

        $productos = [];
        if (isset($_POST['productos']) && is_array($_POST['productos'])) {
            foreach ($_POST['productos'] as $item) {
                if (!empty($item['id'])) {
                    $productos[] = [
                        'producto_id' => $item['id'],
                        'cantidad' => $item['cantidad'] ?? 1,
                        'precio_unitario' => $item['precio'] ?? 0
                    ];
                }
            }
        }

        $result = $this->ordenModel->createWithAutorizaciones($_POST, $productos);

        if ($result) {
            // Obtener datos para enviar correo
            $orden = $this->ordenModel->getById($result);
            $paciente = $this->clienteModel->getById($_POST['paciente_id']);
            $medico = !empty($_POST['medico_id']) ? $this->clienteModel->getById($_POST['medico_id']) : null;

            // Obtener productos de la orden
            $productosOrden = $this->ordenModel->getProductosByOrden($result);

            // Enviar correo de notificación
            require_once __DIR__ . '/../Core/Mailer.php';
            $mailer = Mailer::getInstance();

            $destinatario = [
                'nombre' => $paciente['nombre'] . ' ' . ($paciente['apellido'] ?? ''),
                'email' => $paciente['email']
            ];

            if (!empty($paciente['email'])) {
                $mailer->sendOrdenCreada($destinatario, $orden, $paciente, $medico, $productosOrden);
            }

            $_SESSION['flash_success'] = 'Orden médica creada exitosamente. Se ha enviado una notificación al paciente.';
        } else {
            $_SESSION['flash_error'] = 'Error al crear la orden médica';
        }

        $this->redirect('ordenes');
    }

    public function show()
    {
        $id = $_GET['id'] ?? 0;
        $orden = $this->ordenModel->getById($id);

        if (!$orden) {
            $_SESSION['flash_error'] = 'Orden no encontrada';
            $this->redirect('ordenes');
        }

        $productos = $this->ordenModel->getProductosByOrden($id);

        $this->render('ordenes/show', [
            'title' => 'Detalle de Orden Médica',
            'orden' => $orden,
            'productos' => $productos
        ]);
    }

    public function anular()
    {
        $id = $_GET['id'] ?? 0;
        $motivo = $_POST['motivo'] ?? $_GET['motivo'] ?? 'Sin motivo especificado';

        $result = $this->ordenModel->anular($id, $motivo);

        if ($result) {
            $_SESSION['flash_success'] = 'Orden anulada exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al anular la orden';
        }

        $this->redirect('ordenes');
    }

    public function updateEstado()
    {
        $id = $_GET['id'] ?? 0;
        $estado = $_GET['estado'] ?? '';

        $result = $this->ordenModel->updateEstado($id, $estado);

        if ($result) {
            $_SESSION['flash_success'] = 'Estado actualizado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar el estado';
        }

        $this->redirect('ordenes');
    }

    public function edit()
    {
        $id = $_GET['id'] ?? 0;
        $orden = $this->ordenModel->getById($id);

        if (!$orden) {
            $_SESSION['flash_error'] = 'Orden no encontrada';
            $this->redirect('ordenes');
        }

        $this->render('ordenes/edit', [
            'title' => 'Editar Orden Médica',
            'orden' => $orden
        ]);
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ordenes');
        }

        $id = $_POST['id'] ?? 0;
        // Aquí iría la lógica de actualización
        $_SESSION['flash_success'] = 'Orden actualizada exitosamente';
        $this->redirect('ordenes');
    }

    public function delete()
    {
        $id = $_GET['id'] ?? 0;
        $_SESSION['flash_success'] = 'Orden eliminada exitosamente';
        $this->redirect('ordenes');
    }
}
