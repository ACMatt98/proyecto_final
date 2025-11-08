<?php
header('Content-Type: application/json');

include_once 'conexion.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó un ID de presupuesto.']);
    exit;
}

$id_presupuesto = intval($_GET['id']);
$response = ['success' => false, 'data' => null, 'message' => ''];

try {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    // 1. Obtener el precio total del presupuesto
    $consulta_presupuesto = "SELECT precio_total_presup FROM presupuesto WHERE id_presupuesto = ?";
    $stmt_presupuesto = $conexion->prepare($consulta_presupuesto);
    $stmt_presupuesto->execute([$id_presupuesto]);
    $presupuesto = $stmt_presupuesto->fetch(PDO::FETCH_ASSOC);

    if (!$presupuesto) {
        throw new Exception("No se encontró el presupuesto con el ID proporcionado.");
    }

    // 2. Obtener la suma de los cobros (seña y pagos)
    $consulta_cobros = "SELECT 
                            COALESCE(SUM(CASE WHEN tipo_cobro = 'Seña' THEN monto ELSE 0 END), 0) AS seña,
                            COALESCE(SUM(CASE WHEN tipo_cobro = 'Pago' THEN monto ELSE 0 END), 0) AS pago_final
                        FROM cobro 
                        WHERE id_presupuesto = ?";
    $stmt_cobros = $conexion->prepare($consulta_cobros);
    $stmt_cobros->execute([$id_presupuesto]);
    $cobros = $stmt_cobros->fetch(PDO::FETCH_ASSOC);

    // 3. Combinar los resultados
    $data = [
        'precio_total_presup' => $presupuesto['precio_total_presup'],
        'seña' => $cobros['seña'],
        'pago_final' => $cobros['pago_final']
    ];

    $response['success'] = true;
    $response['data'] = $data;

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
$conexion = null;
?>