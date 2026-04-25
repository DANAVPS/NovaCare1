<?php
// app/Controllers/ClienteController.php

require_once __DIR__ . '/../Models/ClienteModel.php';

class ClienteController {
    private $clienteModel;
    
    public function __construct() {
        $this->checkAuth();
        $this->clienteModel = new ClienteModel();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /xampp/NovaCareCRM/public/index.php?action=login');
            exit;
        }
    }
    
    private function redirect($action) {
        header("Location: /xampp/NovaCareCRM/public/index.php?action={$action}");
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
    
    /**
     * Listar clientes
     */
    public function index() {
        $tipo = $_GET['tipo'] ?? null;
        $clientes = $this->clienteModel->getAll($tipo, 1);
        $stats = $this->clienteModel->getStats();
        
        $this->render('clientes/index', [
            'title' => 'Clientes',
            'clientes' => $clientes,
            'stats' => $stats,
            'tipoFiltro' => $tipo
        ]);
    }
    
    /**
     * Mostrar formulario de creación
     */
    public function create() {
        $this->render('clientes/create', ['title' => 'Nuevo Cliente']);
    }
    
    /**
     * Guardar cliente
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('clientes');
        }
        
        $result = $this->clienteModel->create($_POST);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Cliente creado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al crear el cliente';
        }
        
        $this->redirect('clientes');
    }
    
    /**
     * Mostrar detalle de cliente
     */
    public function show() {
        $id = $_GET['id'] ?? 0;
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $_SESSION['flash_error'] = 'Cliente no encontrado';
            $this->redirect('clientes');
        }
        
        $this->render('clientes/show', [
            'title' => 'Detalle del Cliente',
            'cliente' => $cliente
        ]);
    }
    
    /**
     * Mostrar formulario de edición
     */
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $cliente = $this->clienteModel->getById($id);
        
        if (!$cliente) {
            $_SESSION['flash_error'] = 'Cliente no encontrado';
            $this->redirect('clientes');
        }
        
        $this->render('clientes/edit', [
            'title' => 'Editar Cliente',
            'cliente' => $cliente
        ]);
    }
    
    /**
     * Actualizar cliente
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('clientes');
        }
        
        $id = $_POST['id'] ?? 0;
        $result = $this->clienteModel->update($id, $_POST);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Cliente actualizado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar el cliente';
        }
        
        $this->redirect('clientes');
    }
    
    /**
     * Eliminar cliente
     */
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $result = $this->clienteModel->delete($id);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Cliente eliminado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar el cliente';
        }
        
        $this->redirect('clientes');
    }
    
    /**
     * Buscar clientes (AJAX)
     */
    public function search() {
        $termino = $_GET['term'] ?? '';
        $tipo = $_GET['tipo'] ?? null;
        
        $clientes = $this->clienteModel->search($termino, $tipo);
        
        header('Content-Type: application/json');
        echo json_encode($clientes);
        exit;
    }
}