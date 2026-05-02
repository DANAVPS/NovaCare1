<?php
// app/Controllers/ProductoController.php

require_once __DIR__ . '/../Models/ProductoModel.php';

class ProductoController {
    private $productoModel;
    
    public function __construct() {
        $this->checkAuth();
        $this->productoModel = new ProductoModel();
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /NovaCareCRM/public/index.php?action=login');
            exit;
        }
    }
    
    private function redirect($action) {
        header("Location: /NovaCareCRM/public/index.php?action={$action}");
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
    
    public function index() {
        $productos = $this->productoModel->getAll();
        $stats = $this->productoModel->getStats();
        
        $this->render('productos/index', [
            'title' => 'Productos y Servicios',
            'productos' => $productos,
            'stats' => $stats
        ]);
    }
    
    public function create() {
        $this->render('productos/create', ['title' => 'Nuevo Producto']);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('productos');
        }
        
        $result = $this->productoModel->create($_POST);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Producto creado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al crear el producto';
        }
        
        $this->redirect('productos');
    }
    
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $producto = $this->productoModel->getById($id);
        
        if (!$producto) {
            $_SESSION['flash_error'] = 'Producto no encontrado';
            $this->redirect('productos');
        }
        
        $this->render('productos/edit', [
            'title' => 'Editar Producto',
            'producto' => $producto
        ]);
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('productos');
        }
        
        $id = $_POST['id'] ?? 0;
        $result = $this->productoModel->update($id, $_POST);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Producto actualizado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar el producto';
        }
        
        $this->redirect('productos');
    }
    
    public function delete() {
        $id = $_GET['id'] ?? 0;
        $result = $this->productoModel->delete($id);
        
        if ($result) {
            $_SESSION['flash_success'] = 'Producto eliminado exitosamente';
        } else {
            $_SESSION['flash_error'] = 'Error al eliminar el producto';
        }
        
        $this->redirect('productos');
    }
    
    public function show() {
        $id = $_GET['id'] ?? 0;
        $producto = $this->productoModel->getById($id);
        
        if (!$producto) {
            $_SESSION['flash_error'] = 'Producto no encontrado';
            $this->redirect('productos');
        }
        
        $this->render('productos/show', [
            'title' => 'Detalle del Producto',
            'producto' => $producto
        ]);
    }
    
    public function search() {
        $termino = $_GET['term'] ?? '';
        $productos = $this->productoModel->search($termino);
        
        header('Content-Type: application/json');
        echo json_encode($productos);
        exit;
    }
}