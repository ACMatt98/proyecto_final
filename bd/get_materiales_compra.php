<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $id_comprobante = $data['id_comprobante'] ?? 0;
    
    if ($id_comprobante <= 0) {
        throw new Exception('ID de comprobante inválido');
    }

    $consulta = "SELECT d.id_materiales, m.nombre_material, d.cantidad, d.unidad_medida, d.precio_unitario
                 FROM detallecomprob_cpra d
                 JOIN materiales m ON d.id_materiales = m.id_materiales
                 WHERE d.id_compro_comp = :id_comprobante";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([':id_comprobante' => $id_comprobante]);
    $materiales = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($materiales);

} catch (Exception $e) {
    echo json_encode([]);
}

$conexion = NULL;
?>