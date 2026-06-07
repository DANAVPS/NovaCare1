<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Models/AutorizacionModel.php';

/**
 * AutorizacionModelTest
 *
 * Cubre las transacciones lógicas del modelo de autorizaciones:
 * - Creación con estado inicial (pendiente / aprobada automática)
 * - Verificación de existencia (existeParaOrdenProducto)
 * - Flujo aprobar / rechazar con notificación
 * - Estadísticas de autorización del panel
 */
class AutorizacionModelTest extends TestCase
{
    private AutorizacionModel $model;
    private $dbMock;
    private $stmtMock;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;

        $this->model = new AutorizacionModel();

        $this->stmtMock = $this->createMock(PDOStatement::class);

        $this->dbMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['prepare', 'beginTransaction', 'commit', 'rollback'])
            ->getMock();

        $reflection = new ReflectionClass($this->model);
        $property   = $reflection->getProperty('db');
        $property->setAccessible(true);
        $property->setValue($this->model, $this->dbMock);
    }

    // ──────────────────────────────────────────────────────────────
    // getById
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que getById retorna la autorización correcta cuando existe.
     */
    public function testGetByIdReturnsAutorizacionWhenFound(): void
    {
        $expected = [
            'id'                => 1,
            'numero_autorizacion' => 'AUT-20260601-0001',
            'paciente_id'       => 10,
            'estado'            => 'pendiente',
        ];

        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetch')->willReturn($expected);
        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getById(1);

        $this->assertSame($expected, $result);
        $this->assertEquals('pendiente', $result['estado']);
    }

    /**
     * Verifica que getById retorna false cuando la autorización no existe.
     */
    public function testGetByIdReturnsFalseWhenNotFound(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $this->assertFalse($this->model->getById(9999));
    }

    // ──────────────────────────────────────────────────────────────
    // create — estado inicial según regla de negocio
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que create inserta estado 'pendiente' cuando no viene estado_inicial.
     */
    public function testCreateInsertsPendienteByDefault(): void
    {
        $data = [
            'orden_producto_id' => 5,
            'paciente_id'       => 10,
            'cantidad_aprobada' => 2,
        ];

        $capturedParams = [];

        // Primera llamada: generarNumeroAutorizacion (SELECT COUNT)
        $stmtCount = $this->createMock(PDOStatement::class);
        $stmtCount->method('execute')->willReturn(true);
        $stmtCount->method('fetch')->willReturn(['total' => 0]);

        // Segunda llamada: INSERT autorización
        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams = $params;
            return true;
        });

        $this->dbMock->expects($this->exactly(2))->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtCount, $stmtInsert);

        $this->model->create($data);

        $this->assertEquals('pendiente', $capturedParams[':estado']);
        $this->assertNull($capturedParams[':fecha_autorizacion']);
    }

    /**
     * Verifica que create inserta estado 'aprobada' cuando viene estado_inicial = 'aprobada'
     * (producto libre — aprobación automática).
     */
    public function testCreateInsertsAprobadaWhenEstadoInicialIsAprobada(): void
    {
        $data = [
            'orden_producto_id' => 5,
            'paciente_id'       => 10,
            'cantidad_aprobada' => 1,
            'estado_inicial'    => 'aprobada',
            'observaciones'     => 'Producto libre - aprobado automáticamente',
        ];

        $capturedParams = [];

        $stmtCount = $this->createMock(PDOStatement::class);
        $stmtCount->method('execute')->willReturn(true);
        $stmtCount->method('fetch')->willReturn(['total' => 3]);

        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams = $params;
            return true;
        });

        $this->dbMock->expects($this->exactly(2))->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtCount, $stmtInsert);

        $this->model->create($data);

        $this->assertEquals('aprobada', $capturedParams[':estado']);
        // fecha_autorizacion debe ser no nula cuando estado = aprobada
        $this->assertNotNull($capturedParams[':fecha_autorizacion']);
    }

    /**
     * Verifica que create retorna true cuando el execute del INSERT tiene éxito.
     */
    public function testCreateReturnsTrueOnSuccess(): void
    {
        $stmtCount = $this->createMock(PDOStatement::class);
        $stmtCount->method('execute')->willReturn(true);
        $stmtCount->method('fetch')->willReturn(['total' => 0]);

        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->method('execute')->willReturn(true);

        $this->dbMock->expects($this->exactly(2))->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtCount, $stmtInsert);

        $result = $this->model->create([
            'orden_producto_id' => 1,
            'paciente_id'       => 10,
            'cantidad_aprobada' => 1,
        ]);

        $this->assertTrue($result);
    }

    /**
     * Verifica que create retorna false cuando el INSERT falla.
     */
    public function testCreateReturnsFalseWhenInsertFails(): void
    {
        $stmtCount = $this->createMock(PDOStatement::class);
        $stmtCount->method('execute')->willReturn(true);
        $stmtCount->method('fetch')->willReturn(['total' => 0]);

        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->method('execute')->willReturn(false);

        $this->dbMock->expects($this->exactly(2))->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtCount, $stmtInsert);

        $result = $this->model->create([
            'orden_producto_id' => 1,
            'paciente_id'       => 10,
            'cantidad_aprobada' => 1,
        ]);

        $this->assertFalse($result);
    }

    // ──────────────────────────────────────────────────────────────
    // existeParaOrdenProducto
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que existeParaOrdenProducto retorna true cuando ya hay registro.
     */
    public function testExisteParaOrdenProductoReturnsTrueWhenFound(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchColumn')->willReturn('1');
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $this->assertTrue($this->model->existeParaOrdenProducto(15));
    }

    /**
     * Verifica que existeParaOrdenProducto retorna false cuando no hay registro.
     */
    public function testExisteParaOrdenProductoReturnsFalseWhenNotFound(): void
    {
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchColumn')->willReturn('0');
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $this->assertFalse($this->model->existeParaOrdenProducto(99));
    }

    // ──────────────────────────────────────────────────────────────
    // aprobar
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que aprobar() actualiza el estado a 'aprobada' correctamente.
     */
    public function testAprobarUpdatesEstadoToAprobada(): void
    {
        $capturedParams = [];

        $this->stmtMock->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams = $params;
            return true;
        });
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->aprobar(3, 'aprobada', null);

        $this->assertTrue($result);
        $this->assertEquals('aprobada', $capturedParams[':estado']);
        $this->assertEquals(3, $capturedParams[':id']);
    }

    /**
     * Verifica que aprobar() actualiza el estado a 'rechazada' con motivo de rechazo.
     */
    public function testAprobarUpdatesEstadoToRechazadaWithMotivo(): void
    {
        $capturedParams = [];

        $this->stmtMock->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams = $params;
            return true;
        });
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->aprobar(7, 'rechazada', 'Fuera de plan');

        $this->assertTrue($result);
        $this->assertEquals('rechazada', $capturedParams[':estado']);
        $this->assertEquals('Fuera de plan', $capturedParams[':motivo']);
    }

    /**
     * Verifica que aprobar() retorna false cuando la DB falla.
     */
    public function testAprobarReturnsFalseOnDbFailure(): void
    {
        $this->stmtMock->method('execute')->willReturn(false);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $this->assertFalse($this->model->aprobar(1, 'aprobada'));
    }

    // ──────────────────────────────────────────────────────────────
    // getStats — aserciones panel administrativo
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que getStats devuelve las claves requeridas por el dashboard.
     */
    public function testGetStatsReturnsAllRequiredKeys(): void
    {
        $statsRow = [
            'total'      => 200,
            'pendientes' => 80,
            'aprobadas'  => 100,
            'rechazadas' => 20,
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($statsRow);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getStats();

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('pendientes', $result);
        $this->assertArrayHasKey('aprobadas', $result);
        $this->assertArrayHasKey('rechazadas', $result);
    }

    /**
     * Verifica la coherencia numérica de getStats:
     * pendientes + aprobadas + rechazadas debe ser <= total.
     */
    public function testGetStatsNumericalCoherence(): void
    {
        $statsRow = [
            'total'      => 200,
            'pendientes' => 80,
            'aprobadas'  => 100,
            'rechazadas' => 20,
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($statsRow);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $stats = $this->model->getStats();

        $suma = $stats['pendientes'] + $stats['aprobadas'] + $stats['rechazadas'];
        $this->assertLessThanOrEqual($stats['total'], $suma,
            'La suma de estados no puede superar el total.');
        $this->assertGreaterThanOrEqual(0, $stats['pendientes']);
        $this->assertGreaterThanOrEqual(0, $stats['aprobadas']);
        $this->assertGreaterThanOrEqual(0, $stats['rechazadas']);
    }

    /**
     * Verifica que getStats retorna ceros cuando no hay autorizaciones.
     */
    public function testGetStatsReturnsZerosWhenEmpty(): void
    {
        $statsRow = [
            'total'      => 0,
            'pendientes' => 0,
            'aprobadas'  => 0,
            'rechazadas' => 0,
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($statsRow);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $stats = $this->model->getStats();

        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['pendientes'] + $stats['aprobadas'] + $stats['rechazadas']);
    }

    // ──────────────────────────────────────────────────────────────
    // getAll — filtrado por estado
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que getAll retorna un array cuando la consulta es exitosa.
     */
    public function testGetAllReturnsArrayOfAutorizaciones(): void
    {
        $rows = [
            ['id' => 1, 'estado' => 'pendiente'],
            ['id' => 2, 'estado' => 'pendiente'],
        ];

        $this->stmtMock->method('bindValue');
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchAll')->willReturn($rows);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getAll('pendiente');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}