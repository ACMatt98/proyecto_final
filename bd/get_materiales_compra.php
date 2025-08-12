<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $id_comprobante = $data['id_comprobante'] ?? 0;

    $consulta = "SELECT dc.id_materiales, m.nombre_material, dc.cantidad_nec as cantidad, 
                        dc.precio_unitario 
                 FROM detallecomprob_cpra dc
                 JOIN materiales m ON dc.id_materiales = m.id_materiales
                 WHERE dc.id_compro_comp = :id_comprobante";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([':id_comprobante' => $id_comprobante]);
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion = NULL;
?>