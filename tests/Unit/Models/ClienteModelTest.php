<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Models/ClienteModel.php';

class ClienteModelTest extends TestCase
{
    private ClienteModel $model;
    private $dbMock;
    private $stmtMock;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = 1;

        $this->model = new ClienteModel();

        // Mock del PDOStatement
        $this->stmtMock = $this->createMock(PDOStatement::class);

        // Mock de la base de datos
        $this->dbMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['prepare'])
            ->getMock();

        // Reemplazar propiedad privada $db
        $reflection = new ReflectionClass($this->model);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->dbMock);
    }

    public function testCreateReturnsTrueWhenInsertIsSuccessful(): void
    {
        $data = [
            'tipo' => 'EPS',
            'identificacion' => '123456789',
            'nombre' => 'Juan Perez'
        ];

        $this->dbMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $result = $this->model->create($data);

        $this->assertTrue($result);
    }

    public function testCreateReturnsFalseWhenInsertFails(): void
    {
        $data = [
            'tipo' => 'EPS',
            'identificacion' => '123456789',
            'nombre' => 'Juan Perez'
        ];

        $this->dbMock
            ->expects($this->once())
            ->method('prepare')
            ->willReturn($this->stmtMock);

        $this->stmtMock
            ->expects($this->once())
            ->method('execute')
            ->willReturn(false);

        $result = $this->model->create($data);

        $this->assertFalse($result);
    }
}