<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Error al leer los datos de entrada');
    }
    
    $data = json_decode($json, true);
    if ($data === null) {
        throw new Exception('Error al decodificar JSON');
    }

    // Datos recibidos
    $fecha = $data['fecha'] ?? '';
    $n_de_comprob = $data['n_de_comprob'] ?? '';
    $precio_total = $data['precio_total'] ?? 0;
    $id_proveedor = $data['id_proveedor'] ?? 0;
    $opcion = $data['opcion'] ?? 0;
    $id = $data['id'] ?? null;
    $materiales = $data['materiales'] ?? [];
    $tipo_factura = $data['tipo_factura'] ?? '';
    

    // Validación según operación
    if ($opcion == 1 || $opcion == 2) {
        if (empty($fecha) || empty($n_de_comprob)) {
            throw new Exception('Fecha y número de comprobante son requeridos');
        }
        if ($id_proveedor <= 0) {
            throw new Exception('Debe seleccionar un proveedor válido');
        }
        if (!is_array($materiales) || count($materiales) === 0) {
            throw new Exception('Debe agregar al menos un material');
        }
    }
    if ($opcion == 3 && !$id) {
        throw new Exception('ID de comprobante inválido');
    }

    $response = ['success' => false];
    $conexion->beginTransaction();

    if ($opcion == 1) {
        // Alta: Insertar comprobante de compra y obtener su ID
        $consulta = "INSERT INTO comprobantecompra (fecha, n_de_comprob, precio_total, id_proveedor, tipo_factura) 
            VALUES (:fecha, :n_de_comprob, :precio_total, :id_proveedor, :tipo_factura)";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([
            ':fecha' => $fecha,
            ':n_de_comprob' => $n_de_comprob,
            ':precio_total' => $precio_total,
            ':id_proveedor' => $id_proveedor,
            ':tipo_factura' => $tipo_factura
        ]);
        $id_comprobante = $conexion->lastInsertId();

        // Insertar materiales
        $tipo_factura = $data['tipo_factura'] ?? '';
        foreach($materiales as &$material) {
            $material['tipo_factura'] = $tipo_factura;
        }
        unset($material);

        foreach($materiales as $material) {
            // --- Conversión a unidad base ---
            $cantidad = $material['cantidad'];
            $unidad_medida = $material['unidad_medida'];

            if ($unidad_medida === 'Kg') {
                $cantidad = $cantidad * 1000;
                $unidad_medida = 'gr';
            } elseif ($unidad_medida === 'Lt') {
                $cantidad = $cantidad * 1000;
                $unidad_medida = 'ml';
            }
            // Si es gr, ml o Unidad, no cambia

            if (empty($material['id_materiales']) || empty($cantidad) || empty($material['precio_unitario']) || empty($material['tipo_factura'])) {
                throw new Exception('Datos de material incompletos');
            }
            $consulta = "INSERT INTO detallecomprob_cpra 
                        (fecha_detalle, precio_unitario, tipo_factura_compra, 
                        id_compro_comp, id_materiales, cantidad, unidad_medida) 
                        VALUES(NOW(), :precio_unitario, :tipo_factura, 
                        :id_comprobante, :id_materiales, :cantidad, :unidad_medida)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':precio_unitario' => $material['precio_unitario'],
                ':tipo_factura' => $material['tipo_factura'],
                ':id_comprobante' => $id_comprobante,
                ':id_materiales' => $material['id_materiales'],
                ':cantidad' => $cantidad,
                ':unidad_medida' => $unidad_medida
            ]);
            // Actualizar existencia
            $consulta = "UPDATE materiales SET existencia = existencia + :cantidad 
                        WHERE id_materiales = :id_materiales";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':cantidad' => $cantidad,
                ':id_materiales' => $material['id_materiales']
            ]);
        }
        $response = ['success' => true, 'message' => 'Comprobante creado correctamente'];
    } elseif ($opcion == 2 && $id) {
        // Edición: Actualizar comprobante y sus materiales
        $consulta = "UPDATE comprobantecompra SET fecha=:fecha, n_de_comprob=:n_de_comprob, precio_total=:precio_total, id_proveedor=:id_proveedor, tipo_factura=:tipo_factura WHERE id_compro_comp=:id";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([
            ':fecha' => $fecha,
            ':n_de_comprob' => $n_de_comprob,
            ':precio_total' => $precio_total,
            ':id_proveedor' => $id_proveedor,
            ':tipo_factura' => $tipo_factura,
            ':id' => $id
        ]);

        // Eliminar materiales anteriores
        $consulta = "DELETE FROM detallecomprob_cpra WHERE id_compro_comp=:id";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([':id' => $id]);

        // Asignar tipo_factura a cada material
        $tipo_factura = $data['tipo_factura'] ?? '';
        foreach($materiales as &$material) {
            $material['tipo_factura'] = $tipo_factura;
        }
        unset($material);

        foreach($materiales as $material) {
            // --- Conversión a unidad base ---
            $cantidad = $material['cantidad'];
            $unidad_medida = $material['unidad_medida'];

            if ($unidad_medida === 'Kg') {
                $cantidad = $cantidad * 1000;
                $unidad_medida = 'gr';
            } elseif ($unidad_medida === 'Lt') {
                $cantidad = $cantidad * 1000;
                $unidad_medida = 'ml';
            }
            // Si es gr, ml o Unidad, no cambia

            if (empty($material['id_materiales']) || empty($cantidad) || empty($material['precio_unitario']) || empty($material['tipo_factura'])) {
                throw new Exception('Datos de material incompletos');
            }
            $consulta = "INSERT INTO detallecomprob_cpra 
                        (fecha_detalle, precio_unitario, tipo_factura_compra, 
                        id_compro_comp, id_materiales, cantidad, unidad_medida) 
                        VALUES(NOW(), :precio_unitario, :tipo_factura, 
                        :id_comprobante, :id_materiales, :cantidad, :unidad_medida)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':precio_unitario' => $material['precio_unitario'],
                ':tipo_factura' => $material['tipo_factura'],
                ':id_comprobante' => $id,
                ':id_materiales' => $material['id_materiales'],
                ':cantidad' => $cantidad,
                ':unidad_medida' => $unidad_medida
            ]);
            // Actualizar existencia si lo necesitas (puedes ajustar la lógica aquí)
        }
        $response = ['success' => true, 'message' => 'Comprobante actualizado correctamente'];

    } elseif ($opcion == 3 && $id) { // --- INICIO DE LA MODIFICACIÓN ---
        // BAJA: Eliminar comprobante y sus detalles
        
        // Opcional: Revertir la existencia de los materiales.
        // Primero, obtenemos los detalles para saber qué revertir.
        $consulta = "SELECT id_materiales, cantidad FROM detallecomprob_cpra WHERE id_compro_comp = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([':id' => $id]);
        $materiales_a_revertir = $resultado->fetchAll(PDO::FETCH_ASSOC);

        foreach ($materiales_a_revertir as $material) {
            $consulta_revertir = "UPDATE materiales SET existencia = existencia - :cantidad WHERE id_materiales = :id_materiales";
            $resultado_revertir = $conexion->prepare($consulta_revertir);
            $resultado_revertir->execute([
                ':cantidad' => $material['cantidad'],
                ':id_materiales' => $material['id_materiales']
            ]);
        }

        // 1. Eliminar los detalles del comprobante
        $consulta = "DELETE FROM detallecomprob_cpra WHERE id_compro_comp = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([':id' => $id]);

        // 2. Eliminar el comprobante principal
        $consulta = "DELETE FROM comprobantecompra WHERE id_compro_comp = :id";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([':id' => $id]);

        $response = ['success' => true, 'message' => 'Comprobante eliminado correctamente'];
        // --- FIN DE LA MODIFICACIÓN ---

    } else {
        throw new Exception('Operación no válida');
    }
    $conexion->commit();
    echo json_encode($response);

} catch (Exception $e) {
    if ($conexion->inTransaction()){
        $conexion->rollBack();
    }
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion = NULL;
?>