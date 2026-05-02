<?php
// app/Controllers/DashboardController.php

require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/ClienteModel.php';
require_once __DIR__ . '/../Models/ProductoModel.php';
require_once __DIR__ . '/../Models/OrdenMedicaModel.php';

class DashboardController {
    private $userModel;
    private $clienteModel;
    private $productoModel;
    private $ordenModel;
    
    public function __construct() {
        $this->checkAuth();
        $this->userModel = new UserModel();
        $this->clienteModel = new ClienteModel();
        $this->productoModel = new ProductoModel();
        $this->ordenModel = new OrdenMedicaModel();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /NovaCareCRM/public/index.php?action=login');
            exit;
        }
    }
    
    public function index() {
        $user = $this->userModel->findById($_SESSION['user_id']);
        $statsUsuarios = $this->userModel->getStats();
        $statsClientes = $this->clienteModel->getStats();
        $statsProductos = $this->productoModel->getStats();
        $statsOrdenes = $this->ordenModel->getStats();
        
        // Combinar todas las estadísticas
        $stats = [
            'total_usuarios' => $statsUsuarios['total_users'] ?? 0,
            'total_clientes' => $statsClientes['total'] ?? 0,
            'total_productos' => $statsProductos['total'] ?? 0,
            'ordenes_hoy' => $statsOrdenes['total'] ?? 0,
            'autorizaciones_pendientes' => 0 // Temporal
        ];
        
        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user' => $user,
            'stats' => $stats
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
}