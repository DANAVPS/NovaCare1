<!-- app/Views/clientes/show.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Detalle Cliente'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
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
                <a href="/NovaCareCRM/public/index.php?action=clientes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Clientes</a>
                <a href="/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Productos</a>
                <a href="/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Detalle del Cliente</h1>
                <a href="/NovaCareCRM/public/index.php?action=clientes" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-primary p-4">
                    <h2 class="text-white text-xl font-bold">Información General</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="font-semibold text-gray-600">Tipo:</label> <span class="text-gray-800"><?php echo strtoupper($cliente['tipo']); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Identificación:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['identificacion']); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Nombre:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['nombre']); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Apellido:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['apellido'] ?? '-'); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Email:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['email'] ?? '-'); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Teléfono:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['telefono'] ?? '-'); ?></span></div>
                        <div class="md:col-span-2"><label class="font-semibold text-gray-600">Dirección:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['direccion'] ?? '-'); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Ciudad:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['ciudad'] ?? '-'); ?></span></div>
                        <div><label class="font-semibold text-gray-600">Departamento:</label> <span class="text-gray-800"><?php echo htmlspecialchars($cliente['departamento'] ?? '-'); ?></span></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>