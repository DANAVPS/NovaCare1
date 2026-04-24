<!-- app/Views/autorizaciones/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Autorizaciones'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
        .btn-primary { background-color: #f51b1c; transition: all 0.2s; }
        .btn-primary:hover { background-color: #d91617; transform: scale(1.02); }
        .sidebar-link:hover { background-color: #f51b1c; color: white; transform: translateX(5px); }
        .status-pendiente { background-color: #fef3c7; color: #d97706; }
        .status-aprobada { background-color: #d1fae5; color: #059669; }
        .status-rechazada { background-color: #fee2e2; color: #dc2626; }
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
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <a href="/xampp/NovaCareCRM/public/index.php?action=dashboard" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Dashboard</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=clientes" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Clientes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Productos</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=autorizaciones" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Autorizaciones</a>
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
                    <h1 class="text-2xl font-bold text-gray-800">Autorizaciones Médicas</h1>
                    <p class="text-gray-600">Gestión de autorizaciones para procedimientos, medicamentos y exámenes</p>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-primary">
                    <p class="text-gray-500 text-sm">Total Autorizaciones</p>
                    <p class="text-2xl font-bold"><?php echo $stats['total'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <p class="text-gray-500 text-sm">Pendientes</p>
                    <p class="text-2xl font-bold"><?php echo $stats['pendientes'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <p class="text-gray-500 text-sm">Aprobadas</p>
                    <p class="text-2xl font-bold"><?php echo $stats['aprobadas'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                    <p class="text-gray-500 text-sm">Rechazadas</p>
                    <p class="text-2xl font-bold"><?php echo $stats['rechazadas'] ?? 0; ?></p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow mb-6 p-4">
                <div class="flex gap-2">
                    <a href="?action=autorizaciones" class="px-3 py-1 rounded <?php echo !$estadoFiltro ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">Todas</a>
                    <a href="?action=autorizaciones&estado=pendiente" class="px-3 py-1 rounded <?php echo $estadoFiltro == 'pendiente' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">Pendientes</a>
                    <a href="?action=autorizaciones&estado=aprobada" class="px-3 py-1 rounded <?php echo $estadoFiltro == 'aprobada' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">Aprobadas</a>
                    <a href="?action=autorizaciones&estado=rechazada" class="px-3 py-1 rounded <?php echo $estadoFiltro == 'rechazada' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700'; ?>">Rechazadas</a>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Autorización</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Solicitud</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($autorizaciones)): ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay autorizaciones registradas</td></tr>
                        <?php else: ?>
                            <?php foreach ($autorizaciones as $aut): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($aut['numero_autorizacion']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($aut['paciente_nombre']); ?><br><span class="text-xs text-gray-500"><?php echo htmlspecialchars($aut['paciente_identificacion']); ?></span></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($aut['producto_nombre'] ?? 'N/A'); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900 text-center"><?php echo $aut['cantidad_aprobada']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($aut['created_at'])); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full status-<?php echo $aut['estado']; ?>">
                                            <?php echo ucfirst($aut['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                                        <a href="?action=autorizaciones&subaction=show&id=<?php echo $aut['id']; ?>" class="text-blue-600 hover:text-blue-900">Ver</a>
                                        <?php if ($aut['estado'] == 'pendiente'): ?>
                                            <button onclick="mostrarModal(<?php echo $aut['id']; ?>)" class="text-green-600 hover:text-green-900">Aprobar/Rechazar</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal para aprobar/rechazar -->
    <div id="modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="bg-primary p-4 rounded-t-lg">
                <h3 class="text-white text-lg font-bold">Procesar Autorización</h3>
            </div>
            <form id="modalForm" method="POST" action="/xampp/NovaCareCRM/public/index.php?action=autorizaciones&subaction=aprobar" class="p-6">
                <input type="hidden" name="id" id="autorizacionId">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Decisión</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="estado" value="aprobada" checked class="mr-2"> Aprobar
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="estado" value="rechazada" class="mr-2"> Rechazar
                        </label>
                    </div>
                </div>
                <div class="mb-4" id="motivoDiv" style="display:none;">
                    <label class="block text-gray-700 font-semibold mb-2">Motivo de Rechazo</label>
                    <textarea name="motivo_rechazo" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Ingrese el motivo del rechazo"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="cerrarModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancelar</button>
                    <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function mostrarModal(id) {
            document.getElementById('autorizacionId').value = id;
            document.getElementById('modal').classList.remove('hidden');
            document.getElementById('modal').classList.add('flex');
        }
        function cerrarModal() {
            document.getElementById('modal').classList.add('hidden');
            document.getElementById('modal').classList.remove('flex');
        }
        document.querySelectorAll('input[name="estado"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('motivoDiv').style.display = this.value === 'rechazada' ? 'block' : 'none';
            });
        });
    </script>
</body>
</html>