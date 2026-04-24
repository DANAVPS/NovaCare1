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
        .status-pendiente { background-color: #fef3c7; color: #d97706; }
        .status-completada { background-color: #d1fae5; color: #059669; }
        .status-anulada { background-color: #fee2e2; color: #dc2626; }
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
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <a href="/xampp/NovaCareCRM/public/index.php?action=dashboard" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Dashboard</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=clientes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Clientes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Productos</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Órdenes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalle de Orden Médica</h1>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Información de la Orden -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-primary p-4">
                        <h2 class="text-white text-lg font-bold">Información de la Orden</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="font-semibold text-gray-600">N° Orden:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($orden['numero_orden']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Estado:</label><br><span class="px-2 py-1 text-xs rounded-full status-<?php echo $orden['estado']; ?>"><?php echo ucfirst($orden['estado']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Fecha:</label><br><span class="text-gray-800"><?php echo date('d/m/Y', strtotime($orden['fecha_orden'])); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Prioridad:</label><br><span class="text-gray-800"><?php echo ucfirst($orden['prioridad']); ?></span></div>
                            <div class="col-span-2"><label class="font-semibold text-gray-600">Diagnóstico:</label><br><span class="text-gray-800"><?php echo nl2br(htmlspecialchars($orden['diagnostico'] ?? 'No especificado')); ?></span></div>
                            <div class="col-span-2"><label class="font-semibold text-gray-600">Observaciones:</label><br><span class="text-gray-800"><?php echo nl2br(htmlspecialchars($orden['observaciones'] ?? 'Ninguna')); ?></span></div>
                        </div>
                    </div>
                </div>

                <!-- Información del Paciente -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-green-600 p-4">
                        <h2 class="text-white text-lg font-bold">Información del Paciente</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="font-semibold text-gray-600">Nombre:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_nombre']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Identificación:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_identificacion']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Teléfono:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($orden['paciente_telefono'] ?? 'No registrado'); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Médico:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($orden['medico_nombre'] ?? 'No asignado'); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white rounded-lg shadow overflow-hidden mt-6">
                <div class="bg-blue-600 p-4">
                    <h2 class="text-white text-lg font-bold">Productos y Servicios</h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Producto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Código</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">Cantidad</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Precio</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $prod): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($prod['producto_nombre']); ?></td>
                                <td class="px-4 py-2 text-sm"><?php echo htmlspecialchars($prod['producto_codigo']); ?></td>
                                <td class="px-4 py-2 text-sm text-center"><?php echo $prod['cantidad']; ?></td>
                                <td class="px-4 py-2 text-sm text-right">$<?php echo number_format($prod['precio_unitario'], 0, ',', '.'); ?></td>
                                <td class="px-4 py-2 text-sm text-right font-semibold">$<?php echo number_format($prod['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-bold">Total:</td>
                                <td class="px-4 py-2 text-right text-xl font-bold text-primary">$<?php echo number_format($orden['total_valor'], 0, ',', '.'); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>