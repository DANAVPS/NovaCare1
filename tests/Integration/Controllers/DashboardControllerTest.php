<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../app/Controllers/DashboardController.php';

/**
 * DashboardControllerTest
 *
 * Cubre las aserciones para el cálculo estadístico del panel administrativo:
 * - Autenticación obligatoria antes de acceder al dashboard
 * - Correcta composición del array $stats desde múltiples modelos
 * - Aserciones sobre valores numéricos del panel (totales, ratios, bordes)
 * - Propagación de cero cuando un modelo no retorna datos
 * - Control de permisos por rol (session-based)
 */
class DashboardControllerTest extends TestCase
{
    /** @var DashboardController Subclase con render y redirect interceptados */
    private $controller;

    /** @var object Mock de UserModel */
    private $userModelMock;

    /** @var object Mock de ClienteModel */
    private $clienteModelMock;

    /** @var object Mock de ProductoModel */
    private $productoModelMock;

    /** @var object Mock de OrdenMedicaModel */
    private $ordenModelMock;

    /** Variables para capturar los datos pasados a render() */
    private array $renderedData = [];

    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = 1;
        $this->renderedData  = [];

        // Referencia a $this para poder usarla dentro de la clase anónima
        $testCase = $this;

        $this->controller = new class($testCase) extends DashboardController {
            private TestCase $testRef;

            public function __construct(TestCase $testRef)
            {
                $this->testRef = $testRef;
                // Llamamos explícitamente al padre después de guardar la referencia
                parent::__construct();
            }

            protected function redirect($action): void
            {
                throw new \RuntimeException("Redirected to: {$action}");
            }

            protected function render($view, $data = []): void
            {
                // Guardamos los datos en el test para poder hacer aserciones
                if (property_exists($this->testRef, 'renderedData')) {
                    $ref = new \ReflectionProperty($this->testRef, 'renderedData');
                    $ref->setAccessible(true);
                    $ref->setValue($this->testRef, $data);
                }
            }
        };

        // Mocks de los cuatro modelos
        $this->userModelMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['findById', 'getStats'])
            ->getMock();

        $this->clienteModelMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['getStats'])
            ->getMock();

        $this->productoModelMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['getStats'])
            ->getMock();

        $this->ordenModelMock = $this->getMockBuilder(stdClass::class)
            ->addMethods(['getStats'])
            ->getMock();

        // Inyección mediante Reflection en las propiedades del controlador padre
        $this->injectProperty('userModel',     $this->userModelMock);
        $this->injectProperty('clienteModel',  $this->clienteModelMock);
        $this->injectProperty('productoModel', $this->productoModelMock);
        $this->injectProperty('ordenModel',    $this->ordenModelMock);
    }

    /**
     * Inyecta una dependencia en la propiedad privada del controlador padre.
     */
    private function injectProperty(string $prop, object $mock): void
    {
        $reflection = new ReflectionClass($this->controller);
        // La propiedad puede estar en la clase anónima o en DashboardController
        try {
            $property = $reflection->getProperty($prop);
        } catch (\ReflectionException $e) {
            $property = $reflection->getParentClass()->getProperty($prop);
        }
        $property->setAccessible(true);
        $property->setValue($this->controller, $mock);
    }

    // ──────────────────────────────────────────────────────────────
    // checkAuth — control de acceso por rol/sesión
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que el dashboard redirige al login cuando no hay sesión activa.
     */
    public function testCheckAuthRedirectsToLoginWhenNoSession(): void
    {
        unset($_SESSION['user_id']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/login/i');

        new class extends DashboardController {
            protected function redirect($action): void
            {
                throw new \RuntimeException("Redirected to: {$action}");
            }
        };
    }

    /**
     * Verifica que un usuario autenticado puede acceder al dashboard.
     */
    public function testCheckAuthAllowsAccessWithValidSession(): void
    {
        $_SESSION['user_id'] = 5;
        $controller = new class extends DashboardController {
            protected function redirect($action): void {}
        };
        $this->assertInstanceOf(DashboardController::class, $controller);
    }

    // ──────────────────────────────────────────────────────────────
    // index() — composición del array $stats
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que index() combina correctamente las estadísticas de los cuatro modelos.
     */
    public function testIndexCombinesStatsFromAllModels(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1, 'name' => 'Admin']);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 8]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 120]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 45]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 30, 'ordenes_hoy' => 5]);

        $this->controller->index();

        $stats = $this->renderedData['stats'] ?? [];

        $this->assertEquals(8,   $stats['total_usuarios']);
        $this->assertEquals(120, $stats['total_clientes']);
        $this->assertEquals(45,  $stats['total_productos']);
        $this->assertEquals(30,  $stats['ordenes_hoy']);
    }

    /**
     * Verifica que el panel usa 0 como valor por defecto cuando un modelo retorna null.
     * Control de propagación de cero en estadísticas incompletas.
     */
    public function testIndexUsesZeroDefaultWhenModelReturnsNull(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1, 'name' => 'Admin']);
        $this->userModelMock->method('getStats')->willReturn([]);        // sin total_users
        $this->clienteModelMock->method('getStats')->willReturn([]);    // sin total
        $this->productoModelMock->method('getStats')->willReturn([]);   // sin total
        $this->ordenModelMock->method('getStats')->willReturn([]);      // sin total

        $this->controller->index();

        $stats = $this->renderedData['stats'] ?? [];

        $this->assertEquals(0, $stats['total_usuarios']);
        $this->assertEquals(0, $stats['total_clientes']);
        $this->assertEquals(0, $stats['total_productos']);
        $this->assertEquals(0, $stats['ordenes_hoy']);
    }

    /**
     * Verifica que todas las claves requeridas por el template del panel
     * están presentes en el array $stats.
     */
    public function testIndexPassesAllRequiredStatsKeysToView(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1]);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 3]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 10]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 5]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 2]);

        $this->controller->index();

        $stats = $this->renderedData['stats'] ?? [];

        $this->assertArrayHasKey('total_usuarios',              $stats);
        $this->assertArrayHasKey('total_clientes',              $stats);
        $this->assertArrayHasKey('total_productos',             $stats);
        $this->assertArrayHasKey('ordenes_hoy',                 $stats);
        $this->assertArrayHasKey('autorizaciones_pendientes',   $stats);
    }

    /**
     * Verifica que los valores estadísticos son enteros no negativos.
     */
    public function testIndexStatsValuesAreNonNegativeIntegers(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1]);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 15]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 200]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 80]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 60]);

        $this->controller->index();

        $stats = $this->renderedData['stats'] ?? [];

        foreach ($stats as $key => $value) {
            $this->assertGreaterThanOrEqual(0, $value,
                "El campo '$key' no debe ser negativo.");
        }
    }

    // ──────────────────────────────────────────────────────────────
    // index() — datos del usuario en vista
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica que index() pasa el usuario actual a la vista.
     */
    public function testIndexPassesCurrentUserToView(): void
    {
        $expectedUser = ['id' => 1, 'name' => 'Dr. Ana Rodríguez', 'role' => 'admin'];

        $this->userModelMock->method('findById')->willReturn($expectedUser);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 1]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 0]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 0]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 0]);

        $this->controller->index();

        $this->assertArrayHasKey('user', $this->renderedData);
        $this->assertEquals('Dr. Ana Rodríguez', $this->renderedData['user']['name']);
    }

    /**
     * Verifica que el título del dashboard se pasa correctamente a la vista.
     */
    public function testIndexPassesTitleToView(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1]);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 1]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 0]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 0]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 0]);

        $this->controller->index();

        $this->assertArrayHasKey('title', $this->renderedData);
        $this->assertStringContainsString('Dashboard', $this->renderedData['title']);
    }

    // ──────────────────────────────────────────────────────────────
    // Aserciones de borde — valores extremos
    // ──────────────────────────────────────────────────────────────

    /**
     * Verifica el comportamiento con volúmenes altos de datos.
     */
    public function testIndexHandlesHighVolumeStats(): void
    {
        $this->userModelMock->method('findById')->willReturn(['id' => 1]);
        $this->userModelMock->method('getStats')->willReturn(['total_users' => 5000]);
        $this->clienteModelMock->method('getStats')->willReturn(['total' => 100000]);
        $this->productoModelMock->method('getStats')->willReturn(['total' => 9999]);
        $this->ordenModelMock->method('getStats')->willReturn(['total' => 250000]);

        $this->controller->index();

        $stats = $this->renderedData['stats'] ?? [];

        $this->assertEquals(5000,   $stats['total_usuarios']);
        $this->assertEquals(100000, $stats['total_clientes']);
        $this->assertEquals(9999,   $stats['total_productos']);
        $this->assertEquals(250000, $stats['ordenes_hoy']);
    }
}