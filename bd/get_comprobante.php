<?php
// filepath: c:\xampp\htdocs\proyecto_final\bd\get_comprobante.php
include_once 'conexion.php';

header('Content-Type: application/json');

try {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    // El ID que viene del JS es el NÚMERO de comprobante, no el ID de la BD
    $n_comprob_vta = isset($_GET['id']) ? $_GET['id'] : '';

    if (empty($n_comprob_vta)) {
        throw new Exception('Número de comprobante no proporcionado.');
    }

    // CORRECCIÓN 1: Buscar por la columna correcta 'n_comprob_vta'
    $consulta = "SELECT archivo FROM comprobantevta WHERE n_comprob_vta = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$n_comprob_vta]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && !empty($resultado['archivo'])) {
        // CORRECCIÓN 2: Construir la ruta correcta para el navegador
        // Quitamos el '../' y nos aseguramos que la carpeta sea la correcta.
        $ruta_web = 'uploads/comprobantes_venta/' . $resultado['archivo'];
        echo json_encode(['archivo' => $ruta_web]);
    } else {
        // Si no se encuentra, devolvemos un error claro
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Comprobante no encontrado o no tiene archivo asociado.']);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>