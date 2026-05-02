<!-- app/Views/productos/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Editar Producto'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
        .btn-primary { background-color: #f51b1c; transition: all 0.2s; }
        .btn-primary:hover { background-color: #d91617; transform: scale(1.02); }
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
                <a href="/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Productos</a>
                <a href="/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Editar Producto</h1>
                <a href="/NovaCareCRM/public/index.php?action=productos" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="/NovaCareCRM/public/index.php?action=productos&subaction=update">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Código *</label>
                            <input type="text" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nombre *</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tipo *</label>
                            <select name="tipo" required class="w-full px-4 py-2 border rounded-lg">
                                <option value="medicamento" <?php echo $producto['tipo'] == 'medicamento' ? 'selected' : ''; ?>>Medicamento</option>
                                <option value="insumo" <?php echo $producto['tipo'] == 'insumo' ? 'selected' : ''; ?>>Insumo</option>
                                <option value="procedimiento" <?php echo $producto['tipo'] == 'procedimiento' ? 'selected' : ''; ?>>Procedimiento</option>
                                <option value="examen" <?php echo $producto['tipo'] == 'examen' ? 'selected' : ''; ?>>Examen</option>
                                <option value="otros" <?php echo $producto['tipo'] == 'otros' ? 'selected' : ''; ?>>Otros</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Categoría</label>
                            <input type="text" name="categoria" value="<?php echo htmlspecialchars($producto['categoria'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Descripción</label>
                            <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border rounded-lg"><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Precio Unitario *</label>
                            <input type="number" step="0.01" name="precio_unitario" value="<?php echo $producto['precio_unitario']; ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Costo Unitario</label>
                            <input type="number" step="0.01" name="costo_unitario" value="<?php echo $producto['costo_unitario'] ?? 0; ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">IVA (%)</label>
                            <input type="number" step="0.01" name="iva" value="<?php echo $producto['iva']; ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Unidad de Medida</label>
                            <input type="text" name="unidad_medida" value="<?php echo htmlspecialchars($producto['unidad_medida'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Stock Actual</label>
                            <input type="number" name="stock_actual" value="<?php echo $producto['stock_actual']; ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="requiere_autorizacion" value="1" <?php echo $producto['requiere_autorizacion'] ? 'checked' : ''; ?> class="mr-2">
                            <label class="text-gray-700 font-semibold">Requiere Autorización Médica</label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition">Actualizar Producto</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>