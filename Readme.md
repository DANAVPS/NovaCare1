# NovaCare CRM - Sistema de Gestión para el Sector Salud

## 📋 Descripción del Proyecto

NovaCare CRM es un sistema integral de gestión diseñado específicamente para el sector salud. Permite administrar pacientes, médicos, EPS, IPS, productos/servicios médicos, órdenes médicas y autorizaciones de manera eficiente y organizada.

### Características Principales

- ✅ **Autenticación segura** de usuarios con sistema de recuperación de contraseña
- ✅ **Gestión de Clientes** (Pacientes, Médicos, EPS, IPS) con CRUD completo
- ✅ **Catálogo de Productos/Servicios** médicos con control de stock
- ✅ **Órdenes Médicas** con seguimiento de estados y productos asociados
- ✅ **Autorizaciones Médicas** automáticas para productos que lo requieran
- ✅ **Notificaciones por correo electrónico** con PHPMailer (aprobación/rechazo)
- ✅ **Dashboard** con estadísticas y accesos rápidos
- ✅ **Arquitectura MVC** limpia y ordenada
- ✅ **Diseño responsive** con Tailwind CSS
- ✅ **Tipografía Gabarito** y colores corporativos (#f51b1c)

---

## 🛠️ Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **PHP** | 7.4+ | Backend y lógica de negocio |
| **MySQL** | 5.7+ | Base de datos |
| **Tailwind CSS** | 3.x | Estilos y diseño responsive |
| **PHPMailer** | 6.x | Envío de correos electrónicos |
| **HTML5** | - | Estructura de vistas |
| **JavaScript** | ES6 | Interactividad en formularios |
| **Apache** | 2.4+ | Servidor web (XAMPP/WAMP) |

---

## 📁 Estructura del Proyecto

```
NovaCare-GIT/
├── app/
│   ├── Controllers/              # Controladores de la aplicación
│   │   ├── AuthController.php    # Autenticación y login
│   │   ├── ClienteController.php # Gestión de clientes (pacientes, médicos, EPS, IPS)
│   │   ├── ProductoController.php # Catálogo de productos/servicios
│   │   ├── OrdenMedicaController.php # Gestión de órdenes médicas
│   │   ├── AutorizacionController.php # Gestión de autorizaciones
│   │   └── DashboardController.php # Panel de control
│   ├── Models/                   # Modelos de datos
│   │   ├── Database.php          # Conexión a base de datos
│   │   ├── UserModel.php         # Modelo de usuarios
│   │   ├── ClienteModel.php      # Modelo de clientes
│   │   ├── ProductoModel.php     # Modelo de productos
│   │   ├── OrdenMedicaModel.php  # Modelo de órdenes médicas
│   │   └── AutorizacionModel.php # Modelo de autorizaciones
│   ├── Views/                    # Vistas (HTML/Blade)
│   │   ├── auth/                 # Vistas de autenticación
│   │   ├── dashboard/            # Vistas del dashboard
│   │   ├── clientes/             # Vistas de gestión de clientes
│   │   ├── productos/            # Vistas de gestión de productos
│   │   ├── ordenes/              # Vistas de gestión de órdenes
│   │   └── autorizaciones/       # Vistas de gestión de autorizaciones
│   └── core/
│       └── Mailer.php            # Clase para envío de correos
├── config/
│   ├── config.php                # Configuración general de la aplicación
│   └── email_config.php          # Configuración de SMTP y correos
├── public/
│   ├── index.php                 # Punto de entrada de la aplicación
│   └── css/                      # Estilos CSS (Tailwind)
├── Imagenes/                     # Carpeta para imágenes del sistema
├── PHPMailer-master/             # Librería de envío de correos
├── novacare_db.sql              # Script de base de datos
└── Readme.md                     # Este archivo
```

---

## 🚀 Instalación y Configuración

### Requisitos Previos

- **PHP** 7.4 o superior
- **MySQL** 5.7 o superior
- **Apache** 2.4+ (o servidor web compatible)
- **Composer** (opcional, para gestionar dependencias)
- **XAMPP**, **WAMP** o **LAMP** instalado

### Pasos de Instalación

#### 1. Clonar o Descargar el Repositorio
```bash
git clone <URL_DEL_REPOSITORIO>
cd NovaCare-GIT
```

#### 2. Configurar la Base de Datos
```bash
# Abrir phpMyAdmin en http://localhost/phpmyadmin
# Crear una nueva base de datos: novacare
# Importar el archivo novacare_db.sql
```

O desde línea de comandos:
```bash
mysql -u root -p novacare < novacare_db.sql
```

#### 3. Configurar los Archivos de Configuración

**config/config.php** - Configuración general:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'novacare');
define('DB_CHARSET', 'utf8mb4');
```

**config/email_config.php** - Configuración de SMTP:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu_correo@gmail.com');
define('SMTP_PASS', 'tu_contraseña_app');
define('MAIL_FROM', 'noreply@novacare.com');
define('MAIL_FROM_NAME', 'NovaCare');
```

#### 4. Verificar Permisos de Carpetas
Asegúrate de que la carpeta `Imagenes/` tiene permisos de escritura:
```bash
# En Linux/Mac
chmod 755 Imagenes/

# En Windows (a través de propiedades)
# Click derecho > Propiedades > Seguridad > Editar
```

#### 5. Acceder a la Aplicación
Abre tu navegador y ve a:
```
http://localhost/NovaCare-GIT/public/
```

---

## 📊 Módulos Principales

### 1. Autenticación (AuthController)
- **Login**: Acceso con usuario y contraseña
- **Recuperación de Contraseña**: Envío de enlace de reset por correo
- **Registro**: Creación de nuevas cuentas
- **Cierre de Sesión**: Logout seguro

### 2. Gestión de Clientes (ClienteController)
- **CRUD Completo**: Crear, leer, actualizar, eliminar clientes
- **Tipos de Clientes**: Pacientes, Médicos, EPS, IPS
- **Búsqueda y Filtrado**: Filtros avanzados por tipo y estado
- **Historial**: Registro de cambios en clientes

### 3. Catálogo de Productos (ProductoController)
- **Gestión de Productos**: CRUD de productos/servicios médicos
- **Control de Stock**: Seguimiento de cantidades disponibles
- **Categorización**: Clasificación por tipo de servicio
- **Precios y Tarifas**: Gestión de costos

### 4. Órdenes Médicas (OrdenMedicaController)
- **Creación de Órdenes**: Vinculación con pacientes y productos
- **Seguimiento de Estado**: Pendiente, Procesada, Completada
- **Asignación de Médicos**: Médico responsable de la orden
- **Historial**: Registro completo de cambios

### 5. Autorizaciones Médicas (AutorizacionController)
- **Solicitud Automática**: Generación automática para productos que lo requieren
- **Aprobación/Rechazo**: Workflow de autorización
- **Notificaciones**: Correos al aprobador y solicitante
- **Trazabilidad**: Registro de quién autoriza y cuándo

### 6. Dashboard (DashboardController)
- **Estadísticas**: Resumen de órdenes, autorizaciones, clientes
- **Accesos Rápidos**: Atajos a módulos principales
- **Gráficos**: Visualización de datos clave
- **Actividad Reciente**: Últimas operaciones realizadas

---

## 📧 Sistema de Notificaciones por Correo

El sistema utiliza **PHPMailer** para enviar notificaciones automáticas:

### Tipos de Notificaciones

| Evento | Destinatario | Contenido |
|--------|--------------|----------|
| Aprobación de Autorización | Solicitante | Detalles de autorización aprobada |
| Rechazo de Autorización | Solicitante | Motivo del rechazo y contacto |
| Nueva Orden Creada | Médico Asignado | Detalles de la orden |
| Orden Completada | Paciente | Confirmación de completud |
| Recuperación de Contraseña | Usuario | Enlace de reset seguro |

### Configuración de SMTP

Para **Gmail**, **Outlook** u otro proveedor SMTP:
1. Habilitar "Contraseñas de aplicación" en tu cuenta de correo
2. Actualizar `config/email_config.php` con credenciales SMTP
3. Verificar que el puerto SMTP sea el correcto (587 para TLS, 465 para SSL)

---

## 🔐 Seguridad

- **Contraseñas Hasheadas**: Uso de `password_hash()` y `password_verify()`
- **Validación de Entrada**: Sanitización de datos con `htmlspecialchars()` y `filter_var()`
- **Protección CSRF**: Tokens de validación en formularios
- **Sesiones Seguras**: Inicio de sesión con validaciones
- **SQL Injection Prevention**: Uso de prepared statements
- **Confidencialidad**: No se almacenan contraseñas en texto plano

---

## 💾 Base de Datos

### Tablas Principales

- **usuarios**: Usuarios del sistema con roles
- **clientes**: Pacientes, médicos, EPS e IPS
- **productos**: Catálogo de servicios/productos médicos
- **ordenes_medicas**: Órdenes creadas con seguimiento
- **autorizaciones**: Solicitudes y aprobaciones de autorización
- **ordenes_productos**: Relación entre órdenes y productos

Ejecuta `novacare_db.sql` para crear todas las tablas automáticamente.

---

## 🎨 Personalización

### Colores Corporativos

- **Color Principal**: `#f51b1c` (Rojo)
- **Texto**: Blanco y gris oscuro
- **Fondo**: Blanco y gris claro

Modifica en los archivos CSS de Tailwind según necesites.

### Tipografía

La aplicación utiliza la fuente **Gabarito** para una apariencia moderna y profesional.

---

## 📝 Uso Típico del Sistema

### Flujo de una Orden Médica

1. **Paciente o Médico** solicita servicio médico
2. **Sistema** crea la orden y asigna a un médico
3. **Sistema** verifica si el producto requiere autorización
4. Si es necesaria, **crea solicitud de autorización automáticamente**
5. **Aprobador** revisa y aprueba/rechaza
6. **Sistema** notifica al solicitante por correo
7. **Orden** se marca como completada una vez autorizada

### Flujo de Gestión de Clientes

1. **Crear Cliente**: Acceder a Clientes > Nuevo
2. **Seleccionar Tipo**: Paciente, Médico, EPS o IPS
3. **Completar Datos**: Nombre, contacto, dirección, etc.
4. **Guardar**: El cliente se registra en el sistema
5. **Editar/Eliminar**: Modificaciones futuras según necesites

---

## 🐛 Solución de Problemas

### Error: Base de datos no encontrada
- Verifica que importaste `novacare_db.sql` correctamente
- Confirma credenciales en `config/config.php`

### Error: Correos no se envían
- Valida credenciales SMTP en `config/email_config.php`
- Verifica puerto y protocolo (TLS 587 o SSL 465)
- Comprueba que PHP tiene acceso a la red

### Error: Permisos de archivo denegados
- Asegúrate de que `Imagenes/` tiene permisos 755
- En Windows, verifica permisos en Propiedades

### Sesión no se mantiene
- Comprueba que las cookies están habilitadas en el navegador
- Verifica que `session_start()` se llama en el controlador

---

## 📞 Soporte y Contacto

Para reportar errores, sugerencias o preguntas sobre NovaCare:

- **Email**: soporte@novacare.com
- **Web**: www.novacare.com

---

## 📄 Licencia

Este proyecto está bajo licencia **MIT**. Consulta el archivo `LICENSE` para más detalles.

---


## 📝 Changelog

### Versión 1.0.0 (2026-01-15)
- ✅ Sistema inicial completo
- ✅ CRUD de clientes, productos, órdenes y autorizaciones
- ✅ Sistema de notificaciones por correo
- ✅ Dashboard funcional
- ✅ Autenticación segura

---

## 📚 Recursos Adicionales

- [Documentación PHP Oficial](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Tailwind CSS Docs](https://tailwindcss.com/docs)
- [PHPMailer GitHub](https://github.com/PHPMailer/PHPMailer)

---

**Última actualización**: Abril 2026  
**Versión**: 1.0.0  
**Mantenedor**: Equipo NovaCare
