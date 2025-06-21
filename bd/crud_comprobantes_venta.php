<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

try {
    $conexion->beginTransaction();

    // Insertar en comprobantevta
    $n_comprob_vta = $_POST['n_comprob_vta'];
    $tipo_factura = $_POST['tipo_factura'];
    $id_cliente = $_POST['cliente'];

$consulta = "INSERT INTO comprobantevta (n_comprob_vta, tipo_factura_vta, id_cliente) 
            VALUES(?, ?, ?)";
$stmt = $conexion->prepare($consulta);
$stmt->execute([$n_comprob_vta, $tipo_factura, $id_cliente]);

    // Insertar en cobro
    $fecha = $_POST['fecha'];
    $monto = $_POST['monto'];
    
    $consulta = "INSERT INTO cobro (fecha_cobro, precio_total_cobro, id_comprob_vta) 
                VALUES(?, ?, ?)";
    $stmt = $conexion->prepare($consulta);
    $stmt->execute([$fecha, $monto, $id_comprob_vta]);

    // Guardar archivo si existe
    if(isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
        $archivo = $_FILES['archivo'];
        $nombre_archivo = $n_comprob_vta . '_' . time() . '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $ruta = '../uploads/comprobantes/' . $nombre_archivo;
        
        if(!file_exists('../uploads/comprobantes/')) {
            mkdir('../uploads/comprobantes/', 0777, true);
        }
        
        move_uploaded_file($archivo['tmp_name'], $ruta);
        
        // Actualizar ruta en la base de datos
        $consulta = "UPDATE comprobantevta SET archivo = ? WHERE id_comprob_vta = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$nombre_archivo, $id_comprob_vta]);
    }

    $conexion->commit();
    echo json_encode(['success' => true]);

} catch(Exception $e) {
    $conexion->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>