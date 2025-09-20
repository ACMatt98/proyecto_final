<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $id = isset($data['id']) ? intval($data['id']) : null;
    $nombre_material = trim($data['nombre_material'] ?? '');
    $existencia = floatval($data['existencia'] ?? 0);
    $marca = trim($data['marca'] ?? '');
    $unidad_medida = trim($data['unidad_medida'] ?? '');
    $opcion = intval($data['opcion'] ?? 0);

    if ($opcion === 1) {
        // Alta
        if (!$nombre_material || !$existencia || !$marca || !$unidad_medida) {
            throw new Exception('Todos los campos son obligatorios');
        }
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
        $response = ['success' => true, 'message' => 'Material creado'];
    } elseif ($opcion === 2 && $id) {
        // Edición
        if (!$nombre_material || !$existencia || !$marca || !$unidad_medida) {
            throw new Exception('Todos los campos son obligatorios');
        }
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
        $response = ['success' => true, 'message' => 'Material actualizado'];
    } elseif ($opcion === 3 && $id) {
        // Borrar
        $consulta = "DELETE FROM materiales WHERE id_materiales=:id";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([':id' => $id]);
        $response = ['success' => true, 'message' => 'Material eliminado'];
    } else {
        throw new Exception('Operación no válida');
    }

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['success'