<!-- app/Views/autorizaciones/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Detalle Autorización'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
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
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <a href="/NovaCareCRM/public/index.php?action=dashboard" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Dashboard</a>
                <a href="/NovaCareCRM/public/index.php?action=clientes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Clientes</a>
                <a href="/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Productos</a>
                <a href="/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalle de Autorización</h1>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-primary p-4">
                        <h2 class="text-white text-lg font-bold">Información de la Autorización</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="font-semibold text-gray-600">N° Autorización:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['numero_autorizacion']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Estado:</label><br><span class="px-2 py-1 text-xs rounded-full status-<?php echo $autorizacion['estado']; ?>"><?php echo ucfirst($autorizacion['estado']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Fecha Solicitud:</label><br><span class="text-gray-800"><?php echo date('d/m/Y H:i', strtotime($autorizacion['created_at'])); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Fecha Autorización:</label><br><span class="text-gray-800"><?php echo $autorizacion['fecha_autorizacion'] ? date('d/m/Y H:i', strtotime($autorizacion['fecha_autorizacion'])) : 'Pendiente'; ?></span></div>
                            <div><label class="font-semibold text-gray-600">Cantidad Aprobada:</label><br><span class="text-gray-800"><?php echo $autorizacion['cantidad_aprobada']; ?></span></div>
                            <?php if ($autorizacion['motivo_rechazo']): ?>
                                <div class="col-span-2"><label class="font-semibold text-gray-600">Motivo Rechazo:</label><br><span class="text-red-600"><?php echo htmlspecialchars($autorizacion['motivo_rechazo']); ?></span></div>
                            <?php endif; ?>
                            <div class="col-span-2"><label class="font-semibold text-gray-600">Observaciones:</label><br><span class="text-gray-800"><?php echo nl2br(htmlspecialchars($autorizacion['observaciones'] ?? 'Ninguna')); ?></span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="bg-green-600 p-4">
                        <h2 class="text-white text-lg font-bold">Información del Paciente y Producto</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="font-semibold text-gray-600">Paciente:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['paciente_nombre']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Identificación:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['paciente_identificacion']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Producto:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['producto_nombre']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Código Producto:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['producto_codigo']); ?></span></div>
                            <div><label class="font-semibold text-gray-600">Cantidad Solicitada:</label><br><span class="text-gray-800"><?php echo $autorizacion['cantidad']; ?></span></div>
                            <?php if ($autorizacion['medico_nombre']): ?>
                                <div><label class="font-semibold text-gray-600">Médico Autorizador:</label><br><span class="text-gray-800"><?php echo htmlspecialchars($autorizacion['medico_nombre']); ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>