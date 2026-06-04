<?php
// app/Controllers/AuthController.php

require_once __DIR__ . '/../Models/UserModel.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return $protocol . '://' . $host . '/NovaCareCRM';
    }
    
    protected function redirect($action) {
        header("Location: /NovaCareCRM/public/index.php?action={$action}");
        exit;
    }
    
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
    
    public function logout() {
        $_SESSION = [];
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        session_destroy();
        $this->redirect('login');
    }
    
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
        $_SESSION['flash_success'] = "Enlace de restablecimiento generado... ";
        $this->redirect('forgot-password');
    }
    
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            $_SESSION['flash_error'] = 'Token inválido';
            $this->redirect('forgot-password');
        }
        
        $user = $this->userModel->verifyResetToken($token);
        if (!$user) {
            $_SESSION['flash_error'] = 'El enlace ha expirado...';
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
    
    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot-password');
        }
        
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        if (empty($token)) $errors[] = 'Token inválido';
        if (empty($password)) $errors[] = 'La nueva contraseña es obligatoria';
        if ($password !== $confirmPassword) $errors[] = 'Las contraseñas no coinciden';
        
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
            $_SESSION['flash_success'] = '¡Contraseña actualizada exitosamente!';
            $this->redirect('login');
        } else {
            $_SESSION['flash_error'] = 'Error al actualizar...';
            $this->redirect("reset-password&token={$token}");
        }
    }
    
    protected function render($view, $data = []) {
        extract($data);
        require_once __DIR__ . "/../Views/{$view}.php";
    }
}