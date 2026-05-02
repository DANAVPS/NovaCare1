<?php
// config/config.php - Versión para XAMPP

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'novacare_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de la aplicación
define('APP_NAME', 'NovaCare CRM');
define('APP_URL', 'http://localhost/NovaCareCRM'); // Cambia NovaCareCRM por tu carpeta
define('APP_ENV', 'development');

// Configuración de sesión
define('SESSION_NAME', 'novacare_session');
define('SESSION_LIFETIME', 7200);

// Configuración de seguridad
define('HASH_COST', 10);

// Zona horaria
date_default_timezone_set('America/Bogota');

// Configuración de errores
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Función para obtener la URL base automáticamente
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = rtrim(dirname($scriptName), '/');
    
    // Para XAMPP, eliminar /public si está presente
    $basePath = str_replace('/public', '', $basePath);
    
    return $protocol . '://' . $host . $basePath;
}