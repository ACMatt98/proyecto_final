<?php
// filepath: c:\xampp\htdocs\proyecto_final\bd\obtener_seguimiento.php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

$id_presupuesto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_presupuesto === 0) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado.']);
    exit;
}

try {
    // 1. Obtener datos del presupuesto (estado y observaciones)
    $consulta_presup = "SELECT p.id_estado_presupuesto, e.estado, p.observaciones 
                        FROM presupuesto p
                        JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                        WHERE p.id_presupuesto = ?";
    $stmt_presup = $conexion->prepare($consulta_presup);
    $stmt_presup->execute([$id_presupuesto]);
    $presupuesto = $stmt_presup->fetch(PDO::FETCH_ASSOC);

    // 2. Obtener todos los estados posibles para los radio buttons
    $consulta_estados = "SELECT id_estado, estado FROM estado_presup ORDER BY id_estado";
    $stmt_estados = $conexion->prepare($consulta_estados);
    $stmt_estados->execute();
    $todos_los_estados = $stmt_estados->fetchAll(PDO::FETCH_ASSOC);

    // 3. Obtener materiales (Lógica avanzada, requiere tabla de recetas)
    // Esta es una consulta de EJEMPLO. Necesitas una tabla que relacione productos con materiales.
    // Supongamos que tienes una tabla `recetas` con (id_producto_term, id_material, cantidad)
    $consulta_materiales = "SELECT 
                                m.nombre_material,
                                SUM(dr.cantidad_nec) AS cantidad_total,
                                dr.unidad_medida
                            FROM detalle_presupuesto AS dp
                            JOIN receta_estandar AS re ON dp.id_producto_term = re.id_producto_term
                            JOIN detalle_receta AS dr ON re.id_receta_estandar = dr.id_receta_estandar
                            JOIN materiales AS m ON dr.id_materiales = m.id_materiales
                            WHERE dp.id_presupuesto = ?
                            GROUP BY m.nombre_material, dr.unidad_medida
                            ORDER BY m.nombre_material";
    
    $stmt_materiales = $conexion->prepare($consulta_materiales);
    $stmt_materiales->execute([$id_presupuesto]);
    $materiales = $stmt_materiales->fetchAll(PDO::FETCH_ASSOC);


    $response = [
        'success' => true,
        'data' => [
            'presupuesto' => $presupuesto,
            'todos_los_estados' => $todos_los_estados,
            'materiales' => $materiales
        ]
    ];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
?>