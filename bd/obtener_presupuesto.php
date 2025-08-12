<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$id_presupuesto = $_GET['id'];

try {
    // Obtener datos principales del presupuesto
    $consulta = "SELECT p.*, e.estado, 
                CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente_completo
                FROM presupuesto p 
                JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                JOIN cliente c ON p.id_cliente = c.id_cliente 
                WHERE p.id_presupuesto = ?";
    
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$id_presupuesto]);
    $presupuesto = $resultado->fetch(PDO::FETCH_ASSOC);

    if (!$presupuesto) {
        throw new Exception("Presupuesto no encontrado");
    }

    // Obtener productos del presupuesto
    $consulta = "SELECT dp.productos, dp.precio_unitario, pt.nomb_producto
                FROM detalle_presupuesto dp
                LEFT JOIN producto_terminado pt ON dp.id_producto_term = pt.id_producto_term
                WHERE dp.id_presupuesto = ?";
    
    $resultado = $conexion->prepare($consulta);
    $resultado->execute([$id_presupuesto]);
    $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);

    // Formatear la respuesta
    $respuesta = [
        'success' => true,
        'data' => [
            'cliente' => $presupuesto['cliente_completo'],
            'fecha' => $presupuesto['fecha_presup'],
            'estado' => $presupuesto['estado'],
            'lugar' => $presupuesto['lugar'],
            'mano_obra' => $presupuesto['mano_obra'],
            'total' => $presupuesto['precio_total_presup'],
            'observaciones' => $presupuesto['observaciones'] ?: 'No hay observaciones',
            'productos' => $productos
        ]
    ];

} catch (Exception $e) {
    $respuesta = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($respuesta);
$conexion = NULL;
?>