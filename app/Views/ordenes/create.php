<!-- app/Views/ordenes/create.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Nueva Orden Médica'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Gabarito', sans-serif; }
        .bg-primary { background-color: #f51b1c; }
        .text-primary { color: #f51b1c; }
        .btn-primary { background-color: #f51b1c; transition: all 0.2s; }
        .btn-primary:hover { background-color: #d91617; transform: scale(1.02); }
        .producto-row:hover { background-color: #f9fafb; }
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
                <h1 class="text-2xl font-bold text-gray-800">Nueva Orden Médica</h1>
                <a href="/xampp/NovaCareCRM/public/index.php?action=ordenes" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="/xampp/NovaCareCRM/public/index.php?action=ordenes&subaction=store" id="ordenForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Paciente *</label>
                            <select name="paciente_id" required class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Seleccione un paciente</option>
                                <?php foreach ($pacientes as $paciente): ?>
                                    <option value="<?php echo $paciente['id']; ?>"><?php echo htmlspecialchars($paciente['nombre'] . ' ' . ($paciente['apellido'] ?? '') . ' - ' . $paciente['identificacion']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Médico</label>
                            <select name="medico_id" class="w-full px-4 py-2 border rounded-lg">
                                <option value="">Seleccione un médico</option>
                                <?php foreach ($medicos as $medico): ?>
                                    <option value="<?php echo $medico['id']; ?>"><?php echo htmlspecialchars($medico['nombre'] . ' ' . ($medico['apellido'] ?? '')); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Fecha de la Orden *</label>
                            <input type="date" name="fecha_orden" value="<?php echo date('Y-m-d'); ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Prioridad</label>
                            <select name="prioridad" class="w-full px-4 py-2 border rounded-lg">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Diagnóstico</label>
                            <textarea name="diagnostico" rows="2" class="w-full px-4 py-2 border rounded-lg" placeholder="Ingrese el diagnóstico o motivo de la consulta"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Observaciones</label>
                            <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border rounded-lg" placeholder="Observaciones adicionales"></textarea>
                        </div>
                    </div>

                    <!-- Productos -->
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Productos / Servicios</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border rounded-lg" id="productosTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Producto</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Cantidad</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Precio Unit.</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Subtotal</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="productosBody">
                                    <tr class="producto-row">
                                        <td class="px-4 py-2">
                                            <select name="productos[0][id]" class="producto-select w-full px-2 py-1 border rounded" data-index="0" required>
                                                <option value="">Seleccione un producto</option>
                                                <?php foreach ($productos as $producto): ?>
                                                    <option value="<?php echo $producto['id']; ?>" data-precio="<?php echo $producto['precio_unitario']; ?>"><?php echo htmlspecialchars($producto['codigo'] . ' - ' . $producto['nombre']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" name="productos[0][precio]" class="producto-precio" value="0">
                                        </td>
                                        <td class="px-4 py-2">
                                            <input type="number" name="productos[0][cantidad]" class="producto-cantidad w-20 px-2 py-1 border rounded" value="1" min="1">
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="producto-precio-show">$0</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <span class="producto-subtotal">$0</span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <button type="button" class="eliminar-producto text-red-600 hover:text-red-800">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2">
                                            <button type="button" id="agregarProducto" class="text-primary hover:underline">+ Agregar otro producto</button>
                                        </td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-2 text-right font-bold">Total:</td>
                                        <td colspan="2" class="px-4 py-2 text-xl font-bold text-primary" id="totalOrden">$0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary text-white font-bold py-2 px-6 rounded-lg shadow-md hover:shadow-lg transition">Guardar Orden Médica</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let productoIndex = 1;

        function calcularTotales() {
            let totalGeneral = 0;
            document.querySelectorAll('.producto-row').forEach(row => {
                const cantidad = parseFloat(row.querySelector('.producto-cantidad')?.value) || 0;
                const precio = parseFloat(row.querySelector('.producto-precio')?.value) || 0;
                const subtotal = cantidad * precio;
                const subtotalSpan = row.querySelector('.producto-subtotal');
                if (subtotalSpan) subtotalSpan.textContent = '$' + subtotal.toLocaleString('es-CO');
                totalGeneral += subtotal;
            });
            document.getElementById('totalOrden').textContent = '$' + totalGeneral.toLocaleString('es-CO');
        }

        function actualizarPrecio(select) {
            const row = select.closest('.producto-row');
            const precio = select.options[select.selectedIndex]?.dataset.precio || 0;
            const precioInput = row.querySelector('.producto-precio');
            const precioShow = row.querySelector('.producto-precio-show');
            if (precioInput) precioInput.value = precio;
            if (precioShow) precioShow.textContent = '$' + parseFloat(precio).toLocaleString('es-CO');
            calcularTotales();
        }

        document.querySelectorAll('.producto-select').forEach(select => {
            select.addEventListener('change', function() { actualizarPrecio(this); });
        });
        document.querySelectorAll('.producto-cantidad').forEach(input => {
            input.addEventListener('input', () => calcularTotales());
        });

        document.getElementById('agregarProducto').addEventListener('click', function() {
            const tbody = document.getElementById('productosBody');
            const newRow = document.createElement('tr');
            newRow.className = 'producto-row';
            newRow.innerHTML = `
                <td class="px-4 py-2">
                    <select name="productos[${productoIndex}][id]" class="producto-select w-full px-2 py-1 border rounded" data-index="${productoIndex}">
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto['id']; ?>" data-precio="<?php echo $producto['precio_unitario']; ?>"><?php echo htmlspecialchars($producto['codigo'] . ' - ' . $producto['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="productos[${productoIndex}][precio]" class="producto-precio" value="0">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="productos[${productoIndex}][cantidad]" class="producto-cantidad w-20 px-2 py-1 border rounded" value="1" min="1">
                </td>
                <td class="px-4 py-2">
                    <span class="producto-precio-show">$0</span>
                </td>
                <td class="px-4 py-2">
                    <span class="producto-subtotal">$0</span>
                </td>
                <td class="px-4 py-2">
                    <button type="button" class="eliminar-producto text-red-600 hover:text-red-800">Eliminar</button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            const newSelect = newRow.querySelector('.producto-select');
            const newCantidad = newRow.querySelector('.producto-cantidad');
            newSelect.addEventListener('change', function() { actualizarPrecio(this); });
            newCantidad.addEventListener('input', () => calcularTotales());
            newRow.querySelector('.eliminar-producto').addEventListener('click', function() { newRow.remove(); calcularTotales(); });
            productoIndex++;
        });

        document.querySelectorAll('.eliminar-producto').forEach(btn => {
            btn.addEventListener('click', function() { this.closest('.producto-row').remove(); calcularTotales(); });
        });
    </script>
</body>
</html>