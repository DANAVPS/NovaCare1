<?php
// public/index.php - Versión completa con todos los módulos

// Habilitar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración
require_once __DIR__ . '/../config/config.php';

// Cargar controladores
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/ClienteController.php';
require_once __DIR__ . '/../app/Controllers/ProductoController.php';
require_once __DIR__ . '/../app/Controllers/OrdenMedicaController.php';
require_once __DIR__ . '/../app/Controllers/AutorizacionController.php';

// Obtener el parámetro 'action' de la URL
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
$subaction = isset($_GET['subaction']) ? $_GET['subaction'] : null;
$ajax = isset($_GET['ajax']) ? $_GET['ajax'] : null;

// Debug: ver qué acción estamos procesando
// echo "<!-- Debug: Action = " . $action . " -->\n";

// Enrutamiento principal
switch ($action) {
    // =============================================
    // AUTENTICACIÓN
    // =============================================
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->login();
        } else {
            $controller = new AuthController();
            $controller->showLogin();
        }
        break;
    
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    
    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;
    
    case 'forgot-password':
        $controller = new AuthController();
        $controller->showForgotPassword();
        break;

    case 'forgot-password-submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->forgotPassword();
        } else {
            $controller = new AuthController();
            $controller->showForgotPassword();
        }
        break;

    case 'reset-password':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->resetPassword();
        } else {
            $controller = new AuthController();
            $controller->showResetPassword();
        }
        break;

    // =============================================
    // CLIENTES
    // =============================================
    case 'clientes':
        $controller = new ClienteController();
        if ($subaction === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($subaction === 'create') {
            $controller->create();
        } elseif ($subaction === 'edit') {
            $controller->edit();
        } elseif ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($subaction === 'delete') {
            $controller->delete();
        } elseif ($subaction === 'show') {
            $controller->show();
        } elseif ($ajax === 'search') {
            $controller->search();
        } else {
            $controller->index();
        }
        break;

    // =============================================
    // PRODUCTOS
    // =============================================
    case 'productos':
        $controller = new ProductoController();
        if ($subaction === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($subaction === 'create') {
            $controller->create();
        } elseif ($subaction === 'edit') {
            $controller->edit();
        } elseif ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($subaction === 'delete') {
            $controller->delete();
        } elseif ($subaction === 'show') {
            $controller->show();
        } elseif ($ajax === 'search') {
            $controller->search();
        } else {
            $controller->index();
        }
        break;

    // =============================================
    // ÓRDENES MÉDICAS
    // =============================================
    case 'ordenes':
        $controller = new OrdenMedicaController();
        if ($subaction === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } elseif ($subaction === 'create') {
            $controller->create();
        } elseif ($subaction === 'edit') {
            $controller->edit();
        } elseif ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->update();
        } elseif ($subaction === 'delete') {
            $controller->delete();
        } elseif ($subaction === 'show') {
            $controller->show();
        } elseif ($subaction === 'anular') {
            $controller->anular();
        } else {
            $controller->index();
        }
        break;

    // =============================================
    // AUTORIZACIONES
    // =============================================
    case 'autorizaciones':
        $controller = new AutorizacionController();
        if ($subaction === 'aprobar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->aprobar();
        } elseif ($subaction === 'create') {
            $controller->create();
        } elseif ($subaction === 'show') {
            $controller->show();
        } else {
            $controller->index();
        }
        break;

    // =============================================
    // DEFAULT - Mostrar login
    // =============================================
    default:
        $controller = new AuthController();
        $controller->showLogin();
        break;
}
?>