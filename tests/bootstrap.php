<?php
/**
 * Bootstrap file para PHPUnit
 * Configura el autoloader y prepara el entorno de pruebas
 */

// Definir constantes de la aplicación
define('BASE_PATH', __DIR__ . '/..');
define('APP_PATH', BASE_PATH . '/app');
define('CONFIG_PATH', BASE_PATH . '/config');

// Autoloader de Composer (¡Ahora que ya agregaste el autoload en el json, este hace magia!)
$autoloadFile = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
}

// Autoloader manual de respaldo para las clases de la aplicación
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) === 0) {
        $relativeClass = substr($class, strlen($prefix));
        $file = APP_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Configurar la zona horaria predeterminada
date_default_timezone_set('UTC');

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'novacare_db');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');