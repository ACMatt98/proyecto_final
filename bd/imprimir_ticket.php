<?php
include_once 'conexion.php';

// 1. Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: ID no proporcionado.");
}
$id = $_GET['id'];

try {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    // 2. Obtener los datos del comprobante, cliente y detalle del cobro
    $sql = "SELECT c.n_comprob_vta, c.fecha_comprob, c.total_comprob_vta,
                   cli.nombre_cliente, cli.apellido_cliente, cli.direccion_cliente,
                   cob.tipo_cobro, cob.id_presupuesto
            FROM comprobantevta c
            INNER JOIN cliente cli ON c.id_cliente = cli.id_cliente
            LEFT JOIN cobro cob ON c.id_comprob_vta = cob.id_comprob_vta
            WHERE c.id_comprob_vta = :id";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $id]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        die("Comprobante no encontrado.");
    }

    // 3. Preparar las variables que espera la plantilla 'ticket_pago.php'
    $n_comprobante = $datos['n_comprob_vta'];
    $fecha_emision = date('d/m/Y', strtotime($datos['fecha_comprob']));
    $nombre_cliente = $datos['nombre_cliente'] . ' ' . $datos['apellido_cliente'];
    $direccion = $datos['direccion_cliente'];
    $monto = $datos['total_comprob_vta'];
    
    // concepto descriptivo
    $tipo = $datos['tipo_cobro'] ?? 'Pago'; // Seña o Pago
    $id_presup = $datos['id_presupuesto'] ?? '---';
    $concepto = strtoupper($tipo) . " (Presupuesto #$id_presup)";

    // 4. Cargar la plantilla visual
    ob_start();
    include '../templates/ticket_pago.php'; 
    $html = ob_get_clean();
    echo $html;

    // 5. Script automático para imprimir
    echo '<script>
        window.onload = function() { 
            window.print(); 
        }
    </script>';

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>