<!-- app/Views/clientes/edit.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Editar Cliente'; ?> | NovaCare CRM</title>
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
                <a href="/NovaCareCRM/public/index.php?action=clientes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg bg-primary text-white">Clientes</a>
                <a href="/NovaCareCRM/public/index.php?action=productos" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Productos</a>
                <a href="/NovaCareCRM/public/index.php?action=ordenes" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Órdenes</a>
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-primary hover:text-white transition">Autorizaciones</a>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Editar Cliente</h1>
                <a href="/NovaCareCRM/public/index.php?action=clientes" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">← Volver</a>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="/NovaCareCRM/public/index.php?action=clientes&subaction=update">
                    <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Tipo de Cliente *</label>
                            <select name="tipo" required class="w-full px-4 py-2 border rounded-lg">
                                <option value="paciente" <?php echo $cliente['tipo'] == 'paciente' ? 'selected' : ''; ?>>Paciente</option>
                                <option value="medico" <?php echo $cliente['tipo'] == 'medico' ? 'selected' : ''; ?>>Médico</option>
                                <option value="EPS" <?php echo $cliente['tipo'] == 'EPS' ? 'selected' : ''; ?>>EPS</option>
                                <option value="IPS" <?php echo $cliente['tipo'] == 'IPS' ? 'selected' : ''; ?>>IPS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Identificación *</label>
                            <input type="text" name="identificacion" value="<?php echo htmlspecialchars($cliente['identificacion']); ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nombre *</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Apellido</label>
                            <input type="text" name="apellido" value="<?php echo htmlspecialchars($cliente['apellido'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($cliente['email'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Teléfono</label>
                            <input type="text" name="telefono" value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Dirección</label>
                            <textarea name="direccion" rows="2" class="w-full px-4 py-2 border rounded-lg"><?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Ciudad</label>
                            <input type="text" name="ciudad" value="<?php echo htmlspecialchars($cliente['ciudad'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Departamento</label>
                            <input type="text" name="departamento" value="<?php echo htmlspecialchars($cliente['departamento'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="btn-primary text-white font-bold py-2 px-6 rounded-lg shadow-md">Actualizar Cliente</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>