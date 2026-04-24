<!-- app/Views/auth/reset-password.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Nueva Contraseña'; ?> | NovaCare CRM</title>
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
            background-image: url('/xampp/NovaCareCRM/Imagenes/salud.jpg');
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
        .password-strength {
            height: 4px;
            transition: all 0.3s;
            border-radius: 2px;
            width: 0%;
        }
        .requirement {
            font-size: 11px;
            transition: all 0.2s;
        }
        .requirement.valid {
            color: #10b981;
        }
        .requirement.invalid {
            color: #ef4444;
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
            <p class="text-white/90 mt-1 text-lg drop-shadow">Nueva Contraseña</p>
        </div>
        
        <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-primary p-4">
                <h2 class="text-white text-xl font-bold text-center">Crear nueva contraseña</h2>
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
                    Ingresa tu nueva contraseña. Debe tener al menos 6 caracteres.
                </p>
                
                <form method="POST" action="/xampp/NovaCareCRM/public/index.php?action=reset-password" class="space-y-6" id="resetForm">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Nueva Contraseña</label>
                        <input type="password" name="password" id="password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none bg-white/90"
                               placeholder="••••••••">
                        <div class="password-strength mt-2 bg-gray-200 rounded-full"></div>
                        
                        <div class="mt-2 space-y-1">
                            <p id="req-length" class="requirement invalid text-xs">✗ Mínimo 6 caracteres</p>
                            <p id="req-number" class="requirement invalid text-xs">✗ Al menos un número</p>
                            <p id="req-letter" class="requirement invalid text-xs">✗ Al menos una letra</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition outline-none bg-white/90"
                               placeholder="••••••••">
                        <p id="match-message" class="text-xs mt-1"></p>
                    </div>
                    
                    <button type="submit" id="submit-btn" class="btn-primary w-full text-white font-bold py-3 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:scale-[1.02]">
                        Restablecer Contraseña
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="/xampp/NovaCareCRM/public/index.php?action=login" class="text-primary font-semibold hover:underline transition">
                        ← Volver al inicio de sesión
                    </a>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">© 2025 NovaCare CRM - Todos los derechos reservados</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Elementos del DOM
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        const matchMsg = document.getElementById('match-message');
        const submitBtn = document.getElementById('submit-btn');
        const strengthBar = document.querySelector('.password-strength');
        
        // Elementos de requerimientos
        const reqLength = document.getElementById('req-length');
        const reqNumber = document.getElementById('req-number');
        const reqLetter = document.getElementById('req-letter');
        
        // Validar fortaleza de la contraseña
        function checkPasswordStrength() {
            const val = password.value;
            
            // Validar longitud
            const hasLength = val.length >= 6;
            // Validar número
            const hasNumber = /[0-9]/.test(val);
            // Validar letra
            const hasLetter = /[a-zA-Z]/.test(val);
            
            // Actualizar estilos de requerimientos
            if (hasLength) {
                reqLength.innerHTML = '✓ Mínimo 6 caracteres';
                reqLength.classList.remove('invalid');
                reqLength.classList.add('valid');
            } else {
                reqLength.innerHTML = '✗ Mínimo 6 caracteres';
                reqLength.classList.remove('valid');
                reqLength.classList.add('invalid');
            }
            
            if (hasNumber) {
                reqNumber.innerHTML = '✓ Al menos un número';
                reqNumber.classList.remove('invalid');
                reqNumber.classList.add('valid');
            } else {
                reqNumber.innerHTML = '✗ Al menos un número';
                reqNumber.classList.remove('valid');
                reqNumber.classList.add('invalid');
            }
            
            if (hasLetter) {
                reqLetter.innerHTML = '✓ Al menos una letra';
                reqLetter.classList.remove('invalid');
                reqLetter.classList.add('valid');
            } else {
                reqLetter.innerHTML = '✗ Al menos una letra';
                reqLetter.classList.remove('valid');
                reqLetter.classList.add('invalid');
            }
            
            // Calcular fortaleza (0-3)
            let strength = 0;
            if (hasLength) strength++;
            if (hasNumber) strength++;
            if (hasLetter) strength++;
            
            // Actualizar barra de fortaleza
            const width = (strength / 3) * 100;
            strengthBar.style.width = width + '%';
            
            if (strength <= 1) {
                strengthBar.style.backgroundColor = '#ef4444'; // Rojo - débil
            } else if (strength <= 2) {
                strengthBar.style.backgroundColor = '#f59e0b'; // Naranja - media
            } else {
                strengthBar.style.backgroundColor = '#10b981'; // Verde - fuerte
            }
            
            // Habilitar/deshabilitar botón según requerimientos
            submitBtn.disabled = !(hasLength && hasNumber && hasLetter);
            
            if (!(hasLength && hasNumber && hasLetter)) {
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            } else {
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            }
            
            // Verificar coincidencia si hay texto en confirm
            if (confirm.value.length > 0) {
                checkMatch();
            }
        }
        
        // Verificar coincidencia de contraseñas
        function checkMatch() {
            if (confirm.value.length > 0) {
                if (password.value === confirm.value && password.value.length > 0) {
                    matchMsg.innerHTML = '✓ Las contraseñas coinciden';
                    matchMsg.style.color = '#10b981';
                } else {
                    matchMsg.innerHTML = '✗ Las contraseñas no coinciden';
                    matchMsg.style.color = '#ef4444';
                }
            } else {
                matchMsg.innerHTML = '';
            }
        }
        
        // Event listeners
        password.addEventListener('keyup', checkPasswordStrength);
        confirm.addEventListener('keyup', checkMatch);
        password.addEventListener('keyup', checkMatch);
        
        // Validación inicial
        checkPasswordStrength();
    </script>
    
    <style>
        .requirement.valid {
            color: #10b981;
        }
        .requirement.invalid {
            color: #ef4444;
        }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
        button:disabled:hover {
            transform: none !important;
        }
    </style>
    
</body>
</html>