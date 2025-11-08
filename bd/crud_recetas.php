<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data === null) {
        throw new Exception('Error al decodificar JSON');
    }

    $opcion = isset($data['opcion']) ? intval($data['opcion']) : 0;
    $id = isset($data['id']) ? intval($data['id']) : 0;

    // --- Lógica de cálculo de costo con conversión de unidades ---
    $costo_receta_calculado = 0;
    if (isset($data['ingredientes']) && is_array($data['ingredientes'])) {
        foreach ($data['ingredientes'] as $ingrediente) {
            $id_material = intval($ingrediente['id_materiales'] ?? 0);
            $cantidad_receta = floatval($ingrediente['cantidad_nec'] ?? 0);
            $unidad_receta = trim($ingrediente['unidad_medida'] ?? '');

            if ($id_material <= 0 || $cantidad_receta <= 0) continue;

            // Obtener precio más reciente Y SU UNIDAD
            $stmtPrecio = $conexion->prepare("SELECT precio_unitario, unidad_medida FROM detallecomprob_cpra WHERE id_materiales = :id ORDER BY id_detalle_comp_cpra DESC LIMIT 1");
            $stmtPrecio->execute([':id' => $id_material]);
            $precioData = $stmtPrecio->fetch(PDO::FETCH_ASSOC);

            if ($precioData) {
                $precio_base = floatval($precioData['precio_unitario']);
                $unidad_precio = $precioData['unidad_medida'];
                $precio_final = $precio_base;

                // Lógica de Conversión
                if ($unidad_precio === 'Kg' && $unidad_receta === 'gr') {
                    $precio_final = $precio_base / 1000;
                } else if ($unidad_precio === 'Lt' && $unidad_receta === 'ml') {
                    $precio_final = $precio_base / 1000;
                }
                
                $costo_receta_calculado += $cantidad_receta * $precio_final;
            }
        }
    }

    switch($opcion) {
        case 1: // ALTA
            $nomb_receta = isset($data['nomb_receta']) ? trim($data['nomb_receta']) : '';
            $id_producto_term = isset($data['id_producto_term']) ? intval($data['id_producto_term']) : 0;

            $conexion->beginTransaction();

            $consulta = "INSERT INTO receta_estandar (nomb_receta, costo_receta, id_producto_term) VALUES(:nomb, :costo, :id_prod)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([':nomb' => $nomb_receta, ':costo' => $costo_receta_calculado, ':id_prod' => $id_producto_term]);
            $id_receta = $conexion->lastInsertId();

            $stmt = $conexion->prepare("INSERT INTO detalle_receta (cantidad_nec, unidad_medida, id_receta_estandar, id_materiales) VALUES (:cant, :unidad, :id_receta, :id_mat)");
            foreach($data['ingredientes'] as $ing) {
                $stmt->execute([':cant' => $ing['cantidad_nec'], ':unidad' => $ing['unidad_medida'], ':id_receta' => $id_receta, ':id_mat' => $ing['id_materiales']]);
            }

            $conexion->commit();

            
            // Consultar los datos de la nueva fila para devolverlos
            $consulta = "SELECT re.id_receta_estandar, re.nomb_receta, re.costo_receta, pt.nomb_producto 
                         FROM receta_estandar re 
                         JOIN producto_terminado pt ON re.id_producto_term = pt.id_producto_term 
                         WHERE re.id_receta_estandar = :id_receta";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([':id_receta' => $id_receta]);
            $datos_nuevos = $resultado->fetch(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'message' => 'Receta agregada', 'data' => $datos_nuevos]);
            break;

        case 2: // MODIFICACIÓN
            if ($id <= 0) throw new Exception('ID de receta no válido');
            
            $nomb_receta = isset($data['nomb_receta']) ? trim($data['nomb_receta']) : '';
            $id_producto_term = isset($data['id_producto_term']) ? intval($data['id_producto_term']) : 0;

            $conexion->beginTransaction();

            // 1. Actualizar la receta principal
            $consulta = "UPDATE receta_estandar SET nomb_receta=:nomb, costo_receta=:costo, id_producto_term=:id_prod WHERE id_receta_estandar=:id";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([':nomb' => $nomb_receta, ':costo' => $costo_receta_calculado, ':id_prod' => $id_producto_term, ':id' => $id]);

            // 2. Borrar los ingredientes viejos
            $resultado = $conexion->prepare("DELETE FROM detalle_receta WHERE id_receta_estandar=:id");
            $resultado->execute([':id' => $id]);

            // 3. Insertar los ingredientes nuevos
            $stmt = $conexion->prepare("INSERT INTO detalle_receta (cantidad_nec, unidad_medida, id_receta_estandar, id_materiales) VALUES (:cant, :unidad, :id_receta, :id_mat)");
            foreach($data['ingredientes'] as $ing) {
                $stmt->execute([':cant' => $ing['cantidad_nec'], ':unidad' => $ing['unidad_medida'], ':id_receta' => $id, ':id_mat' => $ing['id_materiales']]);
            }

            $conexion->commit();
            echo json_encode(['success' => true, 'message' => 'Receta actualizada']);
            break;

        case 3: // BAJA
            if ($id <= 0) throw new Exception('ID de receta no válido');
            
            $conexion->beginTransaction();
            // Borrar primero los detalles
            $resultado = $conexion->prepare("DELETE FROM detalle_receta WHERE id_receta_estandar=:id");
            $resultado->execute([':id' => $id]);
            // Borrar la receta
            $resultado = $conexion->prepare("DELETE FROM receta_estandar WHERE id_receta_estandar=:id");
            $resultado->execute([':id' => $id]);
            $conexion->commit();

            echo json_encode(['success' => true, 'message' => 'Receta eliminada']);
            break;

        default:
            throw new Exception('Opción no válida');
    }
} catch (Exception $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conexion = NULL;
?>