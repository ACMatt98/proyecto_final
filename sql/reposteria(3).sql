-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-11-2025 a las 22:09:15
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
-- Base de datos: `reposteria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(30) NOT NULL,
  `nombre_cliente` text NOT NULL,
  `apellido_cliente` text NOT NULL,
  `telefono_cliente` text NOT NULL,
  `direccion_cliente` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre_cliente`, `apellido_cliente`, `telefono_cliente`, `direccion_cliente`) VALUES
(1, 'Mauricio', 'Sevilla', '2147483647', 'Uruguay 28'),
(2, 'Josefina', 'Ramirez', '3454761083', 'Lamadrid 201'),
(3, 'Catalina', 'Vasquez', '3454044271', 'Salta 531'),
(4, 'Andres', 'Merele', '3454210745', 'San Martin 300'),
(5, 'Hernan', 'Belgrano', '345019283', 'Urquiza 258'),
(6, 'Adriana', 'Yarez', '3454872219', 'San Luis 1134'),
(7, 'Lautaro', 'Polo', '3454882310', 'Urquiza 125'),
(8, 'Franco', 'Barsi', '3454677500', 'Chile 830'),
(9, 'Cesar Dario', 'Acuña Legarreta', '3454965231', 'Mendiburu 300'),
(10, 'Luna', 'Aldecoa', '3454662310', 'Rivadavia 500'),
(11, 'Cielo', 'Aldecoa', '3454007315', 'Rivadavia 500'),
(14, 'Martin', 'Takanaka', '3454856278', 'J.J.Paso 123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro`
--

CREATE TABLE `cobro` (
  `id_cobro` int(30) NOT NULL,
  `id_presupuesto` int(10) NOT NULL,
  `monto` float(10,2) NOT NULL,
  `fecha_cobro` date NOT NULL,
  `tipo_cobro` varchar(50) NOT NULL,
  `id_comprob_vta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cobro`
--

INSERT INTO `cobro` (`id_cobro`, `id_presupuesto`, `monto`, `fecha_cobro`, `tipo_cobro`, `id_comprob_vta`) VALUES
(11, 7, 45000.00, '2025-11-08', 'Seña', 12),
(12, 7, 5500.00, '2025-11-08', 'Pago', 13),
(13, 7, 4500.00, '2025-11-08', 'Pago', 14),
(14, 7, 500.00, '2025-11-08', 'Pago', 15),
(15, 9, 30000.00, '2025-11-08', 'Seña', 16);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantecompra`
--

CREATE TABLE `comprobantecompra` (
  `id_compro_comp` int(10) NOT NULL,
  `fecha` date NOT NULL,
  `n_de_comprob` int(20) NOT NULL,
  `precio_total` float(10,2) NOT NULL,
  `id_proveedor` int(10) NOT NULL,
  `tipo_factura` varchar(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `comprobantecompra`
--

INSERT INTO `comprobantecompra` (`id_compro_comp`, `fecha`, `n_de_comprob`, `precio_total`, `id_proveedor`, `tipo_factura`) VALUES
(1, '2025-03-13', 100, 2000.00, 2, 'E'),
(3, '2025-08-07', 159, 15500.00, 1, 'B'),
(4, '2025-08-07', 160, 25000.00, 1, 'C'),
(18, '2025-09-20', 2345, 6500.00, 1, 'A'),
(19, '2025-09-20', 161, 6000.00, 4, 'B'),
(20, '2025-10-29', 20, 5700.00, 3, 'B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantevta`
--

CREATE TABLE `comprobantevta` (
  `id_comprob_vta` int(30) NOT NULL,
  `fecha_comprob` date DEFAULT NULL,
  `total_comprob_vta` decimal(10,2) DEFAULT NULL,
  `n_comprob_vta` int(20) NOT NULL,
  `tipo_factura_vta` varchar(10) NOT NULL,
  `detalles` text DEFAULT NULL,
  `id_cliente` int(30) DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `comprobantevta`
--

INSERT INTO `comprobantevta` (`id_comprob_vta`, `fecha_comprob`, `total_comprob_vta`, `n_comprob_vta`, `tipo_factura_vta`, `detalles`, `id_cliente`, `archivo`) VALUES
(10, NULL, NULL, 1, 'A', NULL, 4, 'venta_10_1762290452.png'),
(12, '2025-11-08', 45000.00, 0, '', NULL, 2, NULL),
(13, '2025-11-08', 5500.00, 0, '', NULL, 2, NULL),
(14, '2025-11-08', 4500.00, 0, '', NULL, 2, NULL),
(15, '2025-11-08', 500.00, 0, '', NULL, 2, NULL),
(16, '2025-11-08', 30000.00, 0, '', NULL, 8, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detallecomprob_cpra`
--

CREATE TABLE `detallecomprob_cpra` (
  `id_detalle_comp_cpra` int(10) NOT NULL,
  `fecha_detalle` date NOT NULL,
  `precio_unitario` float(10,2) NOT NULL,
  `tipo_factura_compra` varchar(30) NOT NULL,
  `id_compro_comp` int(10) NOT NULL,
  `id_materiales` int(10) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_medida` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `detallecomprob_cpra`
--

INSERT INTO `detallecomprob_cpra` (`id_detalle_comp_cpra`, `fecha_detalle`, `precio_unitario`, `tipo_factura_compra`, `id_compro_comp`, `id_materiales`, `cantidad`, `unidad_medida`) VALUES
(75, '2025-09-20', 3500.00, 'C', 4, 1, 1000.00, 'gr'),
(76, '2025-09-20', 6000.00, 'C', 4, 2, 500.00, 'gr'),
(77, '2025-09-20', 2000.00, 'C', 4, 6, 1000.00, 'gr'),
(78, '2025-09-20', 1234.00, 'C', 4, 6, 123.00, 'gr'),
(82, '2025-09-20', 4000.00, 'A', 18, 1, 1000.00, 'gr'),
(83, '2025-09-20', 1500.00, 'A', 18, 2, 300.00, 'gr'),
(84, '2025-09-20', 1500.00, 'A', 18, 2, 300.00, 'gr'),
(85, '2025-09-20', 3500.00, 'B', 3, 1, 1000.00, 'gr'),
(86, '2025-09-20', 6000.00, 'B', 3, 2, 500.00, 'gr'),
(87, '2025-09-20', 2000.00, 'B', 3, 6, 100.00, 'gr'),
(88, '2025-09-20', 2000.00, 'B', 3, 7, 500.00, 'gr'),
(89, '2025-09-20', 2000.00, 'B', 3, 5, 2000.00, 'gr'),
(90, '2025-09-27', 1000.00, 'B', 19, 5, 7000.00, 'gr'),
(91, '2025-09-27', 5000.00, 'B', 19, 2, 2000.00, 'gr'),
(93, '2025-11-01', 2000.00, 'E', 1, 3, 10.00, 'Unidad'),
(94, '2025-11-01', 1500.00, 'B', 20, 6, 0.50, 'gr'),
(95, '2025-11-01', 1000.00, 'B', 20, 7, 0.20, 'gr'),
(96, '2025-11-01', 1200.00, 'B', 20, 9, 0.50, 'gr'),
(97, '2025-11-01', 2000.00, 'B', 20, 2, 0.50, 'gr');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_presupuesto`
--

CREATE TABLE `detalle_presupuesto` (
  `id_detalle_presupuesto` int(10) NOT NULL,
  `kilos_torta` float(10,2) NOT NULL,
  `productos` varchar(255) NOT NULL,
  `observaciones` varchar(255) NOT NULL,
  `id_presupuesto` int(10) NOT NULL,
  `id_producto_term` int(10) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_presupuesto`
--

INSERT INTO `detalle_presupuesto` (`id_detalle_presupuesto`, `kilos_torta`, `productos`, `observaciones`, `id_presupuesto`, `id_producto_term`, `precio_unitario`) VALUES
(5, 0.00, 'Torta Alemana', '', 7, 1003, 10000.00),
(6, 0.00, 'Torta de Manzana Invertida', '', 7, 1000, 8500.00),
(7, 0.00, 'Pastafrola', '', 7, 1005, 7000.00),
(8, 0.00, 'Cheese Cake', '', 7, 1001, 8500.00),
(9, 0.00, 'Lemon Pie', '', 7, 1002, 6500.00),
(14, 0.00, 'Cheese Cake', '', 9, 1001, 8500.00),
(15, 0.00, 'Tiramisu', '', 9, 1006, 8500.00),
(16, 0.00, 'Torta de Manzana Invertida', '', 9, 1000, 8500.00),
(17, 0.00, 'Selva Negra', '', 9, 1008, 12000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_receta`
--

CREATE TABLE `detalle_receta` (
  `id_detalle_receta` int(10) NOT NULL,
  `cantidad_nec` float(10,2) NOT NULL,
  `unidad_medida` varchar(255) NOT NULL,
  `id_receta_estandar` int(10) NOT NULL,
  `id_materiales` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_receta`
--

INSERT INTO `detalle_receta` (`id_detalle_receta`, `cantidad_nec`, `unidad_medida`, `id_receta_estandar`, `id_materiales`) VALUES
(24, 0.50, 'gr', 1005, 7),
(25, 0.30, 'gr', 1005, 1),
(26, 0.50, 'gr', 1005, 5),
(27, 0.10, 'gr', 1005, 6),
(28, 0.30, 'gr', 1005, 2),
(31, 150.00, 'gr', 1004, 1),
(32, 100.00, 'gr', 1004, 4),
(33, 350.00, 'gr', 1004, 5),
(34, 0.32, 'gr', 1003, 1),
(45, 0.35, 'gr', 1006, 1),
(46, 0.70, 'gr', 1006, 5),
(47, 0.05, 'gr', 1006, 7),
(48, 0.15, 'gr', 1006, 6),
(49, 0.30, 'gr', 1006, 2),
(50, 0.50, 'gr', 1008, 1),
(51, 0.20, 'gr', 1008, 6),
(52, 0.60, 'gr', 1008, 5),
(53, 0.10, 'gr', 1008, 7),
(54, 0.20, 'gr', 1008, 2),
(63, 0.35, 'gr', 1010, 9),
(64, 0.50, 'gr', 1010, 5),
(65, 0.30, 'gr', 1010, 6),
(66, 0.10, 'gr', 1010, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_presup`
--

CREATE TABLE `estado_presup` (
  `id_estado` int(30) NOT NULL,
  `estado` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `estado_presup`
--

INSERT INTO `estado_presup` (`id_estado`, `estado`) VALUES
(1, 'Completado'),
(2, 'En proceso'),
(3, 'Cancelado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materiales`
--

CREATE TABLE `materiales` (
  `id_materiales` int(30) NOT NULL,
  `nombre_material` varchar(30) NOT NULL,
  `unidad_medida` varchar(20) NOT NULL DEFAULT 'unidad',
  `existencia` int(30) NOT NULL,
  `marca` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_materiales`, `nombre_material`, `unidad_medida`, `existencia`, `marca`) VALUES
(1, 'Azucar Mascabo', 'Kg', -141, 'Chango'),
(2, 'Chocolate Amargo', 'gr', 2102, 'Mapsa'),
(3, 'Limon', 'Unidad', 4, 'De la esquina'),
(4, 'Azucar Blanca', 'gr', 1400, 'Chango'),
(5, 'Harina 0000', 'gr', 1658, 'Caserita'),
(6, 'Sal', 'gr', 3004, 'Celusal'),
(7, 'Polvo para Hornear', 'gr', 4, 'Mama cocina'),
(9, 'Azucar rubia', 'Kg', 10, 'Chango');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `id_presupuesto` int(10) NOT NULL,
  `fecha_presup` date NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `precio_total_presup` float(10,2) NOT NULL,
  `mano_obra` float(10,2) NOT NULL,
  `id_estado_presupuesto` int(10) NOT NULL,
  `id_cliente` int(10) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `id_comprob_vta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `presupuesto`
--

INSERT INTO `presupuesto` (`id_presupuesto`, `fecha_presup`, `lugar`, `precio_total_presup`, `mano_obra`, `id_estado_presupuesto`, `id_cliente`, `observaciones`, `id_comprob_vta`) VALUES
(7, '2025-10-27', 'Salon las Americas', 55500.00, 15000.00, 2, 2, 'Pastafrola de Batata solo', NULL),
(9, '2025-10-29', 'Cristobal Cafe', 62500.00, 25000.00, 2, 8, 'Queso Fontina en el tiramisu', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_terminado`
--

CREATE TABLE `producto_terminado` (
  `id_producto_term` int(11) NOT NULL,
  `nomb_producto` varchar(30) NOT NULL,
  `precio_venta` float(10,2) NOT NULL,
  `costo_produccion` float(10,2) NOT NULL,
  `stock` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `producto_terminado`
--

INSERT INTO `producto_terminado` (`id_producto_term`, `nomb_producto`, `precio_venta`, `costo_produccion`, `stock`) VALUES
(1000, 'Torta de Manzana Invertida', 8500.00, 7000.00, 2),
(1001, 'Cheese Cake', 8500.00, 6500.00, 2),
(1002, 'Lemon Pie', 6500.00, 4700.00, 3),
(1003, 'Torta Alemana', 10000.00, 8500.00, 1),
(1005, 'Pastafrola', 7000.00, 5000.00, 2),
(1006, 'Tiramisu', 8500.00, 4250.00, 2),
(1008, 'Selva Negra', 12000.00, 6000.00, 4),
(1009, 'Orange Pie', 8600.00, 4300.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(30) NOT NULL,
  `nombre_proveedor` varchar(30) NOT NULL,
  `direccion_proveedor` varchar(20) NOT NULL,
  `telefono_proveedor` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_proveedor`, `direccion_proveedor`, `telefono_proveedor`) VALUES
(1, 'PisaPan', 'Ber.Hirigoyen', '345-4097431'),
(2, 'JLC', 'Tavella 345', '345-044781'),
(3, 'La casa del panadero', '1ero de Mayo 400', '3454768843'),
(4, 'LMB', 'Sarmiento 230', '34548877765');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receta_estandar`
--

CREATE TABLE `receta_estandar` (
  `id_receta_estandar` int(10) NOT NULL,
  `costo_receta` float(10,2) NOT NULL,
  `nomb_receta` varchar(255) NOT NULL,
  `id_producto_term` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `receta_estandar`
--

INSERT INTO `receta_estandar` (`id_receta_estandar`, `costo_receta`, `nomb_receta`, `id_producto_term`) VALUES
(1003, 1120.00, 'Torta Alemana', 1003),
(1004, 875000.00, 'Torta de Manzana Invertida', 1000),
(1005, 4250.00, 'Chesse Cake', 1001),
(1006, 3825.00, 'Tiramisu', 1006),
(1008, 3950.00, 'Selva Negra', 1008),
(1010, 1300.00, 'Orange Pie', 1009);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(30) NOT NULL,
  `tipo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `tipo`) VALUES
(1, 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(30) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `password`, `id_rol`) VALUES
(1, 'Mirta', 'Carrasco', 'admin123', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD PRIMARY KEY (`id_cobro`),
  ADD KEY `id_comprob_vta` (`id_comprob_vta`),
  ADD KEY `fk_cobro_presupuesto` (`id_presupuesto`);

--
-- Indices de la tabla `comprobantecompra`
--
ALTER TABLE `comprobantecompra`
  ADD PRIMARY KEY (`id_compro_comp`),
  ADD KEY `fk_id_proveedor` (`id_proveedor`);

--
-- Indices de la tabla `comprobantevta`
--
ALTER TABLE `comprobantevta`
  ADD PRIMARY KEY (`id_comprob_vta`),
  ADD KEY `fk_comprobantevta_cliente` (`id_cliente`);

--
-- Indices de la tabla `detallecomprob_cpra`
--
ALTER TABLE `detallecomprob_cpra`
  ADD PRIMARY KEY (`id_detalle_comp_cpra`),
  ADD KEY `fk_id_compro_compra` (`id_compro_comp`),
  ADD KEY `fk_id_materiales_cpra` (`id_materiales`);

--
-- Indices de la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  ADD PRIMARY KEY (`id_detalle_presupuesto`),
  ADD KEY `id_presupuesto` (`id_presupuesto`),
  ADD KEY `fk_detalle_producto` (`id_producto_term`);

--
-- Indices de la tabla `detalle_receta`
--
ALTER TABLE `detalle_receta`
  ADD PRIMARY KEY (`id_detalle_receta`),
  ADD KEY `fk_id_receta_estandar` (`id_receta_estandar`),
  ADD KEY `fk_id_materiales` (`id_materiales`);

--
-- Indices de la tabla `estado_presup`
--
ALTER TABLE `estado_presup`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `materiales`
--
ALTER TABLE `materiales`
  ADD PRIMARY KEY (`id_materiales`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`id_presupuesto`),
  ADD KEY `id_estado_presupuesto` (`id_estado_presupuesto`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_comprob_vta` (`id_comprob_vta`);

--
-- Indices de la tabla `producto_terminado`
--
ALTER TABLE `producto_terminado`
  ADD PRIMARY KEY (`id_producto_term`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `receta_estandar`
--
ALTER TABLE `receta_estandar`
  ADD PRIMARY KEY (`id_receta_estandar`),
  ADD KEY `fk_receta_producto` (`id_producto_term`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `cobro`
--
ALTER TABLE `cobro`
  MODIFY `id_cobro` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `comprobantecompra`
--
ALTER TABLE `comprobantecompra`
  MODIFY `id_compro_comp` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `comprobantevta`
--
ALTER TABLE `comprobantevta`
  MODIFY `id_comprob_vta` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `detallecomprob_cpra`
--
ALTER TABLE `detallecomprob_cpra`
  MODIFY `id_detalle_comp_cpra` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  MODIFY `id_detalle_presupuesto` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `detalle_receta`
--
ALTER TABLE `detalle_receta`
  MODIFY `id_detalle_receta` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT de la tabla `estado_presup`
--
ALTER TABLE `estado_presup`
  MODIFY `id_estado` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_materiales` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `producto_terminado`
--
ALTER TABLE `producto_terminado`
  MODIFY `id_producto_term` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1010;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `receta_estandar`
--
ALTER TABLE `receta_estandar`
  MODIFY `id_receta_estandar` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1011;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cobro`
--
ALTER TABLE `cobro`
  ADD CONSTRAINT `cobro_ibfk_1` FOREIGN KEY (`id_comprob_vta`) REFERENCES `comprobantevta` (`id_comprob_vta`),
  ADD CONSTRAINT `fk_cobro_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `comprobantecompra`
--
ALTER TABLE `comprobantecompra`
  ADD CONSTRAINT `fk_id_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `comprobantevta`
--
ALTER TABLE `comprobantevta`
  ADD CONSTRAINT `fk_comprobantevta_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detallecomprob_cpra`
--
ALTER TABLE `detallecomprob_cpra`
  ADD CONSTRAINT `fk_id_compro_compra` FOREIGN KEY (`id_compro_comp`) REFERENCES `comprobantecompra` (`id_compro_comp`),
  ADD CONSTRAINT `fk_id_materiales_cpra` FOREIGN KEY (`id_materiales`) REFERENCES `materiales` (`id_materiales`);

--
-- Filtros para la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`id_producto_term`) REFERENCES `producto_terminado` (`id_producto_term`) ON UPDATE CASCADE,
  ADD CONSTRAINT `id_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_receta`
--
ALTER TABLE `detalle_receta`
  ADD CONSTRAINT `fk_id_materiales` FOREIGN KEY (`id_materiales`) REFERENCES `materiales` (`id_materiales`),
  ADD CONSTRAINT `fk_id_receta_estandar` FOREIGN KEY (`id_receta_estandar`) REFERENCES `receta_estandar` (`id_receta_estandar`);

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `id_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_comprob_vta` FOREIGN KEY (`id_comprob_vta`) REFERENCES `comprobantevta` (`id_comprob_vta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_estado_presupuesto` FOREIGN KEY (`id_estado_presupuesto`) REFERENCES `estado_presup` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `receta_estandar`
--
ALTER TABLE `receta_estandar`
  ADD CONSTRAINT `fk_receta_producto` FOREIGN KEY (`id_producto_term`) REFERENCES `producto_terminado` (`id_producto_term`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
