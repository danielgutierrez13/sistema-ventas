-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 18-04-2022 a las 04:17:34
-- Versión del servidor: 5.7.36
-- Versión de PHP: 8.1.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema-ventas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, 1, 'BEBIDAS', '2022-04-09 23:27:25', '2022-04-15 03:53:32', 1, 0xde3e0b311d9a428fad3345629cbf8052);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL,
  `tipo_persona_id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `tipo_persona_id`, `tipo_documento_id`, `propietario_id`, `config_id`, `nombre`, `documento`, `direccion`, `telefono`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 2, 1, 1, 1, 'ALFARO NUNURA AARON JESUS', '75545493', 'SECHURA', '987556789', '2022-04-13 01:27:42', '2022-04-14 03:54:36', 1, 0xb9a700b77ce5490e8579bd08e33d8c2d),
(2, 1, 2, 1, 1, 'GUTIERREZ VILLEGAS CESAR DANIEL', '10723148541', 'PIURA', '917845429', '2022-04-14 06:09:23', '2022-04-14 06:09:23', 1, 0xee19daf6c17d4f25b44b7366b4763beb);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `codigo` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`id`, `proveedor_id`, `propietario_id`, `config_id`, `codigo`, `precio`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(3, 1, 1, 1, '1432022224325', '24.50', '2022-04-15 03:43:35', '2022-04-16 05:40:03', 1, 0xa876f1ceeb03451a91a78dd458e4ab88),
(4, 1, 1, 1, '1432022224644', '12.50', '2022-04-15 03:48:06', '2022-04-15 03:48:06', 1, 0xe5a84277e24541af97a8359bb167ad22),
(5, 2, 1, 1, '173202214917', '2000.00', '2022-04-17 06:49:37', '2022-04-17 06:49:37', 1, 0xe5e4ab64f61543ff87bdc97fec355daa);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `alias` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_corto` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`id`, `propietario_id`, `config_id`, `alias`, `nombre`, `nombre_corto`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, NULL, 'Pidia SA', 'Pidia SA', 'Pidia SA', '2022-04-09 21:59:10', '2022-04-17 07:48:37', 1, 0x9dc691a3f2554f0b8bed0551ae87bd97);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_config_menu_menus`
--

CREATE TABLE `config_config_menu_menus` (
  `config_id` int(11) NOT NULL,
  `config_menu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config_config_menu_menus`
--

INSERT INTO `config_config_menu_menus` (`config_id`, `config_menu_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_menu`
--

CREATE TABLE `config_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config_menu`
--

INSERT INTO `config_menu` (`id`, `name`, `route`, `activo`) VALUES
(1, 'Usuario', 'usuario_index', 1),
(2, 'Menu', 'menu_index', 1),
(3, 'Config Menu', 'config_menu_index', 1),
(4, 'Config', 'config_index', 1),
(5, 'Usurio Rol', 'usuario_rol_index', 1),
(6, 'Categoria Productos', 'categoria_index', 1),
(7, 'Compra', 'compra_index', 1),
(8, 'Pedido', 'pedido_index', 1),
(9, 'Marca', 'marca_index', 1),
(10, 'Producto', 'producto_index', 1),
(11, 'Proveedor', 'proveedor_index', 1),
(12, 'Tipo de Documentos', 'tipo_documento_index', 1),
(13, 'Tipo Moneda', 'tipo_moneda_index', 1),
(14, 'Tipo de Pago', 'tipo_pago_index', 1),
(15, 'Tipo Persona', 'tipo_persona_index', 1),
(16, 'Unidad Medida Producto', 'unidad_medida_index', 1),
(17, 'Vendedor', 'vendedor_index', 1),
(18, 'Cliente', 'cliente_index', 1),
(19, 'Pago', 'pago_index', 1),
(20, 'Kardex', 'kardex_index', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `producto_id`, `compra_id`, `propietario_id`, `config_id`, `precio`, `cantidad`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(3, 1, 3, 1, 1, '12.00', '1.00', '2022-04-15 03:43:35', '2022-04-16 05:40:03', 1, 0xb2a6d5fc4ac04711ac5d28e25713d1be),
(4, 1, 4, 1, 1, '12.50', '1.00', '2022-04-15 03:48:06', '2022-04-15 03:48:06', 1, 0xfd629c8b43da435dac7f7aa3fd6f3df4),
(5, 2, 3, NULL, NULL, '12.50', '14.00', '2022-04-16 03:57:07', '2022-04-16 05:40:03', 1, 0x81861a1cad3d4d8595d29de3124266ce),
(6, 2, 5, 1, 1, '2000.00', '500.00', '2022-04-17 06:49:37', '2022-04-17 06:49:37', 1, 0xf48065d90db34495bbd24d1fbfc27310);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido`
--

CREATE TABLE `detalle_pedido` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `descuento` decimal(10,2) NOT NULL,
  `estado_entrega` tinyint(1) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_pedido`
--

INSERT INTO `detalle_pedido` (`id`, `producto_id`, `pedido_id`, `propietario_id`, `config_id`, `cantidad`, `precio`, `descuento`, `estado_entrega`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(6, 1, 4, 1, 1, 1, '6.00', '0.00', 0, '2022-04-16 07:23:18', '2022-04-17 04:51:27', 1, 0x68876ba0ecb243bd8962203258c6082b),
(7, 2, 5, 1, 1, 1, '8.00', '0.00', 1, '2022-04-16 07:39:09', '2022-04-16 08:17:36', 1, 0xfe922e5ab790428fb7e44c46a83b78a4),
(8, 2, 6, 1, 1, 1, '8.00', '0.00', 1, '2022-04-16 07:50:48', '2022-04-16 08:17:22', 1, 0xd3b7c0b1d06d421db39844b6f274eb4b),
(9, 2, 7, 1, 1, 1, '8.00', '0.00', 1, '2022-04-16 08:04:19', '2022-04-17 15:40:41', 1, 0xe98a6b467e5b4ac1b35360cbda1b5db4),
(11, 2, 8, 1, 1, 5, '40.00', '0.00', 0, '2022-04-16 08:08:11', '2022-04-17 05:39:54', 1, 0xfc2bf21874354b249de145109efe15ba),
(15, 2, 9, 1, 1, 200, '1600.00', '0.00', 0, '2022-03-09 08:46:29', '2022-04-17 05:54:02', 1, 0x2aee5bd2b67243aa85c0862a9ce4bfdf),
(16, 2, 9, 1, 1, 2, '16.00', '0.00', 0, '2022-04-16 08:46:29', '2022-04-17 05:54:02', 1, 0xe88a53b1502548e581a3d3516f726b95);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220415052717', '2022-04-15 05:27:21', 531),
('DoctrineMigrations\\Version20220416093832', '2022-04-16 09:38:47', 170),
('DoctrineMigrations\\Version20220416095254', '2022-04-16 09:52:59', 99);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, 1, 'COCA COLA', '2022-04-09 22:46:27', '2022-04-15 03:53:08', 1, 0x32721f382dc049478eecfe6eb6d4a923),
(2, 1, 1, 'INKA COLA', '2022-04-09 22:47:09', '2022-04-15 03:53:18', 1, 0x9edd8191dfdc4682a1f0966b948fc601);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ranking` smallint(6) NOT NULL,
  `badge` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id`, `parent_id`, `propietario_id`, `config_id`, `name`, `route`, `icon`, `ranking`, `badge`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, NULL, 1, 1, 'Configuracion', NULL, 'bi bi-gear-fill', 6, NULL, '2022-04-09 22:00:13', '2022-04-14 03:39:19', 1, 0x003ca9974b55483fa8ffa6d3d109b17a),
(2, 1, 1, 1, 'Menu', 'menu_index', NULL, 0, NULL, '2022-04-09 22:01:42', '2022-04-09 22:01:42', 1, 0x29717d9ed9b14075a58271e9cf74bdc0),
(3, 1, 1, 1, 'Usuario', 'usuario_index', NULL, 0, NULL, '2022-04-09 22:02:23', '2022-04-09 22:02:23', 1, 0x646be9b425f6456f8156f52ac3015e6a),
(4, 1, 1, 1, 'Usuario Rol', 'usuario_rol_index', NULL, 0, NULL, '2022-04-09 22:03:04', '2022-04-09 22:03:04', 1, 0x9db4c1b8f6c84cea9a2646700407951d),
(5, 1, 1, 1, 'Config', 'config_index', NULL, 0, NULL, '2022-04-09 22:03:21', '2022-04-09 22:03:21', 1, 0x3c5819eb3435416e98e728c265d7048e),
(6, 1, 1, 1, 'Config Menu', 'config_menu_index', NULL, 0, NULL, '2022-04-09 22:03:47', '2022-04-09 22:03:47', 1, 0xc87f975edffa4a51957f5eea9e0b82e5),
(7, NULL, 1, 1, 'Compras', NULL, 'bi bi-cart-plus-fill', 2, NULL, '2022-04-14 03:22:24', '2022-04-14 03:42:40', 1, 0x5a50fba5fa90453d9108239a8e87d918),
(8, 7, 1, 1, 'Compra', 'compra_index', NULL, 0, NULL, '2022-04-14 03:23:39', '2022-04-14 03:23:39', 1, 0xb2111b65727346e9890b11486e94d854),
(9, 7, 1, 1, 'Proveedor', 'proveedor_index', NULL, 0, NULL, '2022-04-14 03:24:02', '2022-04-14 03:24:02', 1, 0xf1b53984d3d44c7aa76a6d5c44c8216b),
(10, NULL, 1, 1, 'Productos', NULL, 'bi bi-basket-fill', 3, NULL, '2022-04-14 03:27:18', '2022-04-14 03:42:50', 1, 0xa8f2b4e5d1b2462cb18795dc8acff567),
(11, 10, 1, 1, 'Producto', 'producto_index', NULL, 3, NULL, '2022-04-14 03:27:56', '2022-04-14 03:38:41', 1, 0x269df1f74f91413a9b0d38775ab64584),
(12, 10, 1, 1, 'Categorias', 'categoria_index', NULL, 2, NULL, '2022-04-14 03:29:10', '2022-04-14 03:29:10', 1, 0x9a53369d47f743dda60edf996b5667c6),
(13, 10, 1, 1, 'Marcas', 'marca_index', NULL, 3, NULL, '2022-04-14 03:29:37', '2022-04-14 03:29:37', 1, 0x5118159fa94c4a039c3af6129880098f),
(14, 10, 1, 1, 'Unidad Medida', 'unidad_medida_index', NULL, 4, NULL, '2022-04-14 03:30:05', '2022-04-14 03:30:26', 1, 0x502e089d212f4b8b87c48820a8453f80),
(15, NULL, 1, 1, 'Vendedores', NULL, 'bi bi-person-fill', 4, NULL, '2022-04-14 03:33:03', '2022-04-14 03:38:56', 1, 0x9788c9eba42546f4bac89121f38e25b6),
(16, 15, 1, 1, 'Vendedores', 'vendedor_index', NULL, 0, NULL, '2022-04-14 03:33:31', '2022-04-14 03:33:31', 1, 0x612fbd4ce8e44a4f8d6d77beee7d990a),
(17, NULL, 1, 1, 'DATOS', NULL, 'bi bi-shield-lock-fill', 5, NULL, '2022-04-14 03:37:23', '2022-04-14 03:39:06', 1, 0xb58e188840274f3d8e0bcfb4fd7eff5c),
(18, 17, 1, 1, 'Tipo Documentos', 'tipo_documento_index', NULL, 6, NULL, '2022-04-14 03:37:46', '2022-04-14 03:38:09', 1, 0xca289c3aaa0648c2a1bad7df11469dfb),
(19, 17, 1, 1, 'Tipo Persona', 'tipo_persona_index', NULL, 0, NULL, '2022-04-14 03:40:11', '2022-04-14 03:40:11', 1, 0xae8947d478464b1696468e4bd427beed),
(20, 17, 1, 1, 'Tipo Pago', 'tipo_pago_index', NULL, 0, NULL, '2022-04-14 03:40:31', '2022-04-14 03:40:31', 1, 0xa8ca5097b7154c3994fed09e8e2f4699),
(21, 17, 1, 1, 'Tipo Moneda', 'tipo_moneda_index', NULL, 3, NULL, '2022-04-14 03:40:51', '2022-04-14 03:54:08', 1, 0x88de3b934c774288ae71f6baee5f8238),
(22, NULL, 1, 1, 'PEDIDOS', NULL, 'bi bi-file-text-fill', 1, NULL, '2022-04-14 03:42:13', '2022-04-14 03:45:24', 1, 0x032390a055eb4c34910e6b3b65254d5e),
(23, 22, 1, 1, 'Pedido', 'pedido_index', NULL, 0, NULL, '2022-04-14 03:42:31', '2022-04-14 03:42:31', 1, 0x06a8da22063e4bbeafda144523db89e3),
(24, 22, 1, 1, 'Clientes', 'cliente_index', NULL, 0, NULL, '2022-04-14 03:46:44', '2022-04-14 03:46:44', 1, 0xb2b11af915bf4826a94a8b8296163495),
(25, 22, 1, 1, 'Pagos', 'pago_index', NULL, 3, NULL, '2022-04-16 08:15:26', '2022-04-16 08:15:26', 1, 0x618dee0d942e4f27a5530eaf8465a3b1),
(26, NULL, 1, 1, 'KARDEX', NULL, 'fas fa-align-justify', 4, NULL, '2022-04-17 07:49:06', '2022-04-17 07:53:59', 1, 0x5dece9abcee046eba53d7163811ceed8),
(27, 26, 1, 1, 'Kardex', 'kardex_index', NULL, 0, NULL, '2022-04-17 07:49:29', '2022-04-17 07:49:29', 1, 0xffa5c7e89e4c46b2ac8f91f78f1948c8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametro`
--

CREATE TABLE `parametro` (
  `id` int(11) NOT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alias` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `tipo_pago_id` int(11) DEFAULT NULL,
  `tipo_moneda_id` int(11) DEFAULT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `precio_final` decimal(10,2) NOT NULL,
  `codigo` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_pago` tinyint(1) DEFAULT NULL,
  `cantidad_recibida` decimal(10,2) DEFAULT NULL,
  `cambio` decimal(10,2) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `vendedor_id`, `cliente_id`, `tipo_pago_id`, `tipo_moneda_id`, `propietario_id`, `config_id`, `precio_final`, `codigo`, `estado_pago`, `cantidad_recibida`, `cambio`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(4, 1, NULL, NULL, NULL, 1, 1, '6.00', '16320222230', 0, NULL, NULL, '2022-04-16 07:23:18', '2022-04-17 04:49:45', 1, 0x77a79fce3a184ea4b5e2c20c6376a51e),
(5, 1, NULL, NULL, NULL, 1, 1, '8.00', '16320222394', 0, NULL, NULL, '2022-04-16 07:39:09', '2022-04-17 04:51:15', 1, 0xb2a37334fa134c5a90b781c46ecb5fb4),
(6, 1, NULL, NULL, NULL, 1, 1, '8.00', '163202225043', 0, NULL, NULL, '2022-04-16 07:50:48', '2022-04-16 09:15:52', 1, 0x70dc0dc41eee46adbc8dc0083664609a),
(7, 1, 2, 3, 4, 1, 1, '8.00', '16320223415', 1, '20.00', '12.00', '2022-04-16 08:04:19', '2022-04-17 15:40:41', 1, 0x93a475bb4eff49b1883cfd8855392731),
(8, 1, 2, 3, 4, 1, 1, '40.00', '1632022387', 1, '100.00', '60.00', '2022-04-16 08:08:11', '2022-04-17 04:52:58', 1, 0x632338a63d2740a3bc50ca6a109aced9),
(9, 1, 2, 3, 4, 1, 1, '1616.00', '163202234614', 1, '2000.00', '384.00', '2022-03-03 08:46:29', '2022-04-17 05:54:02', 1, 0x7144a8cccf64412ba4ff630d1f1f56fe);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `codigo` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)',
  `categoria_id` int(11) NOT NULL,
  `marca_id` int(11) NOT NULL,
  `unidad_medida_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `propietario_id`, `config_id`, `descripcion`, `precio`, `stock`, `precio_venta`, `codigo`, `created_at`, `updated_at`, `activo`, `uuid`, `categoria_id`, `marca_id`, `unidad_medida_id`) VALUES
(1, 1, 1, 'COCA COLA LT', '4.00', 1, '6.00', 'BECO0001', '2022-04-10 00:53:06', '2022-04-17 05:54:02', 1, 0xbd1eebfe1cc24b7ba690f827c94d0332, 1, 1, 2),
(2, 1, 1, 'INKA COLA LT', '5.00', 598, '8.00', 'InK0001', '2022-04-10 01:03:21', '2022-04-17 15:40:41', 1, 0x420bc51722fa429aa4bffe653c09b099, 1, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id` int(11) NOT NULL,
  `tipo_persona_id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id`, `tipo_persona_id`, `tipo_documento_id`, `propietario_id`, `config_id`, `nombre`, `documento`, `direccion`, `telefono`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 2, 1, 1, 1, 'ALFARO NUNURA AARON JESUS', '75545493', 'SECHURA', '985475111', '2022-04-12 22:36:18', '2022-04-14 03:24:56', 1, 0x5ca03e3f472c4647ba2d693a7610e4a0),
(2, 2, 1, 1, 1, 'GUTIERREZ VILLEGAS CESAR DANIEL', '72314854', '', '', '2022-04-16 09:52:01', '2022-04-16 09:52:01', 1, 0x7c4636e5a9cb4d2f97092df15d4fba25),
(5, 2, 1, 1, 1, 'VILLEGAS NAVARRO SANTOS YOJANI', '03685701', '', '', '2022-04-17 05:11:08', '2022-04-17 05:11:08', 1, 0x17acbaaa052147c7939a46a37e4408d2),
(6, 2, 1, 1, 1, 'GUTIERREZ VILLEGAS BENJAMIN ALEXANDER', '72113011', '', '', '2022-04-17 05:11:41', '2022-04-17 05:11:41', 1, 0xfdf2392c152248c9bc8ec9e2c368dc81);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id` int(11) NOT NULL,
  `tipo_persona_id` int(11) DEFAULT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id`, `tipo_persona_id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 2, 1, 1, 'DNI', '2022-04-12 02:14:01', '2022-04-12 02:14:01', 1, 0x8a4efe04c98147ffb23ccba2b742326b),
(2, 1, 1, 1, 'RUC', '2022-04-12 02:14:15', '2022-04-12 02:14:15', 1, 0x0677671aaaa94aba917c324bcdfeedab),
(3, 2, 1, 1, 'CARNET DE EXTRANJERIA', '2022-04-12 22:34:18', '2022-04-14 03:47:54', 1, 0xfbccdd853d0c4f519ea1ef635e0fabf6),
(4, 2, 1, 1, 'PASAPORTE', '2022-04-12 22:34:44', '2022-04-14 03:48:03', 1, 0x55caf1ba31ad43a89f37d8fc89e7329d);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_moneda`
--

CREATE TABLE `tipo_moneda` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_moneda`
--

INSERT INTO `tipo_moneda` (`id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(4, 1, 1, 'SOLES', '2022-04-14 04:33:40', '2022-04-15 05:00:54', 1, 0x0d6f294b7f4840f8890fc7a8a95effce);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pago`
--

CREATE TABLE `tipo_pago` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `propietario_cuenta` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuenta` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_corto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_pago`
--

INSERT INTO `tipo_pago` (`id`, `propietario_id`, `config_id`, `descripcion`, `propietario_cuenta`, `cuenta`, `nombre_corto`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(3, 1, 1, 'PAGO EFECTIVO', NULL, NULL, NULL, '2022-04-14 04:06:55', '2022-04-15 05:00:35', 1, 0x0bb509b24e8a412db7b2eabc7f6d3586),
(4, 1, 1, 'TRANFERENCIA BANCO DE CREDITO DEL PERU', 'CESAR DANIEL GUTIERREZ VILLEGAS', '32233233443343', 'BCP', '2022-04-16 07:32:40', '2022-04-16 07:32:40', 1, 0x2a807366ff934462b0539b8b9c76b6a7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_persona`
--

CREATE TABLE `tipo_persona` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipo_persona`
--

INSERT INTO `tipo_persona` (`id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, 1, 'JURIDICA', '2022-04-09 22:45:35', '2022-04-14 03:47:07', 1, 0x8ce0c8484b33408d8597d156fbe41861),
(2, 1, 1, 'NATURAL', '2022-04-09 22:45:51', '2022-04-14 03:47:18', 1, 0xe23c4da0fec54aac8b06bad2b4b3e6de);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id`, `propietario_id`, `config_id`, `descripcion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, 1, 'KG', '2022-04-09 23:45:17', '2022-04-15 04:06:20', 1, 0xefb87ce518cd4cd4ba81100c16bab6f4),
(2, 1, 1, 'LT', '2022-04-09 23:45:41', '2022-04-15 04:06:28', 1, 0xf70208d822a94f9385a25235ba996ca4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `propietario_id`, `config_id`, `username`, `email`, `password`, `full_name`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, NULL, 1, 'admin', 'cio@pidia.pe', '$2y$13$sJGZ2iWV6xPqhHwClWsqbehYr0VDhv5VRwi1v0uZlZkN9IOzJzKyW', 'Carlos Chininin', '2022-04-09 21:50:13', '2022-04-09 22:00:45', 1, 0x246deebd651b4b999f30049088cd9808),
(2, 1, 1, 'AARON', 'AARON@PIDIA.PE', '$2y$13$JtMtbnkTGIj3M/MWKGlo5eAN5MPqLJe5gev6HRDbq1ktD6y.sV6mi', 'ALFARO NUNURA AARON JESUS', '2022-04-12 02:38:13', '2022-04-15 05:22:56', 1, 0xab6fef236b18468083feaab87e68609b);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_rol`
--

CREATE TABLE `usuario_rol` (
  `id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_rol`
--

INSERT INTO `usuario_rol` (`id`, `propietario_id`, `config_id`, `nombre`, `rol`, `permissions`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, NULL, NULL, 'Super Administrador', 'ROLE_SUPER_ADMIN', '[]', '2022-04-09 21:50:12', '2022-04-09 21:50:12', 1, 0xc44f22052b3d413d9ef762343fe5c8b3),
(2, 1, 1, 'Vendedor', 'ROLE_VENDEDOR', '{\"1\": {\"attr\": [\"master\"], \"menu\": \"config_index\"}, \"2\": {\"attr\": [\"master\"], \"menu\": \"config_menu_index\"}, \"3\": {\"attr\": [\"master\"], \"menu\": \"menu_index\"}, \"4\": {\"attr\": [\"master\"], \"menu\": \"usuario_index\"}, \"5\": {\"attr\": [\"master\"], \"menu\": \"usuario_rol_index\"}}', '2022-04-12 02:22:43', '2022-04-12 02:22:43', 1, 0xa7b222373e3c4696a39fc0e219f6b333);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_usuario_rol`
--

CREATE TABLE `usuario_usuario_rol` (
  `usuario_id` int(11) NOT NULL,
  `usuario_rol_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_usuario_rol`
--

INSERT INTO `usuario_usuario_rol` (`usuario_id`, `usuario_rol_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vendedor`
--

CREATE TABLE `vendedor` (
  `id` int(11) NOT NULL,
  `tipo_documento_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `propietario_id` int(11) DEFAULT NULL,
  `config_id` int(11) DEFAULT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documento` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL,
  `uuid` binary(16) NOT NULL COMMENT '(DC2Type:uuid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vendedor`
--

INSERT INTO `vendedor` (`id`, `tipo_documento_id`, `usuario_id`, `propietario_id`, `config_id`, `nombre`, `documento`, `telefono`, `direccion`, `created_at`, `updated_at`, `activo`, `uuid`) VALUES
(1, 1, 2, 1, 1, 'ALFARO NUNURA AARON JESUS', '75545493', '901215214', 'SECHURA', '2022-04-12 02:38:13', '2022-04-15 05:22:56', 1, 0x73f23f17f03542dd99b4403b591140e2);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4E10122DD17F50A6` (`uuid`),
  ADD KEY `IDX_4E10122D53C8D32C` (`propietario_id`),
  ADD KEY `IDX_4E10122D24DB0683` (`config_id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_F41C9B25D17F50A6` (`uuid`),
  ADD UNIQUE KEY `UNIQ_F41C9B25B6B12EC7` (`documento`),
  ADD KEY `IDX_F41C9B25647E8F91` (`tipo_persona_id`),
  ADD KEY `IDX_F41C9B25F6939175` (`tipo_documento_id`),
  ADD KEY `IDX_F41C9B2553C8D32C` (`propietario_id`),
  ADD KEY `IDX_F41C9B2524DB0683` (`config_id`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9EC131FFD17F50A6` (`uuid`),
  ADD KEY `IDX_9EC131FFCB305D73` (`proveedor_id`),
  ADD KEY `IDX_9EC131FF53C8D32C` (`propietario_id`),
  ADD KEY `IDX_9EC131FF24DB0683` (`config_id`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D48A2F7CD17F50A6` (`uuid`),
  ADD KEY `IDX_D48A2F7C53C8D32C` (`propietario_id`),
  ADD KEY `IDX_D48A2F7C24DB0683` (`config_id`);

--
-- Indices de la tabla `config_config_menu_menus`
--
ALTER TABLE `config_config_menu_menus`
  ADD PRIMARY KEY (`config_id`,`config_menu_id`),
  ADD KEY `IDX_A8E9CD3124DB0683` (`config_id`),
  ADD KEY `IDX_A8E9CD31B9CB2BE2` (`config_menu_id`);

--
-- Indices de la tabla `config_menu`
--
ALTER TABLE `config_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_F219D258D17F50A6` (`uuid`),
  ADD KEY `IDX_F219D2587645698E` (`producto_id`),
  ADD KEY `IDX_F219D258F2E704D7` (`compra_id`),
  ADD KEY `IDX_F219D25853C8D32C` (`propietario_id`),
  ADD KEY `IDX_F219D25824DB0683` (`config_id`);

--
-- Indices de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_A834F569D17F50A6` (`uuid`),
  ADD KEY `IDX_A834F5697645698E` (`producto_id`),
  ADD KEY `IDX_A834F5694854653A` (`pedido_id`),
  ADD KEY `IDX_A834F56953C8D32C` (`propietario_id`),
  ADD KEY `IDX_A834F56924DB0683` (`config_id`);

--
-- Indices de la tabla `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_70A0113D17F50A6` (`uuid`),
  ADD KEY `IDX_70A011353C8D32C` (`propietario_id`),
  ADD KEY `IDX_70A011324DB0683` (`config_id`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7D053A93D17F50A6` (`uuid`),
  ADD KEY `IDX_7D053A93727ACA70` (`parent_id`),
  ADD KEY `IDX_7D053A9353C8D32C` (`propietario_id`),
  ADD KEY `IDX_7D053A9324DB0683` (`config_id`);

--
-- Indices de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indices de la tabla `parametro`
--
ALTER TABLE `parametro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4C12795FD17F50A6` (`uuid`),
  ADD KEY `IDX_4C12795F613CEC58` (`padre_id`),
  ADD KEY `IDX_4C12795F53C8D32C` (`propietario_id`),
  ADD KEY `IDX_4C12795F24DB0683` (`config_id`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_C4EC16CED17F50A6` (`uuid`),
  ADD KEY `IDX_C4EC16CE8361A8B8` (`vendedor_id`),
  ADD KEY `IDX_C4EC16CEDE734E51` (`cliente_id`),
  ADD KEY `IDX_C4EC16CE7002A220` (`tipo_pago_id`),
  ADD KEY `IDX_C4EC16CE55188B9C` (`tipo_moneda_id`),
  ADD KEY `IDX_C4EC16CE53C8D32C` (`propietario_id`),
  ADD KEY `IDX_C4EC16CE24DB0683` (`config_id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_A7BB0615D17F50A6` (`uuid`),
  ADD KEY `IDX_A7BB061553C8D32C` (`propietario_id`),
  ADD KEY `IDX_A7BB061524DB0683` (`config_id`),
  ADD KEY `IDX_A7BB06153397707A` (`categoria_id`),
  ADD KEY `IDX_A7BB061581EF0041` (`marca_id`),
  ADD KEY `IDX_A7BB06152E003F4` (`unidad_medida_id`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_16C068CED17F50A6` (`uuid`),
  ADD UNIQUE KEY `UNIQ_16C068CEB6B12EC7` (`documento`),
  ADD KEY `IDX_16C068CE647E8F91` (`tipo_persona_id`),
  ADD KEY `IDX_16C068CEF6939175` (`tipo_documento_id`),
  ADD KEY `IDX_16C068CE53C8D32C` (`propietario_id`),
  ADD KEY `IDX_16C068CE24DB0683` (`config_id`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_54DF9189D17F50A6` (`uuid`),
  ADD KEY `IDX_54DF9189647E8F91` (`tipo_persona_id`),
  ADD KEY `IDX_54DF918953C8D32C` (`propietario_id`),
  ADD KEY `IDX_54DF918924DB0683` (`config_id`);

--
-- Indices de la tabla `tipo_moneda`
--
ALTER TABLE `tipo_moneda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_25B2DD35D17F50A6` (`uuid`),
  ADD KEY `IDX_25B2DD3553C8D32C` (`propietario_id`),
  ADD KEY `IDX_25B2DD3524DB0683` (`config_id`);

--
-- Indices de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_FEF0887BD17F50A6` (`uuid`),
  ADD KEY `IDX_FEF0887B53C8D32C` (`propietario_id`),
  ADD KEY `IDX_FEF0887B24DB0683` (`config_id`);

--
-- Indices de la tabla `tipo_persona`
--
ALTER TABLE `tipo_persona`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_421C973BD17F50A6` (`uuid`),
  ADD KEY `IDX_421C973B53C8D32C` (`propietario_id`),
  ADD KEY `IDX_421C973B24DB0683` (`config_id`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7DA31363D17F50A6` (`uuid`),
  ADD KEY `IDX_7DA3136353C8D32C` (`propietario_id`),
  ADD KEY `IDX_7DA3136324DB0683` (`config_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_2265B05DE7927C74` (`email`),
  ADD UNIQUE KEY `UNIQ_2265B05DD17F50A6` (`uuid`),
  ADD KEY `IDX_2265B05D53C8D32C` (`propietario_id`),
  ADD KEY `IDX_2265B05D24DB0683` (`config_id`);

--
-- Indices de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_72EDD1A4D17F50A6` (`uuid`),
  ADD KEY `IDX_72EDD1A453C8D32C` (`propietario_id`),
  ADD KEY `IDX_72EDD1A424DB0683` (`config_id`);

--
-- Indices de la tabla `usuario_usuario_rol`
--
ALTER TABLE `usuario_usuario_rol`
  ADD PRIMARY KEY (`usuario_id`,`usuario_rol_id`),
  ADD KEY `IDX_4AC6232ADB38439E` (`usuario_id`),
  ADD KEY `IDX_4AC6232AFEA85A65` (`usuario_rol_id`);

--
-- Indices de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9797982ED17F50A6` (`uuid`),
  ADD UNIQUE KEY `UNIQ_9797982EDB38439E` (`usuario_id`),
  ADD KEY `IDX_9797982EF6939175` (`tipo_documento_id`),
  ADD KEY `IDX_9797982E53C8D32C` (`propietario_id`),
  ADD KEY `IDX_9797982E24DB0683` (`config_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `compra`
--
ALTER TABLE `compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `config_menu`
--
ALTER TABLE `config_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `parametro`
--
ALTER TABLE `parametro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_moneda`
--
ALTER TABLE `tipo_moneda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_persona`
--
ALTER TABLE `tipo_persona`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `vendedor`
--
ALTER TABLE `vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `FK_4E10122D24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_4E10122D53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `FK_F41C9B2524DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_F41C9B2553C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_F41C9B25647E8F91` FOREIGN KEY (`tipo_persona_id`) REFERENCES `tipo_persona` (`id`),
  ADD CONSTRAINT `FK_F41C9B25F6939175` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`);

--
-- Filtros para la tabla `compra`
--
ALTER TABLE `compra`
  ADD CONSTRAINT `FK_9EC131FF24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_9EC131FF53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_9EC131FFCB305D73` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`);

--
-- Filtros para la tabla `config`
--
ALTER TABLE `config`
  ADD CONSTRAINT `FK_D48A2F7C24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_D48A2F7C53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `config_config_menu_menus`
--
ALTER TABLE `config_config_menu_menus`
  ADD CONSTRAINT `FK_A8E9CD3124DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_A8E9CD31B9CB2BE2` FOREIGN KEY (`config_menu_id`) REFERENCES `config_menu` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `FK_F219D25824DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_F219D25853C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_F219D2587645698E` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`),
  ADD CONSTRAINT `FK_F219D258F2E704D7` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`id`);

--
-- Filtros para la tabla `detalle_pedido`
--
ALTER TABLE `detalle_pedido`
  ADD CONSTRAINT `FK_A834F56924DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_A834F5694854653A` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`),
  ADD CONSTRAINT `FK_A834F56953C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_A834F5697645698E` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`);

--
-- Filtros para la tabla `marca`
--
ALTER TABLE `marca`
  ADD CONSTRAINT `FK_70A011324DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_70A011353C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `FK_7D053A9324DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_7D053A9353C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_7D053A93727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `menu` (`id`);

--
-- Filtros para la tabla `parametro`
--
ALTER TABLE `parametro`
  ADD CONSTRAINT `FK_4C12795F24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_4C12795F53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_4C12795F613CEC58` FOREIGN KEY (`padre_id`) REFERENCES `parametro` (`id`);

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `FK_C4EC16CE24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_C4EC16CE53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_C4EC16CE55188B9C` FOREIGN KEY (`tipo_moneda_id`) REFERENCES `tipo_moneda` (`id`),
  ADD CONSTRAINT `FK_C4EC16CE7002A220` FOREIGN KEY (`tipo_pago_id`) REFERENCES `tipo_pago` (`id`),
  ADD CONSTRAINT `FK_C4EC16CE8361A8B8` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedor` (`id`),
  ADD CONSTRAINT `FK_C4EC16CEDE734E51` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `FK_A7BB061524DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_A7BB06152E003F4` FOREIGN KEY (`unidad_medida_id`) REFERENCES `unidad_medida` (`id`),
  ADD CONSTRAINT `FK_A7BB06153397707A` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  ADD CONSTRAINT `FK_A7BB061553C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_A7BB061581EF0041` FOREIGN KEY (`marca_id`) REFERENCES `marca` (`id`);

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `FK_16C068CE24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_16C068CE53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_16C068CE647E8F91` FOREIGN KEY (`tipo_persona_id`) REFERENCES `tipo_persona` (`id`),
  ADD CONSTRAINT `FK_16C068CEF6939175` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`);

--
-- Filtros para la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD CONSTRAINT `FK_54DF918924DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_54DF918953C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_54DF9189647E8F91` FOREIGN KEY (`tipo_persona_id`) REFERENCES `tipo_persona` (`id`);

--
-- Filtros para la tabla `tipo_moneda`
--
ALTER TABLE `tipo_moneda`
  ADD CONSTRAINT `FK_25B2DD3524DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_25B2DD3553C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `tipo_pago`
--
ALTER TABLE `tipo_pago`
  ADD CONSTRAINT `FK_FEF0887B24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_FEF0887B53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `tipo_persona`
--
ALTER TABLE `tipo_persona`
  ADD CONSTRAINT `FK_421C973B24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_421C973B53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD CONSTRAINT `FK_7DA3136324DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_7DA3136353C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `FK_2265B05D24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_2265B05D53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `FK_72EDD1A424DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_72EDD1A453C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `usuario_usuario_rol`
--
ALTER TABLE `usuario_usuario_rol`
  ADD CONSTRAINT `FK_4AC6232ADB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_4AC6232AFEA85A65` FOREIGN KEY (`usuario_rol_id`) REFERENCES `usuario_rol` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `vendedor`
--
ALTER TABLE `vendedor`
  ADD CONSTRAINT `FK_9797982E24DB0683` FOREIGN KEY (`config_id`) REFERENCES `config` (`id`),
  ADD CONSTRAINT `FK_9797982E53C8D32C` FOREIGN KEY (`propietario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_9797982EDB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `FK_9797982EF6939175` FOREIGN KEY (`tipo_documento_id`) REFERENCES `tipo_documento` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
