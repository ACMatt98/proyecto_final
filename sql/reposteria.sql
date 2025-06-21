-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-06-2025 a las 21:15:13
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
(8, 'Franco', 'Barsi', '3454677432', 'Chile 830'),
(9, 'Cesar Dario', 'Acuña Legarreta', '3454965231', 'Mendiburu 300'),
(10, 'Luna', 'Aldecoa', '3454662310', 'Rivadavia 500'),
(11, 'Cielo', 'Aldecoa', '3454007315', 'Rivadavia 500');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro`
--

CREATE TABLE `cobro` (
  `id_cobro` int(30) NOT NULL,
  `precio_total_cobro` float(10,2) NOT NULL,
  `fecha_cobro` date NOT NULL,
  `senia` float(10,2) NOT NULL,
  `id_comprob_vta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantecompra`
--

CREATE TABLE `comprobantecompra` (
  `id_compro_comp` int(10) NOT NULL,
  `fecha` date NOT NULL,
  `n_de_comprob` int(20) NOT NULL,
  `precio_total` float(10,2) NOT NULL,
  `id_proveedor` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `comprobantecompra`
--

INSERT INTO `comprobantecompra` (`id_compro_comp`, `fecha`, `n_de_comprob`, `precio_total`, `id_proveedor`) VALUES
(1, '2025-03-13', 100, 15000.00, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantevta`
--

CREATE TABLE `comprobantevta` (
  `id_comprob_vta` int(30) NOT NULL,
  `n_comprob_vta` int(20) NOT NULL,
  `tipo_factura_vta` varchar(10) NOT NULL,
  `detalles` text DEFAULT NULL,
  `id_cliente` int(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `comprobantevta`
--

INSERT INTO `comprobantevta` (`id_comprob_vta`, `n_comprob_vta`, `tipo_factura_vta`, `detalles`, `id_cliente`) VALUES
(1, 1, 'A', 'vendido', 4),
(2, 2, 'D', 'vendido', 1),
(3, 3, 'C', 'vendido', 6),
(4, 4, 'B', 'vendido', 2);

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
  `id_materiales` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `id_producto_term` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

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
  `existencia` int(30) NOT NULL,
  `marca` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `materiales`
--

INSERT INTO `materiales` (`id_materiales`, `nombre_material`, `existencia`, `marca`) VALUES
(1, 'Azucar Mascabo', 3, 'Chango'),
(2, 'Chocolate Amargo', 2, 'Mapsa'),
(3, 'Limon', 4, 'De la esquina'),
(4, 'Azucar Blanca', 2, 'Chango'),
(5, 'Harina 0000', 4, 'Caserita');

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
  `id_comprob_vta` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_terminado`
--

CREATE TABLE `producto_terminado` (
  `id_producto_term` int(10) NOT NULL,
  `nomb_producto` varchar(30) NOT NULL,
  `precio_venta` float(10,2) NOT NULL,
  `costo_produccion` float(10,2) NOT NULL,
  `stock` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `producto_terminado`
--

INSERT INTO `producto_terminado` (`id_producto_term`, `nomb_producto`, `precio_venta`, `costo_produccion`, `stock`) VALUES
(1, 'Lemon Pie', 6500.00, 4700.00, 3),
(2, 'Cheese Cake', 8000.00, 6500.00, 1);

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
(1, 'PisaPan', 'B.Hirigoyen', '345-4097431'),
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
(1, 3250.00, 'Lemon Pie', 1),
(2, 3250.00, 'Lemon Pie', 1);

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
  ADD KEY `id_comprob_vta` (`id_comprob_vta`);

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
  ADD KEY `id_producto_term` (`id_producto_term`);

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
  ADD KEY `fk_id_producto_term` (`id_producto_term`);

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
  MODIFY `id_cliente` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `cobro`
--
ALTER TABLE `cobro`
  MODIFY `id_cobro` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comprobantecompra`
--
ALTER TABLE `comprobantecompra`
  MODIFY `id_compro_comp` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `comprobantevta`
--
ALTER TABLE `comprobantevta`
  MODIFY `id_comprob_vta` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `detallecomprob_cpra`
--
ALTER TABLE `detallecomprob_cpra`
  MODIFY `id_detalle_comp_cpra` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_presupuesto`
--
ALTER TABLE `detalle_presupuesto`
  MODIFY `id_detalle_presupuesto` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_receta`
--
ALTER TABLE `detalle_receta`
  MODIFY `id_detalle_receta` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estado_presup`
--
ALTER TABLE `estado_presup`
  MODIFY `id_estado` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `materiales`
--
ALTER TABLE `materiales`
  MODIFY `id_materiales` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `receta_estandar`
--
ALTER TABLE `receta_estandar`
  MODIFY `id_receta_estandar` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `cobro_ibfk_1` FOREIGN KEY (`id_comprob_vta`) REFERENCES `comprobantevta` (`id_comprob_vta`);

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
  ADD CONSTRAINT `id_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `id_producto_term` FOREIGN KEY (`id_producto_term`) REFERENCES `producto_terminado` (`id_producto_term`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `fk_id_producto_term` FOREIGN KEY (`id_producto_term`) REFERENCES `producto_terminado` (`id_producto_term`);

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
