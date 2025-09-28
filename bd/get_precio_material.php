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
    
    // Obtener el precio más reciente del material
    $consulta = "SELECT precio_unitario, fecha_detalle 
                FROM detallecomprob_cpra 
                WHERE id_materiales = :id_material 
                ORDER BY fecha_detalle DESC, id_detalle_comp_cpra DESC 
                LIMIT 1";
    
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([':id_material' => $id_material]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'precio_unitario' => $resultado['precio_unitario'],
            'fecha_detalle' => $resultado['fecha_detalle']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'precio_unitario' => 0,
            'message' => 'No se encontró precio para este material'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion = NULL;
?>