<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Models/ProductoModel.php';

class ProductoModelTest extends TestCase
{
    private ProductoModel $model;
    private $dbMock;
    private $stmtMock;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = 1;

        $this->model = new ProductoModel();

        // Mock del statement PDO
        $this->stmtMock = $this->createMock(PDOStatement::class);

        // Mock de la base de datos
        $this->dbMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['prepare'])
            ->getMock();

        // Reemplazar propiedad privada $db mediante Reflection
        $reflection = new ReflectionClass($this->model);
        $property = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->dbMock);
    }

    public function testCreateReturnsTrueWhenProductIsCreatedSuccessfully(): void
    {
        $data = [
            'codigo' => 'MED001',
            'nombre' => 'Acetaminofen',
            'tipo' => 'medicamento'
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

    public function testCreateReturnsFalseWhenProductCreationFails(): void
    {
        $data = [
            'codigo' => 'MED001',
            'nombre' => 'Acetaminofen',
            'tipo' => 'medicamento'
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