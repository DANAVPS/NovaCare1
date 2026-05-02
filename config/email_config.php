<?php
// config/email_config.php

// Configuración de correo para PHPMailer con tu dominio
define('MAIL_HOST', 'mail.deltadevsystems.com');
define('MAIL_PORT', 465);
define('MAIL_USERNAME', 'soporte@deltadevsystems.com');
define('MAIL_PASSWORD', 'Soportedelta2025*');
define('MAIL_ENCRYPTION', 'ssl');
define('MAIL_FROM_ADDRESS', 'soporte@deltadevsystems.com');
define('MAIL_FROM_NAME', 'NovaCare CRM');

// Configuración de notificaciones
define('MAIL_NOTIFY_ADMIN', true);
define('MAIL_ADMIN_EMAIL', 'soporte@deltadevsystems.com');

// URLs del sistema
define('APP_URL', 'http://localhost/NovaCareCRM');
?>