<?php
// filepath: c:\xampp\htdocs\proyecto_final\bd\crud_comprobantes_venta.php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $conexion->beginTransaction();

    // Leemos la opción para saber si es ALTA (1) o BAJA (3)
    $opcion = isset($_POST['opcion']) ? intval($_POST['opcion']) : 0;

    if ($opcion == 1) {
        // --- LÓGICA PARA CREAR UN NUEVO COMPROBANTE ---

        // 1. Insertar en comprobantevta
        $n_comprob_vta = $_POST['n_comprob_vta'];
        $tipo_factura = $_POST['tipo_factura'];
        $id_cliente = $_POST['cliente'];

        $consulta = "INSERT INTO comprobantevta (n_comprob_vta, tipo_factura_vta, id_cliente) VALUES(?, ?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$n_comprob_vta, $tipo_factura, $id_cliente]);

        $id_comprob_vta = $conexion->lastInsertId();

        // 2. Insertar en cobro
        $fecha = $_POST['fecha'];
        $monto = $_POST['monto'];
        
        $consulta = "INSERT INTO cobro (fecha_cobro, precio_total_cobro, id_comprob_vta) VALUES(?, ?, ?)";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$fecha, $monto, $id_comprob_vta]);

        // 3. Guardar archivo si existe
        if(isset($_FILES['archivo']) && $_FILES['archivo']['error'] == 0) {
            $archivo = $_FILES['archivo'];
            $nombre_archivo = 'venta_' . $id_comprob_vta . '_' . time() . '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $ruta_directorio = '../uploads/comprobantes_venta/';
            
            if(!file_exists($ruta_directorio)) {
                mkdir($ruta_directorio, 0777, true);
            }
            
            move_uploaded_file($archivo['tmp_name'], $ruta_directorio . $nombre_archivo);
            
            $consulta = "UPDATE comprobantevta SET archivo = ? WHERE id_comprob_vta = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$nombre_archivo, $id_comprob_vta]);
        }

        $response = ['success' => true, 'message' => 'Comprobante guardado correctamente'];

    } elseif ($opcion == 3) {
        // --- LÓGICA PARA ELIMINAR UN COMPROBANTE ---
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if (!$id) throw new Exception('ID de comprobante no válido.');

        // 1. Opcional: Obtener nombre del archivo para borrarlo del servidor
        $consulta = "SELECT archivo FROM comprobantevta WHERE id_comprob_vta = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado && !empty($resultado['archivo'])) {
            $ruta_archivo = '../uploads/comprobantes_venta/' . $resultado['archivo'];
            if (file_exists($ruta_archivo)) {
                unlink($ruta_archivo); // Borra el archivo físico
            }
        }

        // 2. Eliminar el registro de cobro asociado
        $consulta = "DELETE FROM cobro WHERE id_comprob_vta = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id]);

        // 3. Eliminar el comprobante principal
        $consulta = "DELETE FROM comprobantevta WHERE id_comprob_vta = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->execute([$id]);

        $response = ['success' => true, 'message' => 'Comprobante eliminado correctamente'];
    
    } else {
        throw new Exception('Operación no válida.');
    }

    $conexion->commit();
    echo json_encode($response);

} catch(Exception $e) {
    $conexion->rollBack();
    http_response_code(500); 
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>