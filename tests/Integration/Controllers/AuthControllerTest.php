<?php
namespace Tests\Integration\Controllers;

use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    private $userModelMock;

    protected function setUp(): void
    {
        require_once APP_PATH . '/Models/Database.php';
        require_once APP_PATH . '/Models/UserModel.php';
        require_once APP_PATH . '/Controllers/AuthController.php';

        // Crear un mock fresco del modelo para cada prueba
        $this->userModelMock = $this->createMock(\UserModel::class);

        $_SESSION = [];
        $_POST = [];
    }

    /**
     * Helper: Crea una instancia de AuthController con mocks de redirect y render,
     * y con el userModelMock inyectado.
     */
    private function createFreshAuthController()
    {
        $authController = $this->getMockBuilder(\AuthController::class)
            ->onlyMethods(['redirect', 'render'])
            ->disableOriginalConstructor()
            ->getMock();

        // Hacer que redirect lance una excepción para detener la ejecución (simula exit)
        $authController->expects($this->any())
            ->method('redirect')
            ->will($this->throwException(new \RuntimeException('Redirect called')));

        // Inyectar el mock del modelo
        $reflection = new \ReflectionClass(\AuthController::class);
        $property = $reflection->getProperty('userModel');
        $property->setAccessible(true);
        $property->setValue($authController, $this->userModelMock);

        return $authController;
    }

    /**
     * Test: Login exitoso con credenciales correctas
     */
    public function testSuccessfulLogin()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $authController = $this->createFreshAuthController();

        $email = 'user@example.com';
        $password = 'CorrectPassword123';
        $_POST = ['email' => $email, 'password' => $password];

        $userData = [
            'id' => 1,
            'name' => 'Test User',
            'email' => $email,
            'password' => 'hashed_password',
            'role' => 'doctor',
            'status' => 1
        ];

        $this->userModelMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($userData);

        $this->userModelMock->expects($this->once())
            ->method('verifyPassword')
            ->with($password, 'hashed_password')
            ->willReturn(true);

        $this->userModelMock->expects($this->once())
            ->method('updateLastLogin')
            ->with(1)
            ->willReturn(true);

        $authController->expects($this->once())
            ->method('redirect')
            ->with('dashboard');

        // Capturar la excepción lanzada por el mock de redirect
        try {
            $authController->login();
        } catch (\RuntimeException $e) {
            // Esperado, continuamos
        }

        // Verificar que las variables de sesión se hayan establecido antes del redirect
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('Test User', $_SESSION['user_name']);
        $this->assertEquals($email, $_SESSION['user_email']);
        $this->assertEquals('doctor', $_SESSION['user_role']);
        $this->assertArrayHasKey('login_time', $_SESSION);
    }

    /**
     * Test: Login fallido con email no encontrado
     */
    public function testLoginWithInvalidEmail()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $authController = $this->createFreshAuthController();

        $email = 'nonexistent@novacare.com';
        $_POST = ['email' => $email, 'password' => 'SomePassword123'];

        $this->userModelMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn(false);

        $authController->expects($this->once())
            ->method('redirect')
            ->with('login');

        try {
            $authController->login();
        } catch (\RuntimeException $e) {
            // Esperado
        }

        $this->assertStringContainsString('Credenciales incorrectas', $_SESSION['flash_error']);
    }

    /**
     * Test: Login fallido con contraseña incorrecta
     */
    public function testLoginWithInvalidPassword()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $authController = $this->createFreshAuthController();

        $email = 'user@example.com';
        $_POST = ['email' => $email, 'password' => 'WrongPassword123'];

        $userData = [
            'id' => 1,
            'name' => 'Test',
            'email' => $email,
            'password' => 'hashed_db_password',
            'role' => 'doctor',
            'status' => 1
        ];

        $this->userModelMock->expects($this->once())
            ->method('findByEmail')
            ->with($email)
            ->willReturn($userData);

        $this->userModelMock->expects($this->once())
            ->method('verifyPassword')
            ->with('WrongPassword123', 'hashed_db_password')
            ->willReturn(false);

        $authController->expects($this->once())
            ->method('redirect')
            ->with('login');

        try {
            $authController->login();
        } catch (\RuntimeException $e) {
            // Esperado
        }

        $this->assertStringContainsString('Credenciales incorrectas', $_SESSION['flash_error']);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_POST = [];
        unset($_SERVER['REQUEST_METHOD']);
    }
}