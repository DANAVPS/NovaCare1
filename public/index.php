<?php
// public/index.php - Versión completa con todos los módulos

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';

require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';
require_once __DIR__ . '/../app/Controllers/ClienteController.php';
require_once __DIR__ . '/../app/Controllers/ProductoController.php';
require_once __DIR__ . '/../app/Controllers/OrdenMedicaController.php';
require_once __DIR__ . '/../app/Controllers/AutorizacionController.php';

$action    = $_GET['action']    ?? 'login';
$subaction = $_GET['subaction'] ?? null;
$ajax      = $_GET['ajax']      ?? null;

switch ($action) {

    // =============================================
    // AUTENTICACIÓN
    // =============================================
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'dashboard':
        (new DashboardController())->index();
        break;

    case 'forgot-password':
        (new AuthController())->showForgotPassword();
        break;

    case 'forgot-password-submit':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->forgotPassword();
        } else {
            $controller->showForgotPassword();
        }
        break;

    case 'reset-password':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->resetPassword();
        } else {
            $controller->showResetPassword();
        }
        break;

    // =============================================
    // CLIENTES
    // =============================================
    case 'clientes':
        $controller = new ClienteController();
        if      ($subaction === 'store'  && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->store();
        elseif  ($subaction === 'create')                                           $controller->create();
        elseif  ($subaction === 'edit')                                             $controller->edit();
        elseif  ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->update();
        elseif  ($subaction === 'delete')                                           $controller->delete();
        elseif  ($subaction === 'show')                                             $controller->show();
        elseif  ($ajax      === 'search')                                           $controller->search();
        else                                                                        $controller->index();
        break;

    // =============================================
    // PRODUCTOS
    // =============================================
    case 'productos':
        $controller = new ProductoController();
        if      ($subaction === 'store'  && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->store();
        elseif  ($subaction === 'create')                                           $controller->create();
        elseif  ($subaction === 'edit')                                             $controller->edit();
        elseif  ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->update();
        elseif  ($subaction === 'delete')                                           $controller->delete();
        elseif  ($subaction === 'show')                                             $controller->show();
        elseif  ($ajax      === 'search')                                           $controller->search();
        else                                                                        $controller->index();
        break;

    // =============================================
    // ÓRDENES MÉDICAS
    // =============================================
    // case 'ordenes':
    //     $controller = new OrdenMedicaController();
    //     if      ($subaction === 'store'  && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->store();
    //     elseif  ($subaction === 'create')                                           $controller->create();
    //     elseif  ($subaction === 'edit')                                             $controller->edit();
    //     elseif  ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->update();
    //     elseif  ($subaction === 'delete')                                           $controller->delete();
    //     elseif  ($subaction === 'show')                                             $controller->show();
    //     elseif  ($subaction === 'anular')                                           $controller->anular();
    //     else                                                                        $controller->index();
    //     break;

    // =============================================
    // AUTORIZACIONES
    // =============================================
    case 'autorizaciones':
    $controller = new AutorizacionController();

    if (($subaction === 'aprobar' || $subaction === 'rechazar') && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->aprobar();
    } elseif ($subaction === 'create') {
        $controller->create();
    } elseif ($subaction === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    } elseif ($subaction === 'show') {
        $controller->show();
    } else {
        $controller->index();
    }
    break;

case 'ordenes':
    $controller = new OrdenMedicaController();

    if      ($subaction === 'aprobarOrden'  && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->aprobarOrden();
    elseif  ($subaction === 'rechazarOrden' && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->rechazarOrden();
    elseif  ($subaction === 'store'  && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->store();
    elseif  ($subaction === 'create')                                           $controller->create();
    elseif  ($subaction === 'edit')                                             $controller->edit();
    elseif  ($subaction === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') $controller->update();
    elseif  ($subaction === 'delete')                                           $controller->delete();
    elseif  ($subaction === 'show')                                             $controller->show();
    elseif  ($subaction === 'anular')                                           $controller->anular();
    else                                                                        $controller->index();
    break;

    // =============================================
    // DEFAULT
    // =============================================
    default:
        (new AuthController())->showLogin();
        break;
}
?>