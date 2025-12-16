<?php
include_once 'conexion.php';

// 1. Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Falta el ID del presupuesto.");
}
$id_presupuesto = $_GET['id'];

try {
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    // 2. Obtener Cabecera
    // Mantenemos la consulta de cabecera que ya funcionaba (ajustada a tus tablas)
    $sql = "SELECT p.id_presupuesto, p.fecha_presup, p.precio_total_presup, p.observaciones,
                   c.nombre_cliente, c.apellido_cliente, c.direccion_cliente, c.telefono_cliente
            FROM presupuesto p 
            INNER JOIN cliente c ON p.id_cliente = c.id_cliente 
            WHERE p.id_presupuesto = :id";
            
    $stmt = $conexion->prepare($sql);
    $stmt->execute([':id' => $id_presupuesto]);
    $cabecera = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cabecera) die("Presupuesto no encontrado (ID: $id_presupuesto).");

    // 3. Obtener Productos (EL CAMBIO CLAVE ESTÁ AQUÍ)
    // Ya no hacemos JOIN con producto_terminado para evitar errores de columnas.
    // Usamos directamente tus columnas: 'productos', 'kilos_torta', 'precio_unitario'
    $sqlItems = "SELECT productos as nombre, kilos_torta as cantidad, precio_unitario as precio 
                 FROM detalle_presupuesto 
                 WHERE id_presupuesto = :id";
    
    $stmtItems = $conexion->prepare($sqlItems);
    $stmtItems->execute([':id' => $id_presupuesto]);
    $items_db = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // 4. Preparar variables para la plantilla
    $n_presupuesto = $cabecera['id_presupuesto'];
    $fecha_emision = date('d/m/Y', strtotime($cabecera['fecha_presup']));
    
    // Concatenar nombre y apellido
    $cliente = $cabecera['nombre_cliente'];
    if (!empty($cabecera['apellido_cliente'])) {
        $cliente .= ' ' . $cabecera['apellido_cliente'];
    }
    
    $direccion = $cabecera['direccion_cliente'];
    $telefono = $cabecera['telefono_cliente'];
    $total = $cabecera['precio_total_presup'];
    $observaciones = $cabecera['observaciones']; // Puedes mostrar esto en la plantilla si quieres

    // Mapear items (Ahora los nombres vienen directo de la consulta)
    $items = [];
    foreach($items_db as $row) {
        $items[] = [
            'nombre' => $row['nombre'],
            'cantidad' => $row['cantidad'], // Esto mostrará los Kilos
            'precio' => $row['precio'],
            'subtotal' => $row['cantidad'] * $row['precio']
        ];
    }

    // 5. Cargar la Plantilla Visual
    include '../templates/presupuesto_plantilla.php';

    // 6. Lanzar impresión automática
    echo '<script>window.onload = function() { window.print(); }</script>';

} catch (Exception $e) {
    echo "Error de Base de Datos: " . $e->getMessage();
}
?>