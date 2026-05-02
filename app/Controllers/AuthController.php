<?php
// app/Controllers/AuthController.php

require_once __DIR__ . '/../Models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    /**
     * Obtener la URL base - CORREGIDO con /
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host . '/NovaCareCRM';
    }
    
    /**
     * Redireccionar con action - CORREGIDO
     */
    private function redirect($action) {
        header("Location: /NovaCareCRM/public/index.php?action={$action}");
        exit;
    }
    
    /**
     * Mostrar página de login
     */
    public function showLogin() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        
        $this->render('auth/login', [
            'title' => 'Iniciar Sesión',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Procesar login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = 'El correo electrónico es obligatorio';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El formato del correo electrónico no es válido';
        }
        
        if (empty($password)) {
            $errors[] = 'La contraseña es obligatoria';
        }
        
        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            $this->redirect('login');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['flash_error'] = 'Credenciales incorrectas. Use admin@novacare.com / 123456';
            $this->redirect('login');
        }
        
        if ($user['status'] != 1) {
            $_SESSION['flash_error'] = 'Tu cuenta está desactivada';
            $this->redirect('login');
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
        
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['remember_token'] = $token;
            setcookie('remember_token', $token, time() + (86400 * 30), '/');
        }
        
        $this->userModel->updateLastLogin($user['id']);
        
        $this->redirect('dashboard');
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        $_SESSION = [];
        
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        session_destroy();
        $this->redirect('login');
    }
    
    /**
     * Mostrar formulario para solicitar restablecimiento de contraseña
     */
    public function showForgotPassword() {
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        
        $this->render('auth/forgot-password', [
            'title' => 'Restablecer Contraseña',
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Procesar solicitud de restablecimiento
     */
    public function forgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot-password');
        }
        
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $_SESSION['flash_error'] = 'Por favor ingresa tu correo electrónico';
            $this->redirect('forgot-password');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Formato de correo electrónico inválido';
            $this->redirect('forgot-password');
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            $_SESSION['flash_success'] = 'Si el correo existe en nuestro sistema, recibirás un enlace para restablecer tu contraseña.';
            $this->redirect('forgot-password');
        }
        
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $this->userModel->saveResetToken($user['id'], $token, $expires);
        
        $resetLink = "/NovaCareCRM/public/index.php?action=reset-password&token=" . $token;
        
        $_SESSION['flash_success'] = "Enlace de restablecimiento generado: <br><a href='{$resetLink}' style='color: #f51b1c;' target='_blank'>Click aquí para restablecer tu contraseña</a><br><small>(Este enlace expira en 1 hora)</small>";
        $this->redirect('forgot-password');
    }
    
    /**
     * Mostrar formulario para nueva contraseña
     */
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['flash_error'] = 'Token inválido';
            $this->redirect('forgot-password');
        }
        
        $user = $this->userModel->verifyResetToken($token);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'El enlace ha expirado o es inválido. Por favor, solicita un nuevo restablecimiento.';
            $this->redirect('forgot-password');
        }
        
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_success'] ?? null;
        
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);
        
        $this->render('auth/reset-password', [
            'title' => 'Nueva Contraseña',
            'token' => $token,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Procesar el cambio de contraseña
     */
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot-password');
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        if (empty($token)) {
            $errors[] = 'Token inválido';
        }
        
        if (empty($password)) {
            $errors[] = 'La nueva contraseña es obligatoria';
        } elseif (strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres';
        }
        
        if ($password !== $confirmPassword) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode('<br>', $errors);
            $this->redirect("reset-password&token={$token}");
        }
        
        $user = $this->userModel->verifyResetToken($token);
        
        if (!$user) {
            $_SESSION['flash_error'] = 'El enlace ha expirado o es inválido';
            $this->redirect('forgot-password');
        }
        
        $result = $this->userModel->updatePassword($user['id'], $password);
        
        if ($result) {
            $_SESSION['flash_success'] = '¡Contraseña actualizada exitosamente! Ahora puedes iniciar sesión con tu nueva contraseña.';
            $this->redirect('login');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar la contraseña. Por favor, intenta nuevamente.';
            $this->redirect("reset-password&token={$token}");
        }
    }
    
    /**
     * Renderizar vista
     */
    private function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
}