<?php
include_once 'conexion.php';

header('Content-Type: application/json');

try {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    // Recibimos el NÚMERO DE COMPROBANTE desde el JS
    $n_comprob_vta = isset($_GET['id']) ? $_GET['id'] : '';

    if (empty($n_comprob_vta)) {
        throw new Exception('Número de comprobante no proporcionado.');
    }

    // CONSULTA MEJORADA: 
    // Buscamos el comprobante y hacemos JOIN con Cliente y Cobro para tener todos los datos
    $consulta = "SELECT 
                    cv.archivo, 
                    cv.fecha_comprob, 
                    cv.total_comprob_vta,
                    cli.nombre_cliente, 
                    cli.apellido_cliente, 
                    cli.direccion_cliente,
                    co.tipo_cobro,
                    co.id_presupuesto
                 FROM comprobantevta cv
                 LEFT JOIN cliente cli ON cv.id_cliente = cli.id_cliente
                 LEFT JOIN cobro co ON cv.id_comprob_vta = co.id_comprob_vta
                 WHERE cv.n_comprob_vta = ?";

    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$n_comprob_vta]);
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        // ESCENARIO 1: Hay un archivo físico subido (PDF o Imagen)
        if (!empty($resultado['archivo']) && file_exists('../uploads/comprobantes_venta/' . $resultado['archivo'])) {
            $ruta_web = 'uploads/comprobantes_venta/' . $resultado['archivo'];
            echo json_encode([
                'success' => true,
                'tipo' => 'archivo',
                'contenido' => $ruta_web
            ]);
        } 
        // ESCENARIO 2: No hay archivo, generamos el TICKET DIGITAL
        else {
            // Preparamos las variables para la plantilla
            $n_comprobante = $n_comprob_vta;
            
            // Formatear fecha
            $fecha_bd = $resultado['fecha_comprob'] ?? date('Y-m-d');
            $fecha_emision = date('d/m/Y', strtotime($fecha_bd));
            
            // Datos cliente
            $nombre_cliente = ($resultado['nombre_cliente'] ?? '') . ' ' . ($resultado['apellido_cliente'] ?? '');
            if (trim($nombre_cliente) == '') $nombre_cliente = 'Consumidor Final';
            $direccion = $resultado['direccion_cliente'] ?? '';

            // Concepto y Monto
            $tipo = $resultado['tipo_cobro'] ?? 'Pago';
            $presupuesto = $resultado['id_presupuesto'] ?? '';
            $concepto = $tipo . ($presupuesto ? " (Presupuesto #$presupuesto)" : " a cuenta");
            
            $monto = $resultado['total_comprob_vta'] ?? 0;

            // Iniciar captura de salida (Output Buffering)
            ob_start();
            // Importar la plantilla que usará las variables de arriba
            include '../templates/ticket_pago.php';
            // Guardar el HTML generado en una variable
            $html_ticket = ob_get_clean();

            echo json_encode([
                'success' => true,
                'tipo' => 'html',
                'contenido' => $html_ticket
            ]);
        }
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Comprobante no encontrado en la base de datos.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>