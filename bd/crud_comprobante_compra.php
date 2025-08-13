<?php
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

    // Validación de datos
    $fecha = $data['fecha'] ?? '';
    $n_de_comprob = $data['n_de_comprob'] ?? '';
    $precio_total = $data['precio_total'] ?? 0;
    $id_proveedor = $data['id_proveedor'] ?? 0;
    $opcion = $data['opcion'] ?? 0;
    $id = $data['id'] ?? null;
    $materiales = $data['materiales'] ?? [];

    if (empty($fecha) || empty($n_de_comprob)) {
        throw new Exception('Fecha y número de comprobante son requeridos');
    }

    if ($id_proveedor <= 0) {
        throw new Exception('Debe seleccionar un proveedor válido');
    }

    if (!is_array($materiales) || count($materiales) === 0) {
        throw new Exception('Debe agregar al menos un material');
    }

    $response = ['success' => false];
    $conexion->beginTransaction();

    switch($opcion) {
        case 1: // Alta
            // Insertar comprobante
            $consulta = "INSERT INTO comprobantecompra 
                        (fecha, n_de_comprob, precio_total, id_proveedor) 
                        VALUES(:fecha, :n_de_comprob, :precio_total, :id_proveedor)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':fecha' => $fecha,
                ':n_de_comprob' => $n_de_comprob,
                ':precio_total' => $precio_total,
                ':id_proveedor' => $id_proveedor
            ]);
            
            $id_comprobante = $conexion->lastInsertId();

            // Insertar materiales
            foreach($materiales as $material) {
                // Validar material
                if (empty($material['id_materiales']) || empty($material['cantidad']) || empty($material['precio_unitario'])) {
                    throw new Exception('Datos de material incompletos');
                }

                // Insertar detalle
                $consulta = "INSERT INTO detallecomprob_cpra 
                            (fecha_detalle, precio_unitario, tipo_factura_compra, 
                            id_compro_comp, id_materiales, cantidad_nec) 
                            VALUES(NOW(), :precio_unitario, 'A', 
                            :id_comprobante, :id_materiales, :cantidad)";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute([
                    ':precio_unitario' => $material['precio_unitario'],
                    ':id_comprobante' => $id_comprobante,
                    ':id_materiales' => $material['id_materiales'],
                    ':cantidad' => $material['cantidad']
                ]);
                
                // Actualizar existencia
                $consulta = "UPDATE materiales SET existencia = existencia + :cantidad 
                            WHERE id_materiales = :id_materiales";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute([
                    ':cantidad' => $material['cantidad'],
                    ':id_materiales' => $material['id_materiales']
                ]);
            }

            $response = ['success' => true, 'message' => 'Comprobante creado correctamente'];
            break;

        // ... (casos para edición y eliminación)
    }

    $conexion->commit();
    echo json_encode($response);

} catch (Exception $e) {
    $conexion->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion = NULL;
?>