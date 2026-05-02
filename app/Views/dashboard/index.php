<!-- app/Views/dashboard/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Dashboard'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Gabarito', sans-serif;
        }
        
        .bg-primary { 
            background-color: #f51b1c; 
        }
        
        .text-primary { 
            color: #f51b1c; 
        }
        
        .border-primary { 
            border-color: #f51b1c; 
        }
        
        .btn-primary { 
            background-color: #f51b1c; 
            transition: all 0.2s; 
        }
        
        .btn-primary:hover { 
            background-color: #d91617; 
            transform: scale(1.02); 
        }
        
        .sidebar-link {
            transition: all 0.2s ease;
        }
        
        .sidebar-link:hover {
            background-color: #f51b1c;
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar-link.active {
            background-color: #f51b1c;
            color: white;
        }
        
        .hover-card:hover {
            transform: translateY(-4px);
            transition: all 0.3s ease;
        }
        
        /* Scrollbar personalizada */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #f51b1c;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #d91617;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Navbar superior -->
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
                    <!-- Fecha actual -->
                    <div class="hidden md:block text-sm text-gray-500">
                        <?php echo date('d/m/Y H:i'); ?>
                    </div>
                    
                    <!-- Menú de usuario -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                            </div>
                            <span class="text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl hidden group-hover:block">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mi Perfil</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configuración</a>
                            <hr class="my-1">
                            <a href="/NovaCareCRM/public/index.php?action=logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal con sidebar -->
    <div class="flex">
        <!-- Sidebar izquierdo -->
        <aside class="w-64 bg-white shadow-lg min-h-screen" style="height: calc(100vh - 64px);">
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="/NovaCareCRM/public/index.php?action=dashboard" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg <?php echo (!isset($_GET['modulo']) && $_GET['action'] == 'dashboard') ? 'active bg-primary text-white' : 'hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashboard
                    </a>
                    
                    <!-- Clientes -->
                    <a href="/NovaCareCRM/public/index.php?action=clientes" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Clientes
                        <span class="ml-auto text-xs text-gray-400">EPS/IPS/Pacientes</span>
                    </a>
                    
                    <!-- Productos -->
                    <a href="/NovaCareCRM/public/index.php?action=productos" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Productos/Servicios
                    </a>
                    
                    <!-- Órdenes Médicas -->
                    <a href="/NovaCareCRM/public/index.php?action=ordenes" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Órdenes Médicas
                    </a>
                    
                    <!-- Autorizaciones -->
                    <a href="/NovaCareCRM/public/index.php?action=autorizaciones" 
                       class="sidebar-link flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Autorizaciones
                    </a>
                </div>
                
                <hr class="my-4 border-gray-200">
                
                <!-- Reportes -->
                <div class="space-y-1">
                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Reportes</div>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Estadísticas
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Contenido principal -->
        <main class="flex-1 p-6">
            <!-- Mensajes flash -->
            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md">
                    <?php echo htmlspecialchars($_SESSION['flash_success']); ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md">
                    <?php echo htmlspecialchars($_SESSION['flash_error']); ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>
            
            <!-- Tarjeta de bienvenida -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6 border-l-4 border-primary">
                <h1 class="text-2xl font-bold text-gray-800">¡Bienvenido, <?php echo htmlspecialchars($user['name']); ?>!</h1>
                <p class="text-gray-600 mt-1">Panel de control de NovaCare CRM - Sistema de Gestión para Salud</p>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Clientes -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-primary hover-card transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold">Total Clientes</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['total_clientes'] ?? 0; ?></p>
                        </div>
                        <div class="bg-primary/10 p-3 rounded-full">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Productos Activos -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500 hover-card transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold">Productos Activos</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['total_productos'] ?? 0; ?></p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Órdenes del Día -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 hover-card transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold">Órdenes Hoy</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['ordenes_hoy'] ?? 0; ?></p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Autorizaciones Pendientes -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500 hover-card transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold">Autorizaciones Pendientes</p>
                            <p class="text-3xl font-bold text-gray-800 mt-1"><?php echo $stats['autorizaciones_pendientes'] ?? 0; ?></p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <a href="/NovaCareCRM/public/index.php?action=clientes&tipo=paciente" 
                   class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold opacity-90">Registrar Nuevo</p>
                            <p class="text-xl font-bold">Paciente</p>
                        </div>
                        <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </a>
                
                <a href="/NovaCareCRM/public/index.php?action=ordenes&action=create" 
                   class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold opacity-90">Crear Nueva</p>
                            <p class="text-xl font-bold">Orden Médica</p>
                        </div>
                        <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </a>
                
                <a href="/NovaCareCRM/public/index.php?action=productos&action=create" 
                   class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold opacity-90">Agregar al</p>
                            <p class="text-xl font-bold">Catálogo</p>
                        </div>
                        <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </a>
                
                <a href="/NovaCareCRM/public/index.php?action=autorizaciones" 
                   class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg shadow-md p-6 text-white hover:shadow-lg transition-all transform hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold opacity-90">Gestionar</p>
                            <p class="text-xl font-bold">Autorizaciones</p>
                        </div>
                        <svg class="w-8 h-8 opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </a>
            </div>

            <!-- Información de la cuenta -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Información de la Cuenta</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold">Nombre completo</p>
                        <p class="font-medium text-gray-800 mt-1"><?php echo htmlspecialchars($user['name']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold">Correo electrónico</p>
                        <p class="font-medium text-gray-800 mt-1"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold">Rol</p>
                        <p class="mt-1">
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-primary/10 text-primary uppercase">
                                <?php echo htmlspecialchars($user['role']); ?>
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-semibold">Miembro desde</p>
                        <p class="font-medium text-gray-800 mt-1"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">© 2025 NovaCare CRM - Todos los derechos reservados | Sistema de Gestión para Salud</p>
            </div>
        </main>
    </div>
    
    <script>
        // Marcar el enlace activo en el sidebar
        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active', 'bg-primary', 'text-white');
            }
        });
    </script>
    
</body>
</html>