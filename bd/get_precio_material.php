<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $id_material = isset($data['id_material']) ? intval($data['id_material']) : 0;
    
    if ($id_material <= 0) {
        throw new Exception('ID de material inválido');
    }
    
    // Modificamos la consulta para obtener también la unidad de medida del precio
    $consulta = "SELECT precio_unitario, unidad_medida 
                 FROM detallecomprob_cpra 
                 WHERE id_materiales = :id_material 
                 ORDER BY id_detalle_comp_cpra DESC 
                 LIMIT 1";
    
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([':id_material' => $id_material]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        // Devolvemos el precio y su unidad
        echo json_encode([
            'success' => true, 
            'precio_unitario' => $resultado['precio_unitario'],
            'unidad_precio' => $resultado['unidad_medida'] 
        ]);
    } else {
        echo json_encode(['success' => false, 'precio_unitario' => 0, 'unidad_precio' => '']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>