<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Controllers/ClienteController.php';

class ClienteControllerTest extends TestCase
{
    private $controller;
    private $clienteModelMock;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = 1;

        // 1. Heredamos de ClienteController en caliente para anular el 'exit' de redirect()
        $this->controller = new class extends ClienteController {
            protected function redirect($action) {
                // Lanzamos una excepción en lugar de usar exit para que PHPUnit no muera
                throw new \RuntimeException("Redirected to: " . $action);
            }
        };

        // 2. Creamos el Mock para el modelo
        $this->clienteModelMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['create'])
            ->getMock();

        // 3. Inyectamos el mock mediante Reflection en nuestra subclase anónima
        $reflection = new ReflectionClass($this->controller);
        // Buscamos la propiedad en la clase padre (ClienteController)
        $property = $reflection->getParentClass()->getProperty('clienteModel');
        $property->setAccessible(true);
        $property->setValue($this->controller, $this->clienteModelMock);

        $_SERVER['REQUEST_METHOD'] = 'POST';
    }

    public function testStoreSuccessCreatesFlashMessage()
    {
        $_POST = [
            'tipo' => 'EPS',
            'identificacion' => '123456',
            'nombre' => 'Juan'
        ];

        $this->clienteModelMock
            ->expects($this->once())
            ->method('create')
            ->willReturn(true);

        try {
            $this->controller->store();
        } catch (\RuntimeException $e) {
            // Capturamos la excepción del redirect simulado para que el test continúe
            $this->assertStringContainsString('Redirected to: clientes', $e->getMessage());
        }

        $this->assertEquals(
            'Cliente creado exitosamente',
            $_SESSION['flash_success'] ?? null
        );
    }

    public function testStoreFailureCreatesFlashError()
    {
        $_POST = [
            'tipo' => 'EPS',
            'identificacion' => '123456',
            'nombre' => 'Juan'
        ];

        $this->clienteModelMock
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);

        try {
            $this->controller->store();
        } catch (\RuntimeException $e) {
            // Capturamos la excepción del redirect simulado para que el test continúe
            $this->assertStringContainsString('Redirected to: clientes', $e->getMessage());
        }

        $this->assertEquals(
            'Error al crear el cliente',
            $_SESSION['flash_error'] ?? null
        );
    }
}