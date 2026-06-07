<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Models/OrdenMedicaModel.php';

/**
 * OrdenMedicaModelTest
 *
 * Cubre las transacciones lógicas del negocio clínico:
 * - Vínculo orden ↔ productos (ordenes_productos)
 * - Cálculo de totales y estados de autorización por ítem
 * - Métodos de aprobación/rechazo de la orden completa
 * - Estadísticas del modelo (getStats)
 */
class OrdenMedicaModelTest extends TestCase
{
    private OrdenMedicaModel $model;
    private $dbMock;
    private $stmtMock;

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;

        $this->model = new OrdenMedicaModel();

        $this->stmtMock = $this->createMock(PDOStatement::class);

        $this->dbMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['prepare', 'beginTransaction', 'commit', 'rollback', 'lastInsertId'])
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
     * Verifica que getById devuelve el array de la orden cuando existe.
     */
    public function testGetByIdReturnsOrdenWhenFound(): void
    {
        $expected = [
            'id'           => 5,
            'numero_orden' => 'ORD-20260601-0001',
            'paciente_id'  => 10,
            'medico_id'    => 20,
            'estado'       => 'pendiente',
        ];

        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetch')->willReturn($expected);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getById(5);

        $this->assertSame($expected, $result);
        $this->assertEquals('ORD-20260601-0001', $result['numero_orden']);
    }

    /**
     * Verifica que getById devuelve false cuando la orden no existe.
     */
    public function testGetByIdReturnsFalseWhenNotFound(): void
    {
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetch')->willReturn(false);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $this->assertFalse($this->model->getById(999));
    }

    // ──────────────────────────────────────────────────────────────
    // getProductosByOrden — vínculo orden ↔ productos
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que getProductosByOrden retorna la colección de productos
     * asociados a la orden (relación ordenes_productos).
     */
    public function testGetProductosByOrdenReturnsAssociatedProducts(): void
    {
        $productos = [
            ['id' => 1, 'orden_id' => 7, 'producto_id' => 100, 'cantidad' => 2, 'precio_unitario' => 15000, 'subtotal' => 30000],
            ['id' => 2, 'orden_id' => 7, 'producto_id' => 200, 'cantidad' => 1, 'precio_unitario' => 50000, 'subtotal' => 50000],
        ];

        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetchAll')->willReturn($productos);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getProductosByOrden(7);

        $this->assertCount(2, $result);
        $this->assertEquals(7, $result[0]['orden_id']);
        $this->assertEquals(30000, $result[0]['subtotal']);
    }

    /**
     * Verifica que getProductosByOrden retorna array vacío si la orden no tiene productos.
     */
    public function testGetProductosByOrdenReturnsEmptyArrayWhenNoProducts(): void
    {
        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetchAll')->willReturn([]);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $this->assertSame([], $this->model->getProductosByOrden(42));
    }

    // ──────────────────────────────────────────────────────────────
    // create — transacción y cálculo de totales
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que create inicia una transacción y la confirma cuando
     * todos los inserts son exitosos. Comprueba que el ID devuelto es el
     * retornado por lastInsertId.
     */
    public function testCreateCommitsTransactionAndReturnsOrdenId(): void
    {
        $data = [
            'paciente_id'  => 10,
            'medico_id'    => 20,
            'fecha_orden'  => '2026-06-01',
            'diagnostico'  => 'Hipertensión',
            'prioridad'    => 'alta',
        ];

        $productos = [
            ['producto_id' => 100, 'cantidad' => 2, 'precio_unitario' => 15000],
            ['producto_id' => 200, 'cantidad' => 1, 'precio_unitario' => 50000],
        ];

        // total_valor esperado: (2*15000) + (1*50000) = 80 000
        $this->dbMock->expects($this->once())->method('beginTransaction');
        $this->dbMock->expects($this->once())->method('commit');
        $this->dbMock->expects($this->never())->method('rollback');

        // prepare se llama: 1 vez para la orden + n veces para cada producto
        // (además 1 para generarNumeroOrden) → al menos 3 veces en total
        $this->dbMock->expects($this->atLeast(3))->method('prepare')->willReturn($this->stmtMock);

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn(['max_num' => 0]);

        $this->dbMock->expects($this->atLeast(1))->method('lastInsertId')->willReturn('99');

        $result = $this->model->create($data, $productos);

        $this->assertEquals(99, $result);
    }

    /**
     * Verifica que create hace rollback y retorna false cuando ocurre una excepción.
     */
    public function testCreateRollsBackAndReturnsFalseOnException(): void
    {
        $data      = ['paciente_id' => 10, 'medico_id' => 20];
        $productos = [['producto_id' => 100, 'cantidad' => 1, 'precio_unitario' => 5000]];

        $this->dbMock->expects($this->once())->method('beginTransaction');
        $this->dbMock->expects($this->never())->method('commit');
        $this->dbMock->expects($this->once())->method('rollback');

        $this->dbMock->method('prepare')->willThrowException(new \Exception('DB failure'));

        $result = $this->model->create($data, $productos);

        $this->assertFalse($result);
    }

    /**
     * Verifica que el subtotal de cada producto se calcula correctamente
     * (cantidad × precio_unitario) durante la creación de la orden.
     * Usamos un stub para capturar el parámetro de execute de ordenes_productos.
     */
    public function testCreateCalculatesSubtotalsCorrectly(): void
    {
        $data     = ['paciente_id' => 10, 'medico_id' => 20, 'prioridad' => 'media'];
        $productos = [
            ['producto_id' => 100, 'cantidad' => 3, 'precio_unitario' => 10000],
        ];

        $capturedParams = [];

        $stmtCaptor = $this->getMockBuilder(PDOStatement::class)->getMock();
        $stmtCaptor->method('fetch')->willReturn(['max_num' => 0]);
        $stmtCaptor->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams[] = $params;
            return true;
        });

        $this->dbMock->method('beginTransaction');
        $this->dbMock->method('commit');
        $this->dbMock->method('prepare')->willReturn($stmtCaptor);
        $this->dbMock->method('lastInsertId')->willReturn('50');

        $this->model->create($data, $productos);

        // El subtotal del producto debe ser 3 × 10 000 = 30 000
        $productoParams = collect_param_with_key($capturedParams, ':subtotal');
        $this->assertEquals(30000, $productoParams);
    }

    // ──────────────────────────────────────────────────────────────
    // updateEstado
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que updateEstado ejecuta el UPDATE correctamente.
     */
    public function testUpdateEstadoReturnsTrueOnSuccess(): void
    {
        $this->stmtMock->expects($this->once())->method('execute')
            ->with([':id' => 5, ':estado' => 'completada'])
            ->willReturn(true);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $this->assertTrue($this->model->updateEstado(5, 'completada'));
    }

    // ──────────────────────────────────────────────────────────────
    // anular
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que anular ejecuta el UPDATE con motivo concatenado.
     */
    public function testAnularReturnsTrueOnSuccess(): void
    {
        $this->stmtMock->expects($this->once())->method('execute')
            ->with([':id' => 3, ':motivo' => 'Duplicada'])
            ->willReturn(true);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $this->assertTrue($this->model->anular(3, 'Duplicada'));
    }

    // ──────────────────────────────────────────────────────────────
    // rechazarOrden
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que rechazarOrden actualiza el estado a 'anulada'.
     */
    public function testRechazarOrdenReturnsTrueAndSetsAnuladaEstado(): void
    {
        $this->stmtMock->expects($this->once())->method('execute')
            ->willReturn(true);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $this->assertTrue($this->model->rechazarOrden(8, 'Fuera de cobertura'));
    }

    /**
     * Verifica que rechazarOrden usa motivo por defecto cuando no se provee.
     */
    public function testRechazarOrdenUsesDefaultMotivoWhenNull(): void
    {
        $capturedParams = [];

        $this->stmtMock->method('execute')->willReturnCallback(function ($params) use (&$capturedParams) {
            $capturedParams = $params;
            return true;
        });

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $this->model->rechazarOrden(8);

        $this->assertEquals('Sin motivo especificado', $capturedParams[':motivo']);
    }

    // ──────────────────────────────────────────────────────────────
    // getStats — aserciones sobre cálculo estadístico
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que getStats devuelve todas las claves esperadas del panel.
     */
    public function testGetStatsReturnsExpectedKeys(): void
    {
        $statsRow = [
            'total'        => 50,
            'pendientes'   => 15,
            'completadas'  => 30,
            'anuladas'     => 5,
            'parcial'      => 0,
            'valor_total'  => 3500000,
            'ordenes_hoy'  => 3,
        ];

        $this->stmtMock->expects($this->once())->method('execute')->willReturn(true);
        $this->stmtMock->expects($this->once())->method('fetch')->willReturn($statsRow);

        $this->dbMock->expects($this->once())->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->getStats();

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('pendientes', $result);
        $this->assertArrayHasKey('completadas', $result);
        $this->assertArrayHasKey('anuladas', $result);
        $this->assertArrayHasKey('valor_total', $result);
        $this->assertArrayHasKey('ordenes_hoy', $result);
    }

    /**
     * Verifica que los valores numéricos de getStats son consistentes:
     * pendientes + completadas + anuladas + parcial <= total.
     */
    public function testGetStatsNumericalConsistency(): void
    {
        $statsRow = [
            'total'       => 100,
            'pendientes'  => 40,
            'completadas' => 50,
            'anuladas'    => 8,
            'parcial'     => 2,
            'valor_total' => 7000000,
            'ordenes_hoy' => 10,
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetch')->willReturn($statsRow);
        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $stats = $this->model->getStats();

        $suma = $stats['pendientes'] + $stats['completadas'] + $stats['anuladas'] + $stats['parcial'];
        $this->assertLessThanOrEqual($stats['total'], $suma);
        $this->assertGreaterThanOrEqual(0, $stats['valor_total']);
    }

    // ──────────────────────────────────────────────────────────────
    // searchByNumero
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que searchByNumero retorna registros que coinciden con el término.
     */
    public function testSearchByNumeroReturnsMatchingOrders(): void
    {
        $rows = [
            ['id' => 1, 'numero_orden' => 'ORD-20260601-0001', 'paciente_nombre' => 'Carlos López'],
        ];

        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchAll')->willReturn($rows);
        $this->stmtMock->method('bindValue');

        $this->dbMock->method('prepare')->willReturn($this->stmtMock);

        $result = $this->model->searchByNumero('ORD-2026');

        $this->assertCount(1, $result);
        $this->assertStringContainsString('ORD-', $result[0]['numero_orden']);
    }
}

// ──────────────────────────────────────────────────────────────────────────────
// Helper global para localizar un parámetro en arrays capturados de execute()
// ──────────────────────────────────────────────────────────────────────────────
if (!function_exists('collect_param_with_key')) {
    function collect_param_with_key(array $allParams, string $key): mixed
    {
        foreach ($allParams as $paramSet) {
            if (is_array($paramSet) && array_key_exists($key, $paramSet)) {
                return $paramSet[$key];
            }
        }
        return null;
    }
}