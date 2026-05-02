<!-- app/Views/auth/forgot-password.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Restablecer Contraseña'; ?> | NovaCare CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
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
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/NovaCareCRM/Imagenes/salud.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        .bg-overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.5) 100%);
        }
        .content-wrapper {
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="bg-overlay"></div>

    <div class="content-wrapper max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-primary rounded-2xl shadow-lg mb-4 transform transition hover:scale-105">
                <span class="text-white text-3xl font-black">NC</span>
            </div>
            <h1 class="text-4xl font-black text-white drop-shadow-lg">NovaCare CRM</h1>
            <p class="text-white/90 mt-1 text-lg drop-shadow">Restablecer Contraseña</p>
        </div>
        
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-primary p-4">
                <h2 class="text-white text-xl font-bold text-center">¿Olvidaste tu contraseña?</h2>
            </div>
            
            <div class="p-8">
                <?php if (isset($error) && $error): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-primary text-red-700 p-4 rounded-md text-sm">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success) && $success): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-md text-sm">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <p class="text-gray-600 mb-6 text-center">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
                
                <form method="POST" action="/NovaCareCRM/public/index.php?action=forgot-password-submit" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Correo Electrónico</label>
                        <input type="email" name="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none bg-white/90"
                               placeholder="admin@novacare.com">
                    </div>
                    
                    <button type="submit" class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-[1.02]">
                        Enviar enlace de restablecimiento
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="/NovaCareCRM/public/index.php?action=login" class="text-primary font-semibold hover:underline transition">
                        ← Volver al inicio de sesión
                    </a>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">© 2025 NovaCare CRM - Todos los derechos reservados</p>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>