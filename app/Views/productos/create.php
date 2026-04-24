<!-- app/Views/productos/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Nuevo Producto'; ?> | NovaCare CRM</title>
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
                <a href="/xampp/NovaCareCRM/public/index.php?action=dashboard" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Dashboard</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=clientes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Clientes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Productos</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/xampp/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Nuevo Producto / Servicio</h1>
                <a href="/xampp/NovaCareCRM/public/index.php?action=productos" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="/xampp/NovaCareCRM/public/index.php?action=productos&subaction=store">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Código *</label>
                            <input type="text" name="codigo" required class="w-full px-4 py-2 border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nombre *</label>
                            <input type="text" name="nombre" required class="w-full px-4 py-2 border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tipo *</label>
                            <select name="tipo" required class="w-full px-4 py-2 border rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20">
                                <option value="medicamento">Medicamento</option>
                                <option value="insumo">Insumo</option>
                                <option value="procedimiento">Procedimiento</option>
                                <option value="examen">Examen</option>
                                <option value="otros">Otros</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Categoría</label>
                            <input type="text" name="categoria" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Descripción</label>
                            <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border rounded-lg"></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Precio Unitario *</label>
                            <input type="number" step="0.01" name="precio_unitario" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Costo Unitario</label>
                            <input type="number" step="0.01" name="costo_unitario" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">IVA (%)</label>
                            <input type="number" step="0.01" name="iva" value="19" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Unidad de Medida</label>
                            <input type="text" name="unidad_medida" placeholder="Ej: tableta, ml, caja" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Stock Mínimo</label>
                            <input type="number" name="stock_minimo" value="0" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Stock Actual</label>
                            <input type="number" name="stock_actual" value="0" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="requiere_autorizacion" value="1" class="mr-2">
                            <label class="text-gray-700 font-semibold">Requiere Autorización Médica</label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>