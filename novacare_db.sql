-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 16:07:34
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `novacare_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autorizaciones`
--

CREATE TABLE `autorizaciones` (
  `id` int(11) NOT NULL,
  `numero_autorizacion` varchar(50) NOT NULL,
  `orden_producto_id` int(11) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_autorizador_id` int(11) DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT current_timestamp(),
  `fecha_autorizacion` datetime DEFAULT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `cantidad_aprobada` int(11) NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','expirada','anulada') DEFAULT 'pendiente',
  `motivo_rechazo` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `autorizado_por` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `autorizaciones`
--

INSERT INTO `autorizaciones` (`id`, `numero_autorizacion`, `orden_producto_id`, `paciente_id`, `medico_autorizador_id`, `fecha_solicitud`, `fecha_autorizacion`, `fecha_expiracion`, `cantidad_aprobada`, `estado`, `motivo_rechazo`, `observaciones`, `autorizado_por`, `created_at`, `updated_at`) VALUES
(7, 'AUTH-2026-0007', 1, 11, 9, '2026-04-01 09:10:00', '2026-04-01 14:00:00', '2026-07-01', 90, 'aprobada', NULL, 'Metformina aprobada por EPS Compensar. Diagnóstico validado.', 1, '2026-04-01 14:10:00', '2026-04-01 19:00:00'),
(8, 'AUTH-2026-0008', 2, 11, 9, '2026-04-01 09:15:00', '2026-04-01 14:05:00', '2026-07-01', 3, 'aprobada', NULL, 'Tiras reactivas — aprobadas para automonitoreo domiciliario.', 1, '2026-04-01 14:15:00', '2026-04-01 19:05:00'),
(9, 'AUTH-2026-0009', 3, 11, 9, '2026-04-01 09:20:00', '2026-04-01 14:10:00', '2026-05-01', 1, 'aprobada', NULL, 'HbA1c basal autorizada. Examen de laboratorio urgente.', 1, '2026-04-01 14:20:00', '2026-04-01 19:10:00'),
(10, 'AUTH-2026-0010', 4, 11, 9, '2026-04-01 09:25:00', '2026-04-02 08:00:00', '2026-10-01', 1, 'aprobada', NULL, 'Glucómetro aprobado. Paciente sin dispositivo de autocontrol.', 1, '2026-04-01 14:25:00', '2026-04-02 13:00:00'),
(11, 'AUTH-2026-0011', 10, 6, 4, '2026-04-14 11:10:00', '2026-04-14 15:00:00', '2026-05-14', 10, 'aprobada', NULL, 'Ibuprofeno 400mg — manejo analgésico agudo aprobado.', 1, '2026-04-14 16:10:00', '2026-04-14 20:00:00'),
(12, 'AUTH-2026-0012', 11, 6, 4, '2026-04-14 11:15:00', '2026-04-14 15:05:00', '2026-05-14', 1, 'aprobada', NULL, 'Consulta médica general — aprobada.', 1, '2026-04-14 16:15:00', '2026-04-14 20:05:00'),
(13, 'AUTH-2026-0013', 12, 13, 9, '2026-04-20 16:10:00', '2026-04-20 18:00:00', '2026-05-20', 10, 'aprobada', NULL, 'Acetaminofén aprobado. Uso sintomático infección respiratoria.', 1, '2026-04-20 21:10:00', '2026-04-20 23:00:00'),
(14, 'AUTH-2026-0014', 14, 13, 9, '2026-04-20 16:15:00', '2026-04-20 18:05:00', '2026-05-20', 1, 'aprobada', NULL, 'Consulta general aprobada. Seguimiento en 7 días.', 1, '2026-04-20 21:15:00', '2026-04-20 23:05:00'),
(15, 'AUTH-2026-0015', 13, 13, 9, '2026-04-21 08:00:00', '2026-04-23 23:53:52', '2026-05-21', 0, 'aprobada', '', 'Pendiente respuesta EPS para ecografía abdominal de Diego.', 1, '2026-04-21 13:00:00', '2026-04-24 04:53:52'),
(16, 'AUTH-2026-0016', 8, 5, 10, '2026-04-10 14:10:00', '2026-04-11 10:00:00', NULL, 0, 'rechazada', 'Falta concepto de nutricionista previo al inicio de estatinas.', 'Se solicita evaluación nutricional antes de autorizar.', 1, '2026-04-10 19:10:00', '2026-04-11 15:00:00'),
(17, 'AUTH-2026-0017', 8, 5, 10, '2026-04-18 09:00:00', '2026-04-24 00:16:35', '2026-05-18', 0, 'aprobada', NULL, 'Re-solicitud con concepto nutricional adjunto. En revisión.', 1, '2026-04-18 14:00:00', '2026-04-24 05:16:35'),
(18, 'AUTH-2026-0018', 5, 12, 4, '2026-04-05 10:40:00', NULL, '2026-07-05', 0, 'pendiente', NULL, 'Solicitada autorización Losartán EPS Famisanar. Sin respuesta.', NULL, '2026-04-05 15:40:00', '2026-04-05 15:40:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `tipo` enum('EPS','IPS','medico','paciente') NOT NULL,
  `identificacion` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `apellido` varchar(150) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `contacto_nombre` varchar(150) DEFAULT NULL,
  `contacto_telefono` varchar(20) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `tipo`, `identificacion`, `nombre`, `apellido`, `email`, `telefono`, `direccion`, `ciudad`, `departamento`, `contacto_nombre`, `contacto_telefono`, `observaciones`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'EPS', '901234567-1', 'SaludTotal EPS', NULL, 'contacto@saludtotal.com', '6012345678', NULL, 'Bogotá', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(2, 'EPS', '901234567-2', 'Famisanar EPS', NULL, 'contacto@famisanar.com', '6012345679', NULL, 'Bogotá', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(3, 'IPS', '901234567-3', 'Clínica Santa Fe', NULL, 'info@clinicasantafe.com', '6012345680', NULL, 'Bogotá', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(4, 'medico', '12345678', 'Juan Carlos', 'Pérez Rodríguez', 'drjperez@email.com', '3101234567', NULL, 'Medellín', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(5, 'paciente', '98765432', 'María Ana', 'González López', 'santiagoadrianrodriguez377@gmail.com', '3117654321', '', 'Cali', '', NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 05:07:16'),
(6, 'paciente', '87654321', 'Carlos Andrés', 'Martínez Ruiz', 'camartinez@email.com', '3123456789', NULL, 'Barranquilla', NULL, NULL, NULL, NULL, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(7, 'EPS', '901234567-4', 'Compensar EPS', NULL, 'contacto@compensar.com', '6012345681', 'Calle 94 # 15-32', 'Bogotá', 'Cundinamarca', 'Área Afiliaciones', '3011234567', NULL, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(8, 'IPS', '901234567-5', 'Hospital San Vicente', NULL, 'info@sanvicente.com', '6044567890', 'Cra 51D # 62-29', 'Medellín', 'Antioquia', 'Dirección Médica', '3049876543', NULL, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(9, 'medico', '23456789', 'Sandra Milena', 'Torres Vargas', 'drtorres@email.com', '3154321098', 'Cra 27 # 45-10', 'Bucaramanga', 'Santander', NULL, NULL, 'Especialista en endocrinología', 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(10, 'medico', '34567890', 'Roberto Emilio', 'Suárez Mora', 'drsuarez@email.com', '3168765432', 'Av. 39 # 47-65', 'Cali', 'Valle del Cauca', NULL, NULL, 'Médico internista', 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(11, 'paciente', '76543210', 'Luis Fernando', 'Castro Ríos', 'lfcastro@email.com', '3191234567', 'Calle 15 # 8-22', 'Pereira', 'Risaralda', NULL, NULL, 'Paciente diabético tipo 2', 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(12, 'paciente', '65432109', 'Patricia Elena', 'Mejía Ospina', 'pmejia@email.com', '3204567890', 'Transv. 10 # 3-15', 'Manizales', 'Caldas', NULL, NULL, 'Hipertensa en control', 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(13, 'paciente', '54321098', 'Diego Alejandro', 'Rojas Pineda', 'drojas@email.com', '3176543210', 'Calle 34 # 21-05', 'Bogotá', 'Cundinamarca', NULL, NULL, NULL, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_medicas`
--

CREATE TABLE `ordenes_medicas` (
  `id` int(11) NOT NULL,
  `numero_orden` varchar(50) NOT NULL,
  `paciente_id` int(11) NOT NULL,
  `medico_id` int(11) DEFAULT NULL,
  `fecha_orden` date NOT NULL,
  `fecha_expiracion` date DEFAULT NULL,
  `diagnostico` text DEFAULT NULL,
  `estado` enum('pendiente','parcial','completada','anulada','expirada') DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `observaciones` text DEFAULT NULL,
  `total_productos` int(11) DEFAULT 0,
  `total_valor` decimal(12,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_medicas`
--

INSERT INTO `ordenes_medicas` (`id`, `numero_orden`, `paciente_id`, `medico_id`, `fecha_orden`, `fecha_expiracion`, `diagnostico`, `estado`, `prioridad`, `observaciones`, `total_productos`, `total_valor`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'OM-2026-0001', 11, 9, '2026-04-01', '2026-07-01', 'Diabetes mellitus tipo 2 — E11.9. Paciente con HbA1c elevada (8.5%). Inicia manejo farmacológico y seguimiento mensual.', 'parcial', 'alta', 'Paciente colaborador. Requiere educación en hábitos saludables.', 4, 352000.00, 1, '2026-04-01 14:00:00', '2026-04-24 10:00:00'),
(2, 'OM-2026-0002', 12, 4, '2026-04-05', '2026-07-05', 'Hipertensión arterial esencial — I10. Control ambulatorio con Losartán. Solicitud de exámenes de seguimiento.', 'pendiente', 'media', 'Refiere buen cumplimiento del tratamiento previo.', 3, 358000.00, 1, '2026-04-05 15:30:00', '2026-04-24 10:00:00'),
(3, 'OM-2026-0003', 5, 10, '2026-04-10', '2026-07-10', 'Dislipidemia mixta — E78.4. Colesterol total 260 mg/dL. Se inicia Atorvastatina y solicita perfil lipídico control.', 'pendiente', 'media', NULL, 2, 220000.00, 2, '2026-04-10 19:00:00', '2026-04-24 10:00:00'),
(4, 'OM-2026-0004', 6, 4, '2026-04-14', '2026-06-14', 'Dolor osteomuscular agudo — M79.3. Manejo analgésico con Ibuprofeno. Consulta general de seguimiento.', 'completada', 'baja', 'Se indica reposo relativo y fisioterapia.', 2, 88000.00, 3, '2026-04-14 16:00:00', '2026-04-24 10:00:00'),
(5, 'OM-2026-0005', 13, 9, '2026-04-20', '2026-07-20', 'Infección respiratoria alta — J06.9. Manejo sintomático. Ecografía abdominal por hallazgo incidental en TAC previo.', 'pendiente', 'urgente', 'Paciente con antecedente de tabaquismo.', 3, 335000.00, 1, '2026-04-20 21:00:00', '2026-04-24 10:00:00'),
(6, 'OM-2026-0006', 11, 10, '2026-04-22', '2026-07-22', 'Control metabólico diabetes — E11.9. Seguimiento trimestral. Solicita HbA1c, perfil lipídico y ajuste de Metformina.', 'pendiente', 'media', 'Paciente refiere episodios de hipoglucemia leve.', 3, 192000.00, 2, '2026-04-22 13:00:00', '2026-04-24 10:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_productos`
--

CREATE TABLE `ordenes_productos` (
  `id` int(11) NOT NULL,
  `orden_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `cantidad_autorizada` int(11) DEFAULT NULL,
  `cantidad_despachada` int(11) DEFAULT 0,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `iva` decimal(5,2) DEFAULT 19.00,
  `total` decimal(12,2) NOT NULL,
  `estado_autorizacion` enum('pendiente','aprobada','rechazada','parcial') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_productos`
--

INSERT INTO `ordenes_productos` (`id`, `orden_id`, `producto_id`, `cantidad`, `cantidad_autorizada`, `cantidad_despachada`, `precio_unitario`, `subtotal`, `iva`, `total`, `estado_autorizacion`, `observaciones`, `created_at`) VALUES
(1, 1, 7, 90, 90, 90, 12000.00, 1080000.00, 19.00, 1285200.00, 'aprobada', 'Metformina 850mg — 3 meses de suministro', '2026-04-01 14:05:00'),
(2, 1, 11, 3, 3, 3, 65000.00, 195000.00, 19.00, 232050.00, 'aprobada', 'Tiras reactivas para automonitoreo', '2026-04-01 14:05:00'),
(3, 1, 6, 1, 1, 0, 45000.00, 45000.00, 19.00, 53550.00, 'aprobada', 'HbA1c basal antes de iniciar tratamiento', '2026-04-01 14:05:00'),
(4, 1, 10, 1, 1, 0, 180000.00, 180000.00, 19.00, 214200.00, 'aprobada', 'Glucómetro portátil para autocontrol en casa', '2026-04-01 14:05:00'),
(5, 2, 3, 60, NULL, 0, 15000.00, 900000.00, 19.00, 1071000.00, 'pendiente', 'Losartán 50mg — 2 meses', '2026-04-05 15:35:00'),
(6, 2, 5, 1, NULL, 0, 250000.00, 250000.00, 19.00, 297500.00, 'pendiente', 'Ecografía abdominal renal para evaluación renal', '2026-04-05 15:35:00'),
(7, 2, 12, 1, NULL, 0, 95000.00, 95000.00, 19.00, 113050.00, 'pendiente', 'ECG basal antes de ajuste de dosis', '2026-04-05 15:35:00'),
(8, 3, 9, 90, 0, 0, 18000.00, 1620000.00, 19.00, 1927800.00, 'aprobada', 'Atorvastatina 40mg — 3 meses. Requiere autorización', '2026-04-10 19:05:00'),
(9, 3, 13, 1, NULL, 0, 55000.00, 55000.00, 19.00, 65450.00, 'pendiente', 'Perfil lipídico control al mes de inicio', '2026-04-10 19:05:00'),
(10, 4, 2, 10, 10, 10, 8000.00, 80000.00, 19.00, 95200.00, 'aprobada', 'Ibuprofeno 400mg — curso corto 10 días', '2026-04-14 16:05:00'),
(11, 4, 4, 1, 1, 1, 80000.00, 80000.00, 19.00, 95200.00, 'aprobada', 'Consulta de seguimiento a los 7 días', '2026-04-14 16:05:00'),
(12, 5, 1, 10, 10, 10, 5000.00, 50000.00, 19.00, 59500.00, 'aprobada', 'Acetaminofén 500mg manejo sintomático', '2026-04-20 21:05:00'),
(13, 5, 5, 1, NULL, 0, 250000.00, 250000.00, 19.00, 297500.00, 'pendiente', 'Ecografía abdominal — hallazgo incidental', '2026-04-20 21:05:00'),
(14, 5, 4, 1, 1, 0, 80000.00, 80000.00, 19.00, 95200.00, 'aprobada', 'Consulta general de control', '2026-04-20 21:05:00'),
(15, 6, 6, 1, NULL, 0, 45000.00, 45000.00, 19.00, 53550.00, 'pendiente', 'HbA1c trimestral control', '2026-04-22 13:05:00'),
(16, 6, 13, 1, NULL, 0, 55000.00, 55000.00, 19.00, 65450.00, 'pendiente', 'Perfil lipídico control', '2026-04-22 13:05:00'),
(17, 6, 7, 90, NULL, 0, 12000.00, 1080000.00, 19.00, 1285200.00, 'pendiente', 'Metformina 850mg — ajuste de dosis a evaluar', '2026-04-22 13:05:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `tipo` enum('medicamento','insumo','procedimiento','examen','otros') DEFAULT 'medicamento',
  `categoria` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT 0.00,
  `costo_unitario` decimal(12,2) DEFAULT 0.00,
  `iva` decimal(5,2) DEFAULT 19.00,
  `stock_minimo` int(11) DEFAULT 0,
  `stock_actual` int(11) DEFAULT 0,
  `unidad_medida` varchar(50) DEFAULT NULL,
  `requiere_autorizacion` tinyint(4) DEFAULT 0,
  `status` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `codigo`, `nombre`, `tipo`, `categoria`, `descripcion`, `precio_unitario`, `costo_unitario`, `iva`, `stock_minimo`, `stock_actual`, `unidad_medida`, `requiere_autorizacion`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'MED001', 'Acetaminofén 500mg', 'medicamento', '', '', 5000.00, 0.00, 19.00, 0, 0, 'tableta', 0, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 04:29:27'),
(2, 'MED002', 'Ibuprofeno 400mg', 'medicamento', NULL, NULL, 8000.00, 0.00, 19.00, 0, 0, 'tableta', 0, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(3, 'MED003', 'Losartán 50mg', 'medicamento', NULL, NULL, 15000.00, 0.00, 19.00, 0, 0, 'tableta', 1, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(4, 'PROC001', 'Consulta Médica General', 'procedimiento', NULL, NULL, 80000.00, 0.00, 19.00, 0, 0, 'consulta', 0, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(5, 'PROC002', 'Ecografía Abdominal', 'examen', NULL, NULL, 250000.00, 0.00, 19.00, 0, 0, 'estudio', 1, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(6, 'PROC003', 'Hemoglobina Glicosilada', 'examen', NULL, NULL, 45000.00, 0.00, 19.00, 0, 0, 'estudio', 1, 1, NULL, '2026-04-24 03:38:44', '2026-04-24 03:38:44'),
(7, 'MED004', 'Metformina 850mg', 'medicamento', 'Antidiabético', 'Para manejo de diabetes tipo 2', 12000.00, 0.00, 19.00, 50, 200, 'tableta', 1, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(8, 'MED005', 'Enalapril 10mg', 'medicamento', 'Antihipertensivo', 'IECA para hipertensión arterial', 9500.00, 0.00, 19.00, 50, 150, 'tableta', 0, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(9, 'MED006', 'Atorvastatina 40mg', 'medicamento', 'Hipolipemiante', 'Control de colesterol LDL', 18000.00, 0.00, 19.00, 30, 100, 'tableta', 1, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(10, 'INS001', 'Glucómetro portátil', 'insumo', 'Diagnóstico', 'Medición glucosa capilar', 180000.00, 0.00, 19.00, 5, 20, 'unidad', 1, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(11, 'INS002', 'Tiras reactivas glucosa x50', 'insumo', 'Diagnóstico', 'Compatibles con glucómetro INS001', 65000.00, 0.00, 19.00, 20, 80, 'caja', 0, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(12, 'PROC004', 'Electrocardiograma 12 derivaciones', 'procedimiento', 'Cardiología', 'ECG estándar de reposo', 95000.00, 0.00, 19.00, 0, 0, 'estudio', 0, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00'),
(13, 'EXAM001', 'Perfil lipídico completo', 'examen', 'Laboratorio', 'Colesterol total, HDL, LDL, triglicéridos', 55000.00, 0.00, 19.00, 0, 0, 'estudio', 0, 1, 1, '2026-04-24 10:00:00', '2026-04-24 10:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `role` enum('admin','doctor','nurse','receptionist') DEFAULT 'doctor',
  `status` tinyint(4) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `reset_token`, `reset_expires`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Administrador NovaCare', 'admin@novacare.com', '$2y$10$5mAkkNvTW2F9bfnOqf4nC.zSlrYsC3sgAGz6sJwyxaMWFK8AuSLd.', NULL, NULL, 'admin', 1, '2026-04-23 22:23:23', '2026-04-22 05:28:13', '2026-04-24 03:23:23'),
(2, 'Dr. Juan Pérez', 'dr.juan@novacare.com', '$2y$10$YourHashHere', NULL, NULL, 'doctor', 1, NULL, '2026-04-22 05:28:14', '2026-04-22 05:28:14'),
(3, 'Enf. María González', 'maria.gonzalez@novacare.com', '$2y$10$YourHashHere', NULL, NULL, 'nurse', 1, NULL, '2026-04-22 05:28:14', '2026-04-22 05:28:14'),
(5, 'Administrador NovaCare', 'adming@novacare.com', '$2y$10$YourHashHere', NULL, NULL, 'admin', 1, NULL, '2026-04-22 05:29:28', '2026-04-22 05:29:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_login_time` (`login_time`);

--
-- Indices de la tabla `autorizaciones`
--
ALTER TABLE `autorizaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_autorizacion` (`numero_autorizacion`),
  ADD KEY `idx_numero_autorizacion` (`numero_autorizacion`),
  ADD KEY `idx_orden_producto` (`orden_producto_id`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `medico_autorizador_id` (`medico_autorizador_id`),
  ADD KEY `autorizado_por` (`autorizado_por`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_identificacion` (`identificacion`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `ordenes_medicas`
--
ALTER TABLE `ordenes_medicas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero_orden` (`numero_orden`),
  ADD KEY `idx_numero_orden` (`numero_orden`),
  ADD KEY `idx_paciente` (`paciente_id`),
  ADD KEY `idx_medico` (`medico_id`),
  ADD KEY `idx_fecha_orden` (`fecha_orden`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `ordenes_productos`
--
ALTER TABLE `ordenes_productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orden` (`orden_id`),
  ADD KEY `idx_producto` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `created_by` (`created_by`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_reset_token` (`reset_token`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `autorizaciones`
--
ALTER TABLE `autorizaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `ordenes_medicas`
--
ALTER TABLE `ordenes_medicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ordenes_productos`
--
ALTER TABLE `ordenes_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `access_logs`
--
ALTER TABLE `access_logs`
  ADD CONSTRAINT `access_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `autorizaciones`
--
ALTER TABLE `autorizaciones`
  ADD CONSTRAINT `autorizaciones_ibfk_1` FOREIGN KEY (`orden_producto_id`) REFERENCES `ordenes_productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `autorizaciones_ibfk_2` FOREIGN KEY (`paciente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `autorizaciones_ibfk_3` FOREIGN KEY (`medico_autorizador_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `autorizaciones_ibfk_4` FOREIGN KEY (`autorizado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ordenes_medicas`
--
ALTER TABLE `ordenes_medicas`
  ADD CONSTRAINT `ordenes_medicas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `ordenes_medicas_ibfk_2` FOREIGN KEY (`medico_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ordenes_medicas_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ordenes_productos`
--
ALTER TABLE `ordenes_productos`
  ADD CONSTRAINT `ordenes_productos_ibfk_1` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_medicas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ordenes_productos_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
