<!-- app/Views/productos/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Productos'; ?> | NovaCare CRM</title>
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

    <!-- Navbar -->
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
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <a href="/xampp/NovaCareCRM/public/index.php?action=dashboard" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=clientes" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Clientes
                </a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Productos
                </a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Órdenes
                </a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=autorizaciones" class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Autorizaciones
                </a>
            </nav>
        </aside>

        <!-- Contenido principal -->
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
                    <h1 class="text-2xl font-bold text-gray-800">Productos y Servicios</h1>
                    <p class="text-gray-600">Catálogo de medicamentos, insumos y procedimientos médicos</p>
                </div>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos&subaction=create" class="btn-primary px-4 py-2 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition">
                    + Nuevo Producto
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-primary">
                    <p class="text-gray-500 text-sm">Total Productos</p>
                    <p class="text-2xl font-bold"><?php echo $stats['total'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <p class="text-gray-500 text-sm">Medicamentos</p>
                    <p class="text-2xl font-bold"><?php echo $stats['total_medicamentos'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                    <p class="text-gray-500 text-sm">Procedimientos</p>
                    <p class="text-2xl font-bold"><?php echo $stats['total_procedimientos'] ?? 0; ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <p class="text-gray-500 text-sm">Bajo Stock</p>
                    <p class="text-2xl font-bold"><?php echo $stats['productos_bajo_stock'] ?? 0; ?></p>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Autorización</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($productos)): ?>
                            <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay productos registrados</td></tr>
                        <?php else: ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($producto['codigo']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            <?php echo $producto['tipo'] == 'medicamento' ? 'bg-blue-100 text-blue-800' : ''; ?>
                                            <?php echo $producto['tipo'] == 'procedimiento' ? 'bg-green-100 text-green-800' : ''; ?>
                                            <?php echo $producto['tipo'] == 'examen' ? 'bg-purple-100 text-purple-800' : ''; ?>">
                                            <?php echo ucfirst($producto['tipo']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">$<?php echo number_format($producto['precio_unitario'], 0, ',', '.'); ?></td>
                                    <td class="px-6 py-4 text-sm <?php echo ($producto['stock_actual'] ?? 0) <= ($producto['stock_minimo'] ?? 0) ? 'text-red-600 font-bold' : 'text-gray-900'; ?>">
                                        <?php echo $producto['stock_actual'] ?? 0; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($producto['requiere_autorizacion']): ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Requiere Autorización</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Libre</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium space-x-2">
                                        <a href="?action=productos&subaction=edit&id=<?php echo $producto['id']; ?>" class="text-green-600 hover:text-green-900">Editar</a>
                                        <a href="?action=productos&subaction=delete&id=<?php echo $producto['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
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