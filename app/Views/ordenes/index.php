<!-- app/Views/ordenes/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Órdenes Médicas'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
        .btn-primary { background-color: #f51b1c; transition: all 0.2s; }
        .btn-primary:hover { background-color: #d91617; transform: scale(1.02); }
        .sidebar-link:hover { background-color: #f51b1c; color: white; transform: translateX(5px); }
        .sidebar-link.active { background-color: #f51b1c; color: white; }
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
                    <h1 class="text-2xl font-bold text-gray-800">Órdenes Médicas</h1>
                    <p class="text-gray-600">Gestión de órdenes, prescripciones y tratamientos</p>
                </div>
                <a href="/NovaCareCRM/public/index.php?action=ordenes&subaction=create"
                   class="btn-primary px-4 py-2 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition">
                    + Nueva Orden
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-primary">
                    <p class="text-gray-500 text-sm">Total Órdenes</p>
                    <p class="text-2xl font-bold"><?php echo $stats['total'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <p class="text-gray-500 text-sm">Pendientes</p>
                    <p class="text-2xl font-bold"><?php echo $stats['pendientes'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <p class="text-gray-500 text-sm">Completadas</p>
                    <p class="text-2xl font-bold"><?php echo $stats['completadas'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                    <p class="text-gray-500 text-sm">Anuladas</p>
                    <p class="text-2xl font-bold"><?php echo $stats['anuladas'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <p class="text-gray-500 text-sm">Valor Total</p>
                    <p class="text-2xl font-bold">$<?php echo number_format($stats['valor_total'] ?? 0, 0, ',', '.'); ?></p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <div class="flex gap-2 flex-wrap">
                    <a href="?action=ordenes"
                       class="px-3 py-1 rounded text-sm <?php echo !$estadoFiltro ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Todas
                    </a>
                    <a href="?action=ordenes&estado=pendiente"
                       class="px-3 py-1 rounded text-sm <?php echo $estadoFiltro == 'pendiente'  ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Pendientes
                    </a>
                    <a href="?action=ordenes&estado=completada"
                       class="px-3 py-1 rounded text-sm <?php echo $estadoFiltro == 'completada' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Completadas
                    </a>
                    <a href="?action=ordenes&estado=anulada"
                       class="px-3 py-1 rounded text-sm <?php echo $estadoFiltro == 'anulada'    ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">
                        Anuladas
                    </a>
                </div>
            </div>

            <!-- Tabla — 8 columnas (sin "Estado Orden") -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prioridad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Autorización</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($ordenes)): ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No hay órdenes médicas registradas
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr class="hover:bg-gray-50">

                                    <!-- N° Orden -->
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($orden['numero_orden']); ?>
                                    </td>

                                    <!-- Paciente -->
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($orden['paciente_nombre']); ?>
                                        <br>
                                        <span class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($orden['paciente_identificacion']); ?>
                                        </span>
                                    </td>

                                    <!-- Médico -->
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo htmlspecialchars($orden['medico_nombre'] ?? 'No asignado'); ?>
                                    </td>

                                    <!-- Fecha -->
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($orden['fecha_orden'])); ?>
                                    </td>

                                    <!-- Prioridad -->
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            <?php echo $orden['prioridad'] == 'urgente' ? 'bg-purple-100 text-purple-800' : ''; ?>
                                            <?php echo $orden['prioridad'] == 'alta'    ? 'bg-red-100 text-red-800'       : ''; ?>
                                            <?php echo $orden['prioridad'] == 'media'   ? 'bg-yellow-100 text-yellow-800' : ''; ?>
                                            <?php echo $orden['prioridad'] == 'baja'    ? 'bg-green-100 text-green-800'   : ''; ?>">
                                            <?php echo ucfirst($orden['prioridad']); ?>
                                        </span>
                                    </td>

                                    <!-- Estado Autorización -->
                                    <td class="px-6 py-4">
                                        <?php
                                            $ea = $orden['estado_autorizacion'] ?? null;
                                            if ($ea === 'aprobada')
                                                echo '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aprobada</span>';
                                            elseif ($ea === 'rechazada')
                                                echo '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rechazada</span>';
                                            elseif ($ea === 'pendiente')
                                                echo '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>';
                                            elseif ($ea === 'parcial')
                                                echo '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Parcial</span>';
                                            else
                                                echo '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">No requiere</span>';
                                        ?>
                                    </td>

                                    <!-- Valor -->
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        $<?php echo number_format($orden['total_valor'], 0, ',', '.'); ?>
                                    </td>

                                    <!-- Acciones -->
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <div class="flex items-center gap-3">
                                            <a href="?action=ordenes&subaction=show&id=<?php echo $orden['id']; ?>"
                                               class="text-blue-600 hover:text-blue-900 hover:underline">
                                                Ver
                                            </a>
                                            <?php if ($orden['estado'] !== 'anulada'): ?>
                                                <a href="?action=ordenes&subaction=anular&id=<?php echo $orden['id']; ?>"
                                                   class="text-red-600 hover:text-red-900 hover:underline"
                                                   onclick="return confirm('¿Anular esta orden médica?')">
                                                    Anular
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400 text-xs">Anulada</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</body>
</html>