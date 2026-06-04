# PHPUnit Testing Structure - NovaCare

## Estructura de Carpetas

```
tests/
├── bootstrap.php                           # Archivo de configuración inicial
├── Unit/                                   # Pruebas unitarias
│   └── Models/
│       └── UserModelTest.php              # Tests del modelo de Usuario
└── Integration/                            # Pruebas de integración
    └── Controllers/
        └── AuthControllerTest.php         # Tests del controlador de Auth
```

## Descripción

### Unit Tests (`tests/Unit/`)
Pruebas aisladas de componentes individuales sin dependencias externas.

- **Models/UserModelTest.php**
  - ✅ Registro exitoso de usuario
  - ❌ Error: Email duplicado

### Integration Tests (`tests/Integration/`)
Pruebas que verifican la integración entre múltiples componentes con Mocks.

- **Controllers/AuthControllerTest.php**
  - ✅ Login exitoso con credenciales válidas
  - ❌ Login fallido: Email no encontrado
  - ❌ Login fallido: Contraseña incorrecta

## Ejecución de Pruebas

### Ejecutar todas las pruebas
```bash
./vendor/bin/phpunit
```

### Ejecutar solo pruebas unitarias
```bash
./vendor/bin/phpunit tests/Unit/
```

### Ejecutar solo pruebas de integración
```bash
./vendor/bin/phpunit tests/Integration/
```

### Ejecutar una prueba específica
```bash
./vendor/bin/phpunit tests/Unit/Models/UserModelTest.php
```

### Con cobertura de código
```bash
./vendor/bin/phpunit --coverage-html=coverage/
```

## Configuración

El archivo `phpunit.xml` en la raíz del proyecto configura:
- Ubicación de los tests: `tests/` directory
- Bootstrap: `tests/bootstrap.php`
- Namespaces automáticos
- Directorios a incluir en cobertura: `app/`

## Instalación de PHPUnit

Si aún no tienes PHPUnit instalado:

```bash
composer require --dev phpunit/phpunit ^9.5
```

## Próximos Pasos

1. Reemplazar las implementaciones mock con los modelos y controladores reales
2. Agregar más casos de prueba según sea necesario
3. Integrar pruebas en el pipeline de CI/CD
4. Mantener la cobertura de código por encima del 80%
