<?php
namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use PDO;
use PDOStatement;

class UserModelTest extends TestCase
{
    private $pdoMock;
    private $stmtMock;
    private $userModel;

    protected function setUp(): void
    {
        // Forzar la carga manual ya que las clases son globales
        require_once APP_PATH . '/Models/Database.php';
        require_once APP_PATH . '/Models/UserModel.php';

        // Mocks de PDO usando las clases importadas en el namespace
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);

        // Instanciar el modelo (anteponiendo \ porque está en el scope global)
        $this->userModel = new \UserModel();

        // Inyectar el PDO mockeado en la propiedad privada $db usando Reflection
        $reflection = new \ReflectionClass($this->userModel);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->userModel, $this->pdoMock);
    }

    /**
     * Test: Registro exitoso de usuario con datos válidos
     */
    public function testSuccessfulUserRegistration()
    {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'SecurePassword123',
            'role' => 'doctor'
        ];

        // Configurar el mock del statement para que execute() devuelva true
        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        // Configurar el mock de PDO para que prepare() devuelva el statement
        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->with($this->stringContains('INSERT INTO users'))
            ->willReturn($this->stmtMock);

        $result = $this->userModel->create($userData);

        $this->assertTrue($result);
    }

    /**
     * Test: Error al registrar usuario con email duplicado
     */
    public function testUserRegistrationWithDuplicateEmail()
    {
        $userData = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => 'SecurePassword123',
            'role' => 'doctor'
        ];

        // Simular que el execute falla (ej. por clave duplicada)
        $this->stmtMock->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $this->pdoMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $result = $this->userModel->create($userData);

        $this->assertFalse($result);
    }
}