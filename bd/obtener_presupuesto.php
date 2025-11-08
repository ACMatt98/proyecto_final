<?php
// filepath: c:\xampp\htdocs\proyecto_final\bd\obtener_presupuesto.php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');
$id_presupuesto = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if (!$id_presupuesto) throw new Exception("ID no válido.");

    // Obtener datos principales (incluyendo IDs para la edición)
    $consulta = "SELECT p.*, e.estado, 
                CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente_completo
                FROM presupuesto p 
                JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                JOIN cliente c ON p.id_cliente = c.id_cliente 
                WHERE p.id_presupuesto = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$id_presupuesto]);
    $presupuesto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$presupuesto) throw new Exception("Presupuesto no encontrado");

    // Obtener productos del presupuesto
    $consulta = "SELECT dp.id_producto_term, dp.productos, dp.precio_unitario, pt.nomb_producto
                FROM detalle_presupuesto dp
                LEFT JOIN producto_terminado pt ON dp.id_producto_term = pt.id_producto_term
                WHERE dp.id_presupuesto = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$id_presupuesto]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $presupuesto['productos'] = $productos;

    $respuesta = ['success' => true, 'data' => $presupuesto];

} catch (Exception $e) {
    $respuesta = ['success' => false, 'error' => $e->getMessage()];
    http_response_code(404);
}

echo json_encode($respuesta);
$conexion = NULL;
?>