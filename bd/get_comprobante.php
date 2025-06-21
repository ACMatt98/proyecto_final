<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$consulta = "SELECT archivo FROM comprobantevta WHERE id_comprob_vta = ?";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$id]);
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

if($resultado) {
    $archivo = '../uploads/comprobantes/' . $resultado['archivo'];
    echo json_encode(['archivo' => $archivo]);
} else {
    echo json_encode(['error' => 'Comprobante no encontrado']);
}

$conexion = NULL;
?>