<!-- app/Views/ordenes/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Detalle Orden'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
        .btn-primary { background-color: #f51b1c; transition: all 0.2s; }
        .btn-primary:hover { background-color: #d91617; }
        .sidebar-link:hover { background-color: #f51b1c; color: white; transform: translateX(5px); }
        .status-pendiente   { background-color: #fef3c7; color: #d97706; }
        .status-completada  { background-color: #d1fae5; color: #059669; }
        .status-anulada     { background-color: #fee2e2; color: #dc2626; }
        .status-parcial     { background-color: #dbeafe; color: #2563eb; }
        .status-rechazada   { background-color: #fee2e2; color: #dc2626; }
        .auth-aprobada  { background-color: #d1fae5; color: #059669; }
        .auth-rechazada { background-color: #fee2e2; color: #dc2626; }
        .auth-pendiente { background-color: #fef3c7; color: #d97706; }
        .auth-none      { background-color: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="bg-primary w-10 h-10 rounded-lg flex items-center justify-center shadow-md">
                        <span class="text-white font-bold text-xl">NC</span>
                    </div>
                    <div class="ml-3">
                        <span class="text-xl font-bold text-gray-800">NovaCare CRM</span>
                        <span class="text-xs text-primary ml-2 font-semibold">Healthcare</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                            <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <a href="/NovaCareCRM/public/index.php?action=dashboard"      class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition">Dashboard</a>
                <a href="/NovaCareCRM/public/index.php?action=clientes"       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition">Clientes</a>
                <a href="/NovaCareCRM/public/index.php?action=productos"      class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition">Productos</a>
                <a href="/NovaCareCRM/public/index.php?action=ordenes"        class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Órdenes</a>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
                    <?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                    <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detalle de Orden Médica</h1>
                    <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($orden['numero_orden']); ?></p>
                </div>
                <a href="/NovaCareCRM/public/index.php?action=ordenes"
                   class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    ← Volver
                </a>
            </div>

            <!-- Fila 1: Datos de la orden + Datos del paciente -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                <!-- Información de la Orden -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-primary p-4">
                        <h2 class="text-white text-lg font-bold">Información de la Orden</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-gray-500">N° Orden</p>
                                <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($orden['numero_orden']); ?></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Estado Autorización</p>
                                <?php
                                    $ea = $orden['estado_autorizacion'] ?? null;
                                    if ($ea === 'aprobada')  echo '<span class="px-2 py-1 text-xs rounded-full auth-aprobada">Aprobada</span>';
                                    elseif ($ea === 'rechazada') echo '<span class="px-2 py-1 text-xs rounded-full auth-rechazada">Rechazada</span>';
                                    elseif ($ea === 'pendiente') echo '<span class="px-2 py-1 text-xs rounded-full auth-pendiente">Pendiente</span>';
                                    elseif ($ea === 'parcial')   echo '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Parcial</span>';
                                    else echo '<span class="px-2 py-1 text-xs rounded-full auth-none">No requiere</span>';
                                ?>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Prioridad</p>
                                <span class="px-2 py-1 text-xs rounded-full
                                    <?php echo $orden['prioridad'] == 'urgente' ? 'bg-purple-100 text-purple-800' : ''; ?>
                                    <?php echo $orden['prioridad'] == 'alta'    ? 'bg-red-100 text-red-800'       : ''; ?>
                                    <?php echo $orden['prioridad'] == 'media'   ? 'bg-yellow-100 text-yellow-800' : ''; ?>
                                    <?php echo $orden['prioridad'] == 'baja'    ? 'bg-green-100 text-green-800'   : ''; ?>">
                                    <?php echo ucfirst($orden['prioridad']); ?>
                                </span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Fecha Orden</p>
                                <p class="text-gray-800"><?php echo date('d/m/Y', strtotime($orden['fecha_orden'])); ?></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Creado por</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['creado_por_nombre'] ?? 'Sistema'); ?></p>
                            </div>
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Diagnóstico</p>
                                <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($orden['diagnostico'] ?? 'No especificado')); ?></p>
                            </div>
                            <?php if (!empty($orden['observaciones'])): ?>
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Observaciones</p>
                                <p class="text-gray-800"><?php echo nl2br(htmlspecialchars($orden['observaciones'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Información del Paciente -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-green-600 p-4">
                        <h2 class="text-white text-lg font-bold">Información del Paciente</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-semibold text-gray-500">Nombre</p>
                                <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($orden['paciente_nombre']); ?></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Identificación</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_identificacion']); ?></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Teléfono</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_telefono'] ?? 'No registrado'); ?></p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-500">Email</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_email'] ?? 'No registrado'); ?></p>
                            </div>
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Médico tratante</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['medico_nombre'] ?? 'No asignado'); ?></p>
                            </div>
                            <?php if (!empty($orden['medico_telefono'])): ?>
                            <div class="col-span-2">
                                <p class="font-semibold text-gray-500">Teléfono médico</p>
                                <p class="text-gray-800"><?php echo htmlspecialchars($orden['medico_telefono']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="bg-blue-600 p-4 flex justify-between items-center">
                    <h2 class="text-white text-lg font-bold">Productos y Servicios</h2>
                    <span class="text-blue-100 text-sm"><?php echo count($productos); ?> ítem(s)</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant.</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cant. Aprobada</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Requiere Auth.</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado Auth.</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($productos as $prod): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($prod['producto_nombre']); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <?php echo htmlspecialchars($prod['producto_codigo']); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700">
                                    <?php echo $prod['cantidad']; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-center text-gray-700">
                                    <?php echo $prod['cantidad_autorizada'] ?? '—'; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-right text-gray-700">
                                    $<?php echo number_format($prod['precio_unitario'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-right font-semibold text-gray-800">
                                    $<?php echo number_format($prod['subtotal'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <?php if ($prod['requiere_autorizacion']): ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">Sí</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-500">No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-sm text-center">
                                    <?php
                                        $ea = $prod['estado_autorizacion'] ?? 'aprobada';
                                        if ($ea === 'aprobada')
                                            echo '<span class="px-2 py-1 text-xs rounded-full auth-aprobada">Aprobada</span>';
                                        elseif ($ea === 'rechazada')
                                            echo '<span class="px-2 py-1 text-xs rounded-full auth-rechazada">Rechazada</span>';
                                        elseif ($ea === 'pendiente')
                                            echo '<span class="px-2 py-1 text-xs rounded-full auth-pendiente">Pendiente</span>';
                                        else
                                            echo '<span class="px-2 py-1 text-xs rounded-full auth-none">—</span>';
                                    ?>
                                    <?php if (!empty($prod['numero_autorizacion'])): ?>
                                        <br>
                                        <span class="text-xs text-gray-400"><?php echo htmlspecialchars($prod['numero_autorizacion']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($prod['motivo_rechazo'])): ?>
                                        <br>
                                        <span class="text-xs text-red-500" title="<?php echo htmlspecialchars($prod['motivo_rechazo']); ?>">Ver motivo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-bold text-gray-700">Total:</td>
                                <td class="px-4 py-3 text-right text-lg font-bold text-primary">
                                    $<?php echo number_format($orden['total_valor'], 0, ',', '.'); ?>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Panel de Autorización (solo si la orden tiene productos que requieren autorización) -->
            <?php if (!is_null($orden['estado_autorizacion'])): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <?php
                    $ea = $orden['estado_autorizacion'];
                    $headerClass = 'bg-gray-500';
                    $headerLabel = 'Autorización';
                    if ($ea === 'pendiente')  { $headerClass = 'bg-yellow-500'; $headerLabel = 'Autorización Pendiente'; }
                    if ($ea === 'aprobada')   { $headerClass = 'bg-green-500';  $headerLabel = 'Orden Autorizada'; }
                    if ($ea === 'rechazada')  { $headerClass = 'bg-red-600';    $headerLabel = 'Autorización Rechazada'; }
                    if ($ea === 'parcial')    { $headerClass = 'bg-blue-500';   $headerLabel = 'Autorización Parcial'; }
                ?>
                <div class="<?php echo $headerClass; ?> p-4 flex justify-between items-center">
                    <h2 class="text-white text-lg font-bold"><?php echo $headerLabel; ?></h2>
                    <?php if ($ea === 'pendiente'): ?>
                        <a href="/NovaCareCRM/public/index.php?action=autorizaciones&orden_id=<?php echo $orden['id']; ?>"
                           class="bg-white text-yellow-700 text-sm font-semibold px-4 py-1 rounded-lg hover:bg-yellow-50 transition">
                            Gestionar en Autorizaciones →
                        </a>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <?php if ($ea === 'pendiente'): ?>
                        <div class="flex items-start gap-3 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-yellow-600 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-yellow-800 font-semibold">Autorización requerida</p>
                                <p class="text-yellow-700 text-sm mt-1">Uno o más productos de esta orden requieren autorización médica antes de ser dispensados. Dirígete al módulo de Autorizaciones para procesarlos.</p>
                            </div>
                        </div>

                        <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'doctor'])): ?>
                        <!-- Acciones rápidas para admin/doctor -->
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <!-- Aprobar toda la orden -->
                            <form method="POST" action="/NovaCareCRM/public/index.php?action=autorizaciones&subaction=aprobarOrden"
                                  onsubmit="return confirm('¿Aprobar TODOS los productos pendientes de esta orden?')">
                                <input type="hidden" name="orden_id" value="<?php echo $orden['id']; ?>">
                                <input type="hidden" name="observaciones" value="Aprobación directa desde detalle de orden">
                                <button type="submit"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-3 rounded-lg transition">
                                    Aprobar toda la orden
                                </button>
                            </form>
                            <!-- Rechazar toda la orden -->
                            <button onclick="document.getElementById('modalRechazarOrden').classList.remove('hidden')"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-3 rounded-lg transition">
                                Rechazar toda la orden
                            </button>
                        </div>
                        <?php endif; ?>

                    <?php elseif ($ea === 'aprobada'): ?>
                        <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-4">
                            <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-green-800 font-semibold">Todos los productos han sido autorizados</p>
                                <p class="text-green-700 text-sm mt-1">Esta orden puede proceder con la dispensación de los medicamentos o servicios.</p>
                            </div>
                        </div>

                    <?php elseif ($ea === 'rechazada'): ?>
                        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-4">
                            <svg class="w-6 h-6 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-red-800 font-semibold">Autorización rechazada</p>
                                <p class="text-red-700 text-sm mt-1">Uno o más productos de esta orden fueron rechazados. Revisa el detalle de cada producto en la tabla de arriba.</p>
                            </div>
                        </div>

                    <?php elseif ($ea === 'parcial'): ?>
                        <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-blue-800 font-semibold">Autorización parcial</p>
                                <p class="text-blue-700 text-sm mt-1">Algunos productos están aprobados y otros aún están pendientes de revisión.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Acciones de la Orden -->
            <?php if ($orden['estado'] !== 'anulada'): ?>
            <div class="bg-white rounded-lg shadow p-4 flex justify-end gap-3">
                <a href="?action=ordenes&subaction=anular&id=<?php echo $orden['id']; ?>"
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                   onclick="return confirm('¿Estás seguro de que deseas anular esta orden?')">
                    Anular Orden
                </a>
            </div>
            <?php endif; ?>

        </main>
    </div>

    <!-- Modal Rechazar Orden completa -->
    <div id="modalRechazarOrden" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="bg-red-600 p-4 rounded-t-lg">
                <h3 class="text-white text-lg font-bold">Rechazar Orden Completa</h3>
            </div>
            <form method="POST" action="/NovaCareCRM/public/index.php?action=autorizaciones&subaction=rechazarOrden" class="p-6">
                <input type="hidden" name="orden_id" value="<?php echo $orden['id']; ?>">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Motivo del rechazo <span class="text-red-500">*</span></label>
                    <textarea name="motivo_rechazo" rows="3" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-300"
                        placeholder="Ingrese el motivo del rechazo..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button"
                        onclick="document.getElementById('modalRechazarOrden').classList.add('hidden')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-semibold">
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>