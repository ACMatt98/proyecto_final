<?php
// filepath: c:\xampp\htdocs\proyecto_final\bd\crud_presupuestos.php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Operación no válida.'];

try {
    $conexion->beginTransaction();

    $opcion = isset($_POST['opcion']) ? intval($_POST['opcion']) : 0;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    // Datos comunes
    $fecha_presup = isset($_POST['fecha_presup']) ? $_POST['fecha_presup'] : '';
    $lugar = isset($_POST['lugar']) ? $_POST['lugar'] : '';
    $precio_total_presup = isset($_POST['precio_total_presup']) ? floatval($_POST['precio_total_presup']) : 0;
    $mano_obra = isset($_POST['mano_obra']) ? floatval($_POST['mano_obra']) : 0;
    $id_estado_presupuesto = isset($_POST['id_estado_presupuesto']) ? intval($_POST['id_estado_presupuesto']) : 1;
    $id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
    $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';
    $productos = isset($_POST['productos']) ? json_decode($_POST['productos'], true) : [];

    switch ($opcion) {
        case 1: // ALTA
            $consulta = "INSERT INTO presupuesto (fecha_presup, lugar, precio_total_presup, mano_obra, id_estado_presupuesto, id_cliente, observaciones) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$fecha_presup, $lugar, $precio_total_presup, $mano_obra, $id_estado_presupuesto, $id_cliente, $observaciones]);
            
            // CORRECCIÓN LÓGICA: Obtener el ID del presupuesto recién creado
            $id_presupuesto = $conexion->lastInsertId();

            // Insertar productos en detalle_presupuesto
            $consulta_detalle = "INSERT INTO detalle_presupuesto (id_presupuesto, id_producto_term, productos, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt_detalle = $conexion->prepare($consulta_detalle);
            foreach ($productos as $producto) {
                $stmt_detalle->execute([$id_presupuesto, $producto['id'], $producto['nombre'], $producto['precio']]);
            }
            
            $response = ['success' => true, 'message' => 'Presupuesto creado correctamente.', 'id' => $id_presupuesto];
            break;

        case 2: // MODIFICACIÓN
            if (!$id) throw new Exception("ID de presupuesto no proporcionado.");

            $consulta = "UPDATE presupuesto SET fecha_presup=?, lugar=?, precio_total_presup=?, mano_obra=?, id_estado_presupuesto=?, id_cliente=?, observaciones=? WHERE id_presupuesto=?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$fecha_presup, $lugar, $precio_total_presup, $mano_obra, $id_estado_presupuesto, $id_cliente, $observaciones, $id]);

            // Reemplazar los productos: Borrar los antiguos e insertar los nuevos
            $stmt_delete = $conexion->prepare("DELETE FROM detalle_presupuesto WHERE id_presupuesto = ?");
            $stmt_delete->execute([$id]);

            $consulta_detalle = "INSERT INTO detalle_presupuesto (id_presupuesto, id_producto_term, productos, precio_unitario) VALUES (?, ?, ?, ?)";
            $stmt_detalle = $conexion->prepare($consulta_detalle);
            foreach ($productos as $producto) {
                $stmt_detalle->execute([$id, $producto['id'], $producto['nombre'], $producto['precio']]);
            }

            $response = ['success' => true, 'message' => 'Presupuesto actualizado correctamente.', 'id' => $id];
            break;

        case 3: // BAJA
            if (!$id) throw new Exception("ID de presupuesto no proporcionado.");
            
            // Eliminar primero los detalles
            $stmt_delete = $conexion->prepare("DELETE FROM detalle_presupuesto WHERE id_presupuesto = ?");
            $stmt_delete->execute([$id]);

            // Luego eliminar el presupuesto principal
            $stmt_delete = $conexion->prepare("DELETE FROM presupuesto WHERE id_presupuesto = ?");
            $stmt_delete->execute([$id]);

            $response = ['success' => true, 'message' => 'Presupuesto eliminado.'];
            break;

        case 4: // ACTUALIZAR SOLO ESTADO
            $id_presupuesto_estado = isset($_POST['id']) ? intval($_POST['id']) : null;
            $id_estado_presupuesto = isset($_POST['id_estado_presupuesto']) ? intval($_POST['id_estado_presupuesto']) : null;

            if (!$id_presupuesto_estado) throw new Exception("ID de presupuesto no proporcionado para actualizar estado.");
            if (!$id_estado_presupuesto) throw new Exception("Nuevo estado no proporcionado.");

            $consulta = "UPDATE presupuesto SET id_estado_presupuesto = ? WHERE id_presupuesto = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id_estado_presupuesto, $id_presupuesto_estado]);

            $response = ['success' => true, 'message' => 'Estado del presupuesto actualizado.'];
            break;

        case 5: // DESCONTAR STOCK DE UN PRESUPUESTO
            if (!$id) throw new Exception("ID de presupuesto no proporcionado.");

            // 1. Obtener la lista de materiales y cantidades para este presupuesto
            $consulta_materiales = "SELECT 
                                        dr.id_materiales,
                                        SUM(dr.cantidad_nec) AS cantidad_a_descontar
                                    FROM detalle_presupuesto AS dp
                                    JOIN receta_estandar AS re ON dp.id_producto_term = re.id_producto_term
                                    JOIN detalle_receta AS dr ON re.id_receta_estandar = dr.id_receta_estandar
                                    WHERE dp.id_presupuesto = ?
                                    GROUP BY dr.id_materiales";
            
            $stmt_materiales = $conexion->prepare($consulta_materiales);
            $stmt_materiales->execute([$id]);
            $materiales_a_descontar = $stmt_materiales->fetchAll(PDO::FETCH_ASSOC);

            if (empty($materiales_a_descontar)) {
                throw new Exception("No se encontraron materiales para descontar para este presupuesto.");
            }

            // 2. Preparar la consulta para actualizar el stock
            $consulta_update_stock = "UPDATE materiales SET existencia = existencia - ? WHERE id_materiales = ?";
            $stmt_update = $conexion->prepare($consulta_update_stock);

            // 3. Recorrer la lista y descontar cada material
            foreach ($materiales_a_descontar as $material) {
                $stmt_update->execute([
                    $material['cantidad_a_descontar'],
                    $material['id_materiales']
                ]);
            }

            $response = ['success' => true, 'message' => 'Stock descontado correctamente.'];
            break;

        case 6: // REGISTRAR SEÑA
        case 7: // REGISTRAR PAGO
            if (!$id || !isset($_POST['monto'])) {
                throw new Exception("Faltan datos para registrar el cobro (ID o monto).");
            }

            $monto = floatval($_POST['monto']);
            $tipo_cobro = ($opcion == 6) ? 'Seña' : 'Pago';
            $fecha_actual = date('Y-m-d');

                // 1. Obtener el id_cliente desde el presupuesto
                $stmt_cliente = $conexion->prepare("SELECT id_cliente FROM presupuesto WHERE id_presupuesto = ?");
                $stmt_cliente->execute([$id]);
                $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
                if (!$cliente) {
                    throw new Exception("No se encontró el presupuesto para asociar el cliente.");
                }
                $id_cliente = $cliente['id_cliente'];

                // 2. Crear el comprobante de venta
                $stmt_comprob = $conexion->prepare("INSERT INTO comprobantevta (fecha_comprob, total_comprob_vta, id_cliente) VALUES (?, ?, ?)");
                $stmt_comprob->execute([$fecha_actual, $monto, $id_cliente]);
                
                // 3. Obtener el ID del comprobante recién creado
                $id_comprob_vta = $conexion->lastInsertId();

                // 4. Registrar el cobro, asociándolo al presupuesto y al nuevo comprobante
                $stmt_cobro = $conexion->prepare("INSERT INTO cobro (id_presupuesto, id_comprob_vta, fecha_cobro, monto, tipo_cobro) VALUES (?, ?, ?, ?, ?)");
                $stmt_cobro->execute([$id, $id_comprob_vta, $fecha_actual, $monto, $tipo_cobro]);

                $response = ['success' => true, 'message' => $tipo_cobro . ' registrado correctamente.'];
            break;
    }

    $conexion->commit();

} catch (Exception $e) {
    $conexion->rollBack();
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    http_response_code(500);
}

echo json_encode($response);
$conexion = NULL;
?>