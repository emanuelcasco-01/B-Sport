-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 09-04-2026 a las 23:45:29
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `b_sport`
--

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `sp_login_usuario`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_usuario` (IN `p_identificador` VARCHAR(100))   BEGIN
    -- 1. Buscamos al usuario
    SELECT u.id_usuario, u.nombre, u.apellido, u.password_hash, u.email, r.nombre AS rol
    FROM usuario u
    JOIN rol r ON u.id_rol = r.id_rol
    WHERE (u.username = p_identificador OR u.email = p_identificador)
    AND u.estado = TRUE;

    -- 2. Actualizamos la fecha de acceso
    UPDATE usuario 
    SET ultimo_acceso = NOW() 
    WHERE username = p_identificador OR email = p_identificador;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

DROP TABLE IF EXISTS `articulo`;
CREATE TABLE IF NOT EXISTS `articulo` (
  `id_articulo` int NOT NULL AUTO_INCREMENT,
  `codigo_barra` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('PRODUCTO','INSUMO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_producido` tinyint(1) DEFAULT '0',
  `id_categoria` int DEFAULT NULL,
  `id_marca` int DEFAULT NULL,
  `id_unidad` int DEFAULT NULL,
  `id_iva` int DEFAULT NULL,
  `precio_compra` decimal(12,2) DEFAULT '0.00',
  `precio_venta` decimal(12,2) DEFAULT '0.00',
  `stock_minimo` decimal(12,2) DEFAULT '5.00',
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_articulo`),
  UNIQUE KEY `codigo_barra` (`codigo_barra`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_marca` (`id_marca`),
  KEY `id_unidad` (`id_unidad`),
  KEY `id_iva` (`id_iva`),
  KEY `idx_articulo_nombre` (`nombre`),
  KEY `idx_articulo_codigo` (`codigo_barra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `articulo`
--
DROP TRIGGER IF EXISTS `tr_auditoria_cambio_precio`;
DELIMITER $$
CREATE TRIGGER `tr_auditoria_cambio_precio` AFTER UPDATE ON `articulo` FOR EACH ROW BEGIN
    IF OLD.precio_venta <> NEW.precio_venta THEN
        INSERT INTO auditoria (
            id_usuario, 
            tabla_afectada, 
            id_registro_afectado, 
            accion, 
            valor_anterior, 
            valor_nuevo
        )
        VALUES (
            NULL, -- Puedes pasar el ID del usuario desde la app si lo deseas
            'articulo', 
            NEW.id_articulo, 
            'MODIFICAR', 
            JSON_OBJECT('precio', OLD.precio_venta), 
            JSON_OBJECT('precio', NEW.precio_venta)
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo_variante`
--

DROP TABLE IF EXISTS `articulo_variante`;
CREATE TABLE IF NOT EXISTS `articulo_variante` (
  `id_variante` int NOT NULL AUTO_INCREMENT,
  `id_articulo` int NOT NULL,
  `id_talla` int NOT NULL,
  `id_color` int NOT NULL,
  `stock_actual` decimal(12,2) DEFAULT '0.00',
  PRIMARY KEY (`id_variante`),
  KEY `id_talla` (`id_talla`),
  KEY `id_color` (`id_color`),
  KEY `idx_variante_producto` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria`
--

DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE IF NOT EXISTS `auditoria` (
  `id_auditoria` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `tabla_afectada` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_registro_afectado` int DEFAULT NULL,
  `accion` enum('INSERTAR','MODIFICAR','ELIMINAR') COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor_anterior` json DEFAULT NULL,
  `valor_nuevo` json DEFAULT NULL,
  `fecha_hora` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_auditoria`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

DROP TABLE IF EXISTS `banco`;
CREATE TABLE IF NOT EXISTS `banco` (
  `id_banco` int NOT NULL AUTO_INCREMENT,
  `nombre_banco` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_banco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

DROP TABLE IF EXISTS `caja`;
CREATE TABLE IF NOT EXISTS `caja` (
  `id_caja` int NOT NULL AUTO_INCREMENT,
  `nombre_caja` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_caja`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja_apertura_cierre`
--

DROP TABLE IF EXISTS `caja_apertura_cierre`;
CREATE TABLE IF NOT EXISTS `caja_apertura_cierre` (
  `id_apertura` int NOT NULL AUTO_INCREMENT,
  `id_caja` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha_apertura` datetime DEFAULT CURRENT_TIMESTAMP,
  `monto_apertura` decimal(12,2) NOT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `monto_cierre_real` decimal(12,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA') COLLATE utf8mb4_unicode_ci DEFAULT 'ABIERTA',
  PRIMARY KEY (`id_apertura`),
  KEY `id_caja` (`id_caja`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `ruc_ci` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `ruc_ci` (`ruc_ci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

DROP TABLE IF EXISTS `color`;
CREATE TABLE IF NOT EXISTS `color` (
  `id_color` int NOT NULL AUTO_INCREMENT,
  `nombre_color` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_color`),
  UNIQUE KEY `nombre_color` (`nombre_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

DROP TABLE IF EXISTS `compra`;
CREATE TABLE IF NOT EXISTS `compra` (
  `id_compra` int NOT NULL AUTO_INCREMENT,
  `id_proveedor` int NOT NULL,
  `id_usuario` int NOT NULL,
  `numero_factura` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `condicion` enum('CONTADO','CREDITO') COLLATE utf8mb4_unicode_ci DEFAULT 'CONTADO',
  `total` decimal(12,2) NOT NULL,
  `estado` enum('ACTIVA','ANULADA') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVA',
  PRIMARY KEY (`id_compra`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_compra_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

DROP TABLE IF EXISTS `detalle_compra`;
CREATE TABLE IF NOT EXISTS `detalle_compra` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_compra` int NOT NULL,
  `id_articulo` int NOT NULL,
  `id_variante` int DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_compra` (`id_compra`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_variante` (`id_variante`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `detalle_compra`
--
DROP TRIGGER IF EXISTS `tr_actualizar_stock_compra`;
DELIMITER $$
CREATE TRIGGER `tr_actualizar_stock_compra` AFTER INSERT ON `detalle_compra` FOR EACH ROW BEGIN
    -- Aumenta el stock de la variante comprada
    UPDATE articulo_variante 
    SET stock_actual = stock_actual + NEW.cantidad
    WHERE id_variante = NEW.id_variante;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encargado`
--

DROP TABLE IF EXISTS `encargado`;
CREATE TABLE IF NOT EXISTS `encargado` (
  `id_encargado` int NOT NULL AUTO_INCREMENT,
  `nombre_apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('INTERNO','EXTERNO') COLLATE utf8mb4_unicode_ci DEFAULT 'INTERNO',
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_encargado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapa_produccion`
--

DROP TABLE IF EXISTS `etapa_produccion`;
CREATE TABLE IF NOT EXISTS `etapa_produccion` (
  `id_etapa` int NOT NULL AUTO_INCREMENT,
  `nombre_etapa` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_etapa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva`
--

DROP TABLE IF EXISTS `iva`;
CREATE TABLE IF NOT EXISTS `iva` (
  `id_iva` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id_iva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

DROP TABLE IF EXISTS `marca`;
CREATE TABLE IF NOT EXISTS `marca` (
  `id_marca` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_marca`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE IF NOT EXISTS `metodo_pago` (
  `id_metodo_pago` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_metodo_pago`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo_menu`
--

DROP TABLE IF EXISTS `modulo_menu`;
CREATE TABLE IF NOT EXISTS `modulo_menu` (
  `id_menu` int NOT NULL AUTO_INCREMENT,
  `nombre_menu` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `url` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_padre` int DEFAULT NULL,
  `orden` int DEFAULT '0',
  `id_permiso_requerido` int DEFAULT NULL,
  PRIMARY KEY (`id_menu`),
  KEY `id_padre` (`id_padre`),
  KEY `id_permiso_requerido` (`id_permiso_requerido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

DROP TABLE IF EXISTS `permisos`;
CREATE TABLE IF NOT EXISTS `permisos` (
  `id_permiso` int NOT NULL AUTO_INCREMENT,
  `nombre_permiso` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `nombre_permiso` (`nombre_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`id_permiso`, `nombre_permiso`, `descripcion`) VALUES
(1, 'MENU_SEGURIDAD', 'Acceso a usuarios y configuración'),
(2, 'MENU_COMPRAS', 'Acceso a proveedores y compras'),
(3, 'MENU_PRODUCCION', 'Acceso a fábrica y artículos'),
(4, 'MENU_VENTAS', 'Acceso a facturación y caja'),
(5, 'MODULO_REPORTES', 'Acceso a estadísticas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
CREATE TABLE IF NOT EXISTS `proveedor` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon_social` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `ruc` (`ruc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_cabecera`
--

DROP TABLE IF EXISTS `receta_cabecera`;
CREATE TABLE IF NOT EXISTS `receta_cabecera` (
  `id_receta` int NOT NULL AUTO_INCREMENT,
  `id_producto_final` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad_producida` int DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_receta`),
  UNIQUE KEY `id_producto_final` (`id_producto_final`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_detalle`
--

DROP TABLE IF EXISTS `receta_detalle`;
CREATE TABLE IF NOT EXISTS `receta_detalle` (
  `id_receta` int NOT NULL,
  `id_insumo` int NOT NULL,
  `cantidad_necesaria` decimal(12,2) NOT NULL,
  `porcentaje_desperdicio` decimal(5,2) DEFAULT '0.00',
  `id_unidad` int DEFAULT NULL,
  PRIMARY KEY (`id_receta`,`id_insumo`),
  KEY `id_insumo` (`id_insumo`),
  KEY `id_unidad` (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recuperacion_password`
--

DROP TABLE IF EXISTS `recuperacion_password`;
CREATE TABLE IF NOT EXISTS `recuperacion_password` (
  `id_recuperacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_solicitud` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) DEFAULT '0',
  `ip_solicitud` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_recuperacion`),
  UNIQUE KEY `token` (`token`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recuperacion_password`
--

INSERT INTO `recuperacion_password` (`id_recuperacion`, `id_usuario`, `token`, `fecha_solicitud`, `fecha_expiracion`, `usado`, `ip_solicitud`) VALUES
(1, 5, '7f3d8f3f20b120fd5ef4cbbab520ad66c30ebf907a915292371691b751466c1c', '2026-04-09 08:18:06', '2026-04-09 08:33:06', 1, '::1'),
(2, 5, '962fcf6db618ea453cf62e94f8ef6cfda45523e47779b3210850e977e4fb02cd', '2026-04-09 08:48:25', '2026-04-09 09:03:25', 0, '::1'),
(3, 5, 'aa1d0970e28169305097cdd2e7a17bef6ab1640c202a569535bf359177f45b27', '2026-04-09 08:51:35', '2026-04-09 09:06:35', 0, '::1'),
(4, 5, '1ae703e8e1550f840b0b039482ae9a077ac27ad16858be9628abf0544cc585ec', '2026-04-09 13:46:17', '2026-04-09 14:01:17', 0, '::1'),
(5, 5, 'da71f7b7e18b26220de3379601b65dce3addd29abecb5b17517a55e3d5c5d0fb', '2026-04-09 14:07:54', '2026-04-09 14:22:54', 1, '::1'),
(6, 5, '5095c2119ea50f82c53b5007c2e6c7e57e7760595c670248b4622adc596c5153', '2026-04-09 20:42:17', '2026-04-09 20:57:17', 1, '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Administrador', 'Acceso total al sistema', 1),
(3, 'Cajero', 'Acceso al menu de ventas', 1),
(4, 'Gerente', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles_permisos`
--

DROP TABLE IF EXISTS `roles_permisos`;
CREATE TABLE IF NOT EXISTS `roles_permisos` (
  `id_rol` int NOT NULL,
  `id_permiso` int NOT NULL,
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `id_permiso` (`id_permiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles_permisos`
--

INSERT INTO `roles_permisos` (`id_rol`, `id_permiso`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(3, 4),
(1, 5),
(4, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesion`
--

DROP TABLE IF EXISTS `sesion`;
CREATE TABLE IF NOT EXISTS `sesion` (
  `id_sesion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` datetime DEFAULT NULL,
  `activa` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_sesion`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sesion`
--

INSERT INTO `sesion` (`id_sesion`, `id_usuario`, `token`, `fecha_inicio`, `fecha_expiracion`, `activa`) VALUES
(1, 3, '583bfa43d5657969770e983f1c1e7ffafc07434afc2e987f0853a32dd003c5e3', '2026-04-07 09:01:43', '2026-04-07 09:02:43', 1),
(2, 3, 'd68feb63e71328e2fd2dfbb52db78d54312443136923fea2ab757a5bb1954f14', '2026-04-07 09:18:22', '2026-04-07 09:19:22', 1),
(3, 3, 'a122f127a8267c6e924609e6ec7dda8b05c37bb7d9528b2539cd6b198f95c44b', '2026-04-07 09:19:48', '2026-04-07 09:20:48', 1),
(4, 3, 'b77f97a3bfe989a34eea8d68c1ad2fdac442f4465503e30c50f786c86f5f747e', '2026-04-07 09:25:24', '2026-04-07 09:26:24', 1),
(5, 3, '00eb180c0fec1635989f12ca4ac1e8a38aef6ba46276ac8e1aa9381044b6dff1', '2026-04-07 09:34:31', '2026-04-07 09:35:31', 1),
(6, 3, '4b305677e18e85864b1c48b5bf5c4e6dc92f08c04f422a0318f428a96f1aebb5', '2026-04-07 09:36:59', '2026-04-07 10:06:59', 1),
(7, 3, 'a5b4afdb938226ebf672c0ec9085ac2939da234b6803888961909067bc6edeae', '2026-04-07 10:11:08', '2026-04-07 10:41:08', 1),
(8, 3, 'd44956536fd85a9706b5c325b22a8fbfe1fc3eec0027dda0f3e7f2bc879332c8', '2026-04-07 13:19:54', '2026-04-07 13:49:54', 0),
(9, 3, '4067cc1df41d6b28a9523b5f399c6408e1c50cdcd63ef7a63f47fc0f72b2452e', '2026-04-07 13:54:52', '2026-04-07 14:24:52', 0),
(10, 3, '1998bba0dbe3eb1f46440bb3c9cb950e7a85fef7927b6040b07a927c5d101e8a', '2026-04-07 13:55:32', '2026-04-07 14:25:32', 1),
(11, 3, '89e42a21dd740215889ab9cb782b7d64d786e35fd8cde77c4a295a93e529696d', '2026-04-07 14:26:09', '2026-04-07 14:56:09', 1),
(12, 3, '879e4481007e2f0c4f63545de469b6f79501290b20f85ed70d82e0262b220bc0', '2026-04-07 18:21:18', '2026-04-07 18:51:18', 0),
(13, 3, 'b80f2ceddbfc75843056c5569a84f0191869403760f4c7a7536dcc4d1b67e62b', '2026-04-07 18:24:54', '2026-04-07 18:54:54', 1),
(14, 3, '15bf9380ba4c7c34c9d30e4645ea09555a2eb0da9a62056ee93a7df25e0d9dd3', '2026-04-07 18:25:46', '2026-04-07 18:55:46', 1),
(15, 3, '66f6444a101b468c3e197f74254946afaabc33d0496d8deebb2a34451cf0d977', '2026-04-07 19:08:38', '2026-04-07 19:38:38', 1),
(16, 3, 'c5e68644470fe7ac7bc250df8241f5a16bf0b6774b9c22274697638afccbfcb0', '2026-04-07 19:21:07', '2026-04-07 19:51:07', 1),
(17, 3, 'f1d4377ba5251393e943f412fe97fc5190307ae81ee93abba99aadc30223c7a0', '2026-04-07 23:09:49', '2026-04-07 23:39:49', 0),
(18, 3, '2e772c457eb18c0cb485c1390abf0c16247e399bb591366718d2454d946607cf', '2026-04-07 23:20:27', '2026-04-07 23:50:27', 1),
(19, 3, 'df752e5b527d4cc49764e760f41845c6340637c8ced4961a716861937e381d0d', '2026-04-08 08:32:31', '2026-04-08 09:02:31', 1),
(20, 3, '94571d4a8bcea0165f79ea0793f8969a81897b1c505af0dd6015f4f132adcddd', '2026-04-08 09:07:16', '2026-04-08 09:37:16', 1),
(21, 3, '1ecd7e06b126bdd542655368cf0f23b02e2570b62edb60607be0edbf14980ffc', '2026-04-08 09:37:50', '2026-04-08 10:07:50', 1),
(22, 3, '6df16b8c440e818a728b5345d030e759c5430344dee96be999082f23f8367409', '2026-04-08 10:12:29', '2026-04-08 10:42:29', 1),
(23, 3, '4ae05ce864736322d32076b162dae86ea876c800ec604713eedfd805d24262ff', '2026-04-08 13:17:17', '2026-04-08 13:47:17', 1),
(24, 3, 'c66138d70170361e4ca0bbced7308a38d935e31db8780c6e28729ff095c75611', '2026-04-08 13:50:36', '2026-04-08 14:20:36', 0),
(25, 4, 'a3d24fb8fedaf572d3749514c3f02eb30853397ba932893754d25e2c31575cda', '2026-04-08 14:20:10', '2026-04-08 14:50:10', 0),
(26, 3, '4dbc1002a365e7f4bb6abd5675aefc78ecf89e13873118641c6e10eead041da3', '2026-04-08 14:20:54', '2026-04-08 14:50:54', 0),
(27, 4, '91573112150a7d5f90bff853f76ecca6e5f2f5e44b9ac20f46c3355add57e028', '2026-04-08 14:21:20', '2026-04-08 14:51:20', 0),
(28, 3, 'c6f9eb35e3c363badf19dcc5bc4ca0f298aa35763ae2534df56d0bb7a9496e66', '2026-04-08 14:38:17', '2026-04-08 15:08:17', 0),
(29, 4, '4149df42de92b5943fa40061d3d50213f3fa344535bee73ea14cc23c2a3942c9', '2026-04-08 14:38:30', '2026-04-08 15:08:30', 0),
(30, 3, '561458498f8909ced60e7219bde7b21382c4980fb868008aca69927b98df8909', '2026-04-08 17:47:26', '2026-04-08 18:18:02', 0),
(31, 4, '276db3d109a44bf6c781dcda8dabe30a5aad2d4d877bc8ccb47a72c9c479231b', '2026-04-08 17:48:23', '2026-04-08 18:18:23', 0),
(32, 3, 'd4d25ac334f6739b46b06841ac8d7ae12bdab819455a831a5c7e2b433cefc01d', '2026-04-08 20:15:09', '2026-04-08 20:48:34', 0),
(33, 3, '71081e868879ade4e8a8547f5de48a2a5f703a16a3b3541def6e761e70bbfa37', '2026-04-08 20:19:58', '2026-04-08 20:50:20', 1),
(34, 3, 'f1dcf1d4772d8ea852bd04f5ab7896b2c074d78ec9bba5908d435920473b5f13', '2026-04-08 20:20:42', '2026-04-08 20:50:52', 1),
(35, 3, '117219cba8b70f7a669bc40f22b524494e9ee159dfeabd7bb9b5aed2dbad3bf6', '2026-04-08 20:21:44', '2026-04-08 21:00:00', 0),
(36, 3, 'c2f260c84a3fef918b9cefc0afc4d5ce54ba47ffee04b41469cfdc9df97082bc', '2026-04-08 20:38:35', '2026-04-08 21:19:27', 1),
(37, 3, 'dbc5b7b9c0036c25deaebf74ab8ec567de6ade39312d8d5ee876be401b4dea94', '2026-04-09 08:15:58', '2026-04-09 08:47:13', 0),
(38, 5, '653706019682ae67438a8e615c8a8ae5a496c2aae9348a00fb7d9bb07b808e83', '2026-04-09 08:17:54', '2026-04-09 08:47:54', 0),
(39, 5, 'd1ff7d26f4901b647d558c292949b14c671c48b40317217c8cfac383966c9cce', '2026-04-09 08:20:50', '2026-04-09 08:50:50', 1),
(40, 5, 'f0feb49e75fd5ff27441eaf1f07b8156a6027414efc91f2d5a8839dc62c14f08', '2026-04-09 08:20:50', '2026-04-09 08:53:09', 0),
(41, 5, 'bcfe7dd37f746418a24140b09e7ab915ce46530b1fd79ca52de65684052d0259', '2026-04-09 08:40:39', '2026-04-09 09:10:39', 1),
(42, 4, '62b709f774882db83a3cc8f1fa7215bf9a218bfaee3b0881e17b4035ebefa63f', '2026-04-09 08:42:07', '2026-04-09 09:12:07', 0),
(43, 3, '7d62b9660e1e03bdf728c3cc9e9d3f3ba17958a6258d3140ac9a7f2f21d98731', '2026-04-09 08:42:32', '2026-04-09 09:12:32', 0),
(44, 5, 'ee677e09bc162c086534f540012512f11e61256da0526b730d8a1f74c18aeb36', '2026-04-09 08:50:29', '2026-04-09 09:20:31', 0),
(45, 5, '5b824639ae78d90c87e8b2636662924399a0d5eaf2c8f933e9984a3e807fe130', '2026-04-09 10:01:19', '2026-04-09 10:31:20', 0),
(46, 5, 'f54725f0bc6742b7f9e3f4c4d081955ed93ae039f6fd4f5ed3cfe18ec95f69e7', '2026-04-09 10:09:16', '2026-04-09 10:39:16', 0),
(47, 5, '206457bf64019f4f48856471680350a6d99accca613607c1a1d335bc0f30712c', '2026-04-09 10:09:27', '2026-04-09 10:39:27', 0),
(48, 5, '80a78b5e32acb10b2cbb05cfffeefc0f4dccd06ca74026a6908266552ce7eb3c', '2026-04-09 10:09:45', '2026-04-09 10:39:45', 0),
(49, 5, '623af40ab00c16e9fa30c6d3727ba30154176e9bbdfc92174b7e531c79451a67', '2026-04-09 10:10:17', '2026-04-09 10:40:17', 0),
(50, 5, '961a69e23a6bb300117b1b8ba4af9d32a5e4da1a02bc6ed31a8044b03c1362f6', '2026-04-09 10:10:37', '2026-04-09 10:43:38', 0),
(51, 3, '189091e4e77c40d284a4c96866fc20914a451aa4c6581c997ed79e5bb17a772d', '2026-04-09 13:42:30', '2026-04-09 14:14:34', 0),
(52, 3, '483eb502f6887111990768a22bb13795d213362343e2309eefbc97a88ef64f70', '2026-04-09 13:52:44', '2026-04-09 14:31:53', 0),
(53, 6, 'de25bcd92483d3cee2ada0c6e63f5457ffe29b74e2dd7109769b81cbd39e41a2', '2026-04-09 14:02:19', '2026-04-09 14:32:59', 0),
(54, 3, 'c8938b5fc5612fd5909d2f4c6ff0a76e8be66e3f1e293b5fd18dc7993e983417', '2026-04-09 14:06:43', '2026-04-09 14:36:43', 0),
(55, 6, '65afc6c660b5d84cf0cd4f6b4e1e0d2e2dd7c8da1bb7a3b47d17c13d284e3cb8', '2026-04-09 14:07:38', '2026-04-09 14:37:38', 0),
(56, 5, '5a4b5fbc7601ae7b956fd1fb1be4f0f246c9976e49ed50cc01ffe6950d503afa', '2026-04-09 14:10:14', '2026-04-09 14:40:14', 0),
(57, 5, '60d2ebfe53c00136b49714975c963e61b9be079d1ed050a56f4e96d9c7dfcfd5', '2026-04-09 14:11:03', '2026-04-09 14:41:03', 0),
(58, 5, '3fa7fde7ee6b2f951489b622f123c2de7d4d5e9d0e506b67251c521fa747a717', '2026-04-09 14:12:53', '2026-04-09 14:42:53', 0),
(59, 5, 'f39ad154b6471012b9f4964ef61df07f246abb2fa2f3dbb0231c2800895549b6', '2026-04-09 14:13:13', '2026-04-09 14:43:13', 0),
(60, 5, 'a9f21da612160dd0cabbe6d79e68f1d24c4abd3d1c32337ded868c3274d87ee4', '2026-04-09 14:15:48', '2026-04-09 14:53:50', 0),
(61, 5, 'a84f8eb16fe91ed2836c021b73f212dc2f3a033e5cedf467f457c541c5e29dc7', '2026-04-09 14:24:49', '2026-04-09 14:54:49', 0),
(62, 5, '71ec3c9a8a4319bfff06a2d20c7aa1435767a73aa9f9eb6aaa3bc34986d260dd', '2026-04-09 14:34:52', '2026-04-09 15:04:53', 0),
(63, 5, '0b50a73e1bf71eda14e9374a3e3d146fd8ffd5d54c321183fd32541bff840394', '2026-04-09 14:35:08', '2026-04-09 15:05:09', 0),
(64, 5, 'f0b87474878174c24b2a93434cf680a58be1127c60c764112e9c68186c10eaee', '2026-04-09 14:35:21', '2026-04-09 15:05:22', 0),
(65, 5, 'a6aa3133a911a980518ebd84f8fcabdea4ac3f738df479d5dbed6c6ce5ab3653', '2026-04-09 14:47:34', '2026-04-09 15:18:08', 0),
(66, 6, 'ee4602e177732d0ce4fed02102ff8ed8fc690a58d942fcdbc55c2dbebacbcbeb', '2026-04-09 14:49:54', '2026-04-09 15:19:55', 0),
(67, 5, 'c89ca19c71c6ded8f4172daa2ec01ccdbb987796069541c1d3b14bc79bd32538', '2026-04-09 14:50:15', '2026-04-09 15:20:16', 0),
(68, 5, '32333d315d937cd2009e64284f2439e0f133f72f32365264a8beb07136311c22', '2026-04-09 14:51:39', '2026-04-09 14:56:31', 1),
(69, 5, 'd5e0a11260741701e460efa74f1818d45b375c1cd4ac91d34a2e4c7aac76e98f', '2026-04-09 14:57:15', '2026-04-09 14:58:17', 0),
(70, 5, '1352d12105041929bf31a538009013025df9b94d749c6b86257e467f56814cd4', '2026-04-09 15:02:15', '2026-04-09 15:05:05', 0),
(71, 5, 'd19ce83ecb94d81dc4663893dee4305f5035c0a5737dc2f9dcaf7634be675cfb', '2026-04-09 15:15:18', '2026-04-09 15:17:21', 1),
(72, 5, 'ef22d2ac5c148559952f50912ce9a94c2e96863ad430b6a06c2df5011b7a81ba', '2026-04-09 15:22:26', '2026-04-09 15:24:25', 1),
(73, 5, '5dee61557790df224d12539036b40115cffc16cf02015bce4a6211cb0722d20b', '2026-04-09 15:25:15', '2026-04-09 15:27:55', 0),
(74, 5, '1e895422a22a60a66a80ab502e72f6e90ce4f3f05631622521cb895cf424b16a', '2026-04-09 17:42:38', '2026-04-09 17:43:53', 1),
(75, 5, 'fde884c35005310c33a967d592fd0f085293152d0362eca05baebe3cb47ee458', '2026-04-09 17:45:55', '2026-04-09 18:33:04', 0),
(76, 5, '6085652f35b05adf9911a4e8a31565dbc829fabec3c115992fc980cd72db8009', '2026-04-09 18:14:38', '2026-04-09 19:11:44', 0),
(77, 5, '503ed577b54640db202a0f0a9e0f06d575440bd27dae76ae6da9936f9faba3f3', '2026-04-09 19:07:17', '2026-04-09 19:41:06', 0),
(78, 5, '9f10da3216f9c522b9e2fdde3770907ca7641d2b8e64a16a9f70bf98cd5df5a7', '2026-04-09 19:14:58', '2026-04-09 19:48:21', 0),
(79, 5, '608e6af89eacf2a0401df164cda723f0b67351372f9fe9c6d67ee2c9ffbfbcd9', '2026-04-09 19:24:27', '2026-04-09 19:57:32', 0),
(80, 5, 'a3c25467e65ddc3ac754c9b23e231b20c76348e01130337c7afe4ef480745c6e', '2026-04-09 19:31:57', '2026-04-09 20:22:44', 0),
(81, 5, 'aee391f31937251966805595ce21dadb570712693b40b5ed60a4b7ad2d79e43c', '2026-04-09 20:00:53', '2026-04-09 20:34:58', 0),
(82, 6, '8b63e583bace35795dcc12ecabff80b3952a0d97ddab82638b5d88a090054596', '2026-04-09 20:05:25', '2026-04-09 20:48:15', 0),
(83, 5, 'af59752bb9fd42f0fa01fc9833d2b1e6715438bc81f3adf5741c4527cf425805', '2026-04-09 20:18:31', '2026-04-09 20:48:38', 0),
(84, 5, 'e2cb750fb5d84da6e771c8c8b49b9b0057ac509596455f659f0963629a987359', '2026-04-09 20:23:27', '2026-04-09 20:53:37', 0),
(85, 5, 'f7e3d083e8b06c33a94e0ae3f18eaf1157e7999179fca326464dfffb33b3ea49', '2026-04-09 20:25:35', '2026-04-09 20:26:06', 1),
(86, 5, '042421cb4059c083be3a4121c0a24dea939c08181e8868e0c7f6ae93d089b205', '2026-04-09 20:36:57', '2026-04-09 20:37:51', 1),
(87, 5, '65d3d83a0530b1b2dd2edd9d715ffa0e01861e45644c691063928a313c011c46', '2026-04-09 20:39:15', '2026-04-09 21:09:18', 0),
(88, 5, '14e3ab81da87ddcbd0a2e17630cc10f333668733095dfb4310b1a41b0a540e3c', '2026-04-09 20:41:30', '2026-04-09 21:11:34', 0),
(89, 5, '9b3a41de587b30ae87e9ed6947f2151a261e9a1138939dd4211b68d68910665a', '2026-04-09 20:43:18', '2026-04-09 21:13:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `talla`
--

DROP TABLE IF EXISTS `talla`;
CREATE TABLE IF NOT EXISTS `talla` (
  `id_talla` int NOT NULL AUTO_INCREMENT,
  `nombre_talla` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_talla`),
  UNIQUE KEY `nombre_talla` (`nombre_talla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
CREATE TABLE IF NOT EXISTS `unidad_medida` (
  `id_unidad` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `abreviatura` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_rol` int NOT NULL,
  `estado` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` datetime DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  KEY `idx_usuario_login` (`username`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `username`, `email`, `password_hash`, `id_rol`, `estado`, `fecha_creacion`, `ultimo_acceso`) VALUES
(3, 'Administrador', 'Sistema', 'admins', 'admin@bsport.com', '$2y$10$3EP.4uaMCXOrtKC5floQPu54rfxIhrD8UU5DKK4evjb/IuQzEwy0C', 1, 0, '2026-04-06 17:14:18', NULL),
(4, 'Juan', 'Perez', 'juancito', 'juanperez@gmail.com', '$2y$10$V/fWnCvKBTBAA2Fmh44bueC1ahrXaKFW0MBDXKYB7Elqmq.1JcN3u', 3, 1, '2026-04-08 13:12:57', NULL),
(5, 'Emanuel', 'Casco', 'emanuel', 'emanuelcrolon19@gmail.com', '$2y$10$jTSuQD8YAsRo5th0cC/CxuEptO35pyjSZn4C7aFJ4Pk0fwUFkO7Eu', 1, 1, '2026-04-09 11:17:12', NULL),
(6, 'Mauricio', 'Obregon', 'Marex', 'marexunc2004@gmail.com', '$2y$10$w99VGtNir47NEjDA5jKczOde4Mteib2Nm.ViBGVBUwEEPTfof1zES', 4, 1, '2026-04-09 17:00:06', NULL),
(8, 'Juan', 'Froilan', 'Juanfro', 'juanfro@gmail.com', '$2y$10$svfZCpvgYGcOy8XdnGGYSeFzJXp6Q21JMglYw6RVqtxmVnL1n8zhW', 3, 1, '2026-04-09 22:52:11', NULL),
(9, 'Jonas', 'Ayala', 'jonascito', 'jonas123@gmail.com', '$2y$10$FRWEexHYVzNvwACIyaClmOFtafzS.bhgg3eqy225u8zPr8KDD/Qg2', 3, 1, '2026-04-09 23:38:15', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_cabecera`
--

DROP TABLE IF EXISTS `venta_cabecera`;
CREATE TABLE IF NOT EXISTS `venta_cabecera` (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_apertura` int NOT NULL,
  `condicion` enum('CONTADO','CREDITO') COLLATE utf8mb4_unicode_ci DEFAULT 'CONTADO',
  `nro_factura` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_timbrado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_venta` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_neto_a_pagar` decimal(12,2) NOT NULL,
  `estado` enum('EMITIDA','ANULADA') COLLATE utf8mb4_unicode_ci DEFAULT 'EMITIDA',
  PRIMARY KEY (`id_venta`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_apertura` (`id_apertura`),
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_venta_fecha` (`fecha_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_detalle`
--

DROP TABLE IF EXISTS `venta_detalle`;
CREATE TABLE IF NOT EXISTS `venta_detalle` (
  `id_venta` int NOT NULL,
  `id_articulo` int NOT NULL,
  `id_variante` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `id_iva` int NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_venta`,`id_articulo`,`id_variante`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_variante` (`id_variante`),
  KEY `id_iva` (`id_iva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Disparadores `venta_detalle`
--
DROP TRIGGER IF EXISTS `tr_actualizar_stock_venta`;
DELIMITER $$
CREATE TRIGGER `tr_actualizar_stock_venta` AFTER INSERT ON `venta_detalle` FOR EACH ROW BEGIN
    -- Resta el stock de la variante vendida
    UPDATE articulo_variante 
    SET stock_actual = stock_actual - NEW.cantidad
    WHERE id_variante = NEW.id_variante;
END
$$
DELIMITER ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `articulo_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `articulo_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marca` (`id_marca`),
  ADD CONSTRAINT `articulo_ibfk_3` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`),
  ADD CONSTRAINT `articulo_ibfk_4` FOREIGN KEY (`id_iva`) REFERENCES `iva` (`id_iva`);

--
-- Filtros para la tabla `articulo_variante`
--
ALTER TABLE `articulo_variante`
  ADD CONSTRAINT `articulo_variante_ibfk_1` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE,
  ADD CONSTRAINT `articulo_variante_ibfk_2` FOREIGN KEY (`id_talla`) REFERENCES `talla` (`id_talla`),
  ADD CONSTRAINT `articulo_variante_ibfk_3` FOREIGN KEY (`id_color`) REFERENCES `color` (`id_color`);

--
-- Filtros para la tabla `auditoria`
--
ALTER TABLE `auditoria`
  ADD CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `caja_apertura_cierre`
--
ALTER TABLE `caja_apertura_cierre`
  ADD CONSTRAINT `caja_apertura_cierre_ibfk_1` FOREIGN KEY (`id_caja`) REFERENCES `caja` (`id_caja`),
  ADD CONSTRAINT `caja_apertura_cierre_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `compra_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`),
  ADD CONSTRAINT `compra_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`),
  ADD CONSTRAINT `detalle_compra_ibfk_3` FOREIGN KEY (`id_variante`) REFERENCES `articulo_variante` (`id_variante`);

--
-- Filtros para la tabla `modulo_menu`
--
ALTER TABLE `modulo_menu`
  ADD CONSTRAINT `modulo_menu_ibfk_1` FOREIGN KEY (`id_padre`) REFERENCES `modulo_menu` (`id_menu`),
  ADD CONSTRAINT `modulo_menu_ibfk_2` FOREIGN KEY (`id_permiso_requerido`) REFERENCES `permisos` (`id_permiso`);

--
-- Filtros para la tabla `receta_cabecera`
--
ALTER TABLE `receta_cabecera`
  ADD CONSTRAINT `receta_cabecera_ibfk_1` FOREIGN KEY (`id_producto_final`) REFERENCES `articulo` (`id_articulo`);

--
-- Filtros para la tabla `receta_detalle`
--
ALTER TABLE `receta_detalle`
  ADD CONSTRAINT `receta_detalle_ibfk_1` FOREIGN KEY (`id_receta`) REFERENCES `receta_cabecera` (`id_receta`) ON DELETE CASCADE,
  ADD CONSTRAINT `receta_detalle_ibfk_2` FOREIGN KEY (`id_insumo`) REFERENCES `articulo` (`id_articulo`),
  ADD CONSTRAINT `receta_detalle_ibfk_3` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`);

--
-- Filtros para la tabla `recuperacion_password`
--
ALTER TABLE `recuperacion_password`
  ADD CONSTRAINT `recuperacion_password_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `roles_permisos`
--
ALTER TABLE `roles_permisos`
  ADD CONSTRAINT `roles_permisos_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_permisos_ibfk_2` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sesion`
--
ALTER TABLE `sesion`
  ADD CONSTRAINT `sesion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `venta_cabecera`
--
ALTER TABLE `venta_cabecera`
  ADD CONSTRAINT `venta_cabecera_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `venta_cabecera_ibfk_2` FOREIGN KEY (`id_apertura`) REFERENCES `caja_apertura_cierre` (`id_apertura`),
  ADD CONSTRAINT `venta_cabecera_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `venta_detalle`
--
ALTER TABLE `venta_detalle`
  ADD CONSTRAINT `venta_detalle_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `venta_cabecera` (`id_venta`) ON DELETE CASCADE,
  ADD CONSTRAINT `venta_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`),
  ADD CONSTRAINT `venta_detalle_ibfk_3` FOREIGN KEY (`id_variante`) REFERENCES `articulo_variante` (`id_variante`),
  ADD CONSTRAINT `venta_detalle_ibfk_4` FOREIGN KEY (`id_iva`) REFERENCES `iva` (`id_iva`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
