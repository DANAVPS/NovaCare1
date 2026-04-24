<?php
// app/core/Mailer.php

// Incluir PHPMailer desde la raíz del proyecto
require_once __DIR__ . '/../../PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config/email_config.php';

class Mailer {
    private $mail;
    private static $instance = null;
    
    private function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setup();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function setup() {
        try {
            // Configuración SMTP
            $this->mail->isSMTP();
            $this->mail->Host = MAIL_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = MAIL_USERNAME;
            $this->mail->Password = MAIL_PASSWORD;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = MAIL_PORT;
            
            // Configuración general
            $this->mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
            // Deshabilitar verificación SSL para entornos locales (opcional)
            $this->mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            
        } catch (Exception $e) {
            error_log("Error al configurar Mailer: " . $e->getMessage());
        }
    }
    
    /**
     * Enviar correo a un destinatario
     */
    public function send($to, $subject, $body, $altBody = null) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?? strip_tags($body);
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error al enviar correo a $to: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Enviar correo a múltiples destinatarios
     */
    public function sendToMany($recipients, $subject, $body, $altBody = null) {
        $results = [];
        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->send($recipient, $subject, $body, $altBody);
        }
        return $results;
    }
    
    // =============================================
    // PLANTILLAS DE CORREO
    // =============================================
    
    /**
     * Correo de creación de orden médica
     */
    public function sendOrdenCreada($destinatario, $orden, $paciente, $medico = null, $productos = []) {
        $subject = "Nueva Orden Médica - NovaCare CRM";
        
        $body = $this->getTemplateHeader("Nueva Orden Médica Generada");
        
        // Lista de productos
        $productosHtml = "";
        if (!empty($productos)) {
            $productosHtml = "<h4 style='margin: 15px 0 10px 0;'>📦 Productos/Servicios:</h4>
                            <table style='width: 100%; border-collapse: collapse;'>
                                <thead>
                                    <tr style='background: #f0f0f0;'>
                                        <th style='padding: 8px; text-align: left;'>Producto</th>
                                        <th style='padding: 8px; text-align: center;'>Cantidad</th>
                                        <th style='padding: 8px; text-align: right;'>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>";
            foreach ($productos as $prod) {
                $productosHtml .= "
                                    <tr>
                                        <td style='padding: 8px; border-bottom: 1px solid #eee;'>" . htmlspecialchars($prod['producto_nombre']) . "</td>
                                        <td style='padding: 8px; text-align: center; border-bottom: 1px solid #eee;'>" . $prod['cantidad'] . "</td>
                                        <td style='padding: 8px; text-align: right; border-bottom: 1px solid #eee;'>$" . number_format($prod['subtotal'], 0, ',', '.') . "</td>
                                    </tr>";
            }
            $productosHtml .= "
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan='2' style='padding: 10px; text-align: right; font-weight: bold;'>Total:</td>
                                        <td style='padding: 10px; text-align: right; font-weight: bold; color: #f51b1c;'>$" . number_format($orden['total_valor'], 0, ',', '.') . "</td>
                                    </tr>
                                </tfoot>
                            </table>";
        }
        
        $body .= "
            <div style='padding: 20px;'>
                <p>Estimado(a) <strong>" . htmlspecialchars($destinatario['nombre'] ?? 'Usuario') . "</strong>,</p>
                <p>Se ha generado una nueva orden médica en el sistema NovaCare CRM.</p>
                
                <div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                    <h3 style='color: #f51b1c; margin: 0 0 10px 0;'>📋 Detalles de la Orden</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 5px 0;'><strong>N° Orden:</strong></td><td>" . htmlspecialchars($orden['numero_orden']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Paciente:</strong></td><td>" . htmlspecialchars($paciente['nombre'] . ' ' . ($paciente['apellido'] ?? '')) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Identificación:</strong></td><td>" . htmlspecialchars($paciente['identificacion']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Médico:</strong></td><td>" . htmlspecialchars($medico['nombre'] ?? 'No asignado') . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Fecha:</strong></td><td>" . date('d/m/Y', strtotime($orden['fecha_orden'])) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Prioridad:</strong></td><td>" . ucfirst($orden['prioridad']) . "</td></tr>
                    </table>
                </div>
                
                {$productosHtml}
                
                <div style='text-align: center; margin: 25px 0;'>
                    <a href='" . APP_URL . "/public/index.php?action=ordenes&subaction=show&id=" . $orden['id'] . "' 
                       style='background-color: #f51b1c; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Ver Orden Médica
                    </a>
                </div>
            </div>
        ";
        $body .= $this->getTemplateFooter();
        
        return $this->send($destinatario['email'], $subject, $body);
    }
    
    /**
     * Correo de autorización aprobada
     */
    public function sendAutorizacionAprobada($destinatario, $autorizacion, $paciente, $producto, $orden) {
        $subject = "✅ Autorización Médica APROBADA - NovaCare CRM";
        
        $body = $this->getTemplateHeader("Autorización Médica Aprobada", "#10b981");
        $body .= "
            <div style='padding: 20px;'>
                <p>Estimado(a) <strong>" . htmlspecialchars($destinatario['nombre'] ?? 'Usuario') . "</strong>,</p>
                <p>Nos complace informarle que la autorización médica ha sido <strong style='color: #10b981;'>APROBADA</strong>.</p>
                
                <div style='background: #f0fdf4; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #10b981;'>
                    <h3 style='color: #10b981; margin: 0 0 10px 0;'>✅ Detalles de la Autorización</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 5px 0;'><strong>N° Autorización:</strong></td><td>" . htmlspecialchars($autorizacion['numero_autorizacion']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>N° Orden:</strong></td><td>" . htmlspecialchars($orden['numero_orden']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Paciente:</strong></td><td>" . htmlspecialchars($paciente['nombre'] . ' ' . ($paciente['apellido'] ?? '')) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Producto:</strong></td><td>" . htmlspecialchars($producto['nombre']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Cantidad Autorizada:</strong></td><td>" . $autorizacion['cantidad_aprobada'] . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Fecha Autorización:</strong></td><td>" . date('d/m/Y H:i', strtotime($autorizacion['fecha_autorizacion'])) . "</td></tr>
                    </table>
                </div>
                
                <p>La orden médica ya puede ser procesada con esta autorización.</p>
                
                <div style='text-align: center; margin: 25px 0;'>
                    <a href='" . APP_URL . "/public/index.php?action=autorizaciones&subaction=show&id=" . $autorizacion['id'] . "' 
                       style='background-color: #10b981; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Ver Autorización
                    </a>
                </div>
            </div>
        ";
        $body .= $this->getTemplateFooter();
        
        return $this->send($destinatario['email'], $subject, $body);
    }
    
    /**
     * Correo de autorización rechazada
     */
    public function sendAutorizacionRechazada($destinatario, $autorizacion, $paciente, $producto, $orden) {
        $subject = "❌ Autorización Médica RECHAZADA - NovaCare CRM";
        
        $body = $this->getTemplateHeader("Autorización Médica Rechazada", "#dc2626");
        $body .= "
            <div style='padding: 20px;'>
                <p>Estimado(a) <strong>" . htmlspecialchars($destinatario['nombre'] ?? 'Usuario') . "</strong>,</p>
                <p>Le informamos que la autorización médica ha sido <strong style='color: #dc2626;'>RECHAZADA</strong>.</p>
                
                <div style='background: #fef2f2; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #dc2626;'>
                    <h3 style='color: #dc2626; margin: 0 0 10px 0;'>❌ Detalles de la Autorización</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr><td style='padding: 5px 0;'><strong>N° Autorización:</strong></td><td>" . htmlspecialchars($autorizacion['numero_autorizacion']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>N° Orden:</strong></td><td>" . htmlspecialchars($orden['numero_orden']) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Paciente:</strong></td><td>" . htmlspecialchars($paciente['nombre'] . ' ' . ($paciente['apellido'] ?? '')) . "</td></tr>
                        <tr><td style='padding: 5px 0;'><strong>Producto:</strong></td><td>" . htmlspecialchars($producto['nombre']) . "</td></tr>
                    </table>
                </div>
                
                <div style='background: #fff5f5; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                    <h4 style='color: #dc2626; margin: 0 0 10px 0;'>Motivo del Rechazo:</h4>
                    <p>" . nl2br(htmlspecialchars($autorizacion['motivo_rechazo'] ?? 'No especificado')) . "</p>
                </div>
                
                <p>Por favor, contacte a su médico tratante para más información.</p>
                
                <div style='text-align: center; margin: 25px 0;'>
                    <a href='" . APP_URL . "/public/index.php?action=autorizaciones&subaction=show&id=" . $autorizacion['id'] . "' 
                       style='background-color: #dc2626; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        Ver Detalles
                    </a>
                </div>
            </div>
        ";
        $body .= $this->getTemplateFooter();
        
        return $this->send($destinatario['email'], $subject, $body);
    }
    
    /**
     * Template Header
     */
    private function getTemplateHeader($title, $color = "#f51b1c") {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$title}</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: \"Gabarito\", sans-serif; background-color: #f4f4f4;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>
                <div style='background-color: {$color}; padding: 25px; text-align: center;'>
                    <div style='display: inline-block; background-color: white; width: 50px; height: 50px; border-radius: 10px; text-align: center; line-height: 50px; margin-bottom: 10px;'>
                        <span style='color: {$color}; font-size: 28px; font-weight: bold;'>NC</span>
                    </div>
                    <h1 style='color: white; margin: 0; font-size: 24px;'>NovaCare CRM</h1>
                    <p style='color: rgba(255,255,255,0.9); margin: 5px 0 0;'>Sistema de Gestión para Salud</p>
                </div>
                <div style='padding: 0;'>
        ";
    }
    
    /**
     * Template Footer
     */
    private function getTemplateFooter() {
        return "
                </div>
                <div style='background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #e9ecef;'>
                    <p style='margin: 0 0 10px; font-size: 12px; color: #6c757d;'>
                        Este es un mensaje automático del sistema NovaCare CRM.
                    </p>
                    <p style='margin: 0; font-size: 12px; color: #6c757d;'>
                        © 2025 NovaCare CRM - Todos los derechos reservados
                    </p>
                    <p style='margin: 10px 0 0; font-size: 11px; color: #adb5bd;'>
                        Si no deseas recibir estos correos, contacta al administrador.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
?>