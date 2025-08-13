<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Configurar encabezados para respuesta JSON
header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    if ($json === false) {
        throw new Exception('Error al leer los datos de entrada');
    }
    
    $data = json_decode($json, true);
    if ($data === null) {
        throw new Exception('Error al decodificar JSON');
    }

    // Validar datos básicos
    $nomb_receta = isset($data['nomb_receta']) ? trim($data['nomb_receta']) : '';
    $costo_receta = 0;

    if (isset($data['ingredientes']) && is_array($data['ingredientes'])) {
        foreach ($data['ingredientes'] as $ingrediente) {
            $id_material = intval($ingrediente['id_materiales'] ?? 0);
            $cantidad = floatval($ingrediente['cantidad_nec'] ?? 0);

            if ($id_material <= 0 || $cantidad <= 0) {
                throw new Exception('Ingrediente inválido para cálculo de costo.');
            }

            // Obtener precio más reciente del material
            $stmtPrecio = $conexion->prepare("SELECT precio_unitario 
                FROM detallecomprob_cpra 
                WHERE id_materiales = :id 
                ORDER BY fecha_detalle DESC 
                LIMIT 1");
            $stmtPrecio->execute([':id' => $id_material]);
            $precio_unitario = $stmtPrecio->fetchColumn();

            if (!$precio_unitario) {
                throw new Exception("No se encontró precio para el material ID $id_material");
            }

            // Sumar al costo total
            $costo_receta += $cantidad * $precio_unitario;
        }
    } else {
        throw new Exception('No se recibieron ingredientes para calcular el costo');
    }

    $usar_calculo_automatico = true; // Cambiar a false si se quiere permitir ingreso manual
    if ($usar_calculo_automatico) {
        // [calcular]
    } else {
        $costo_receta = isset($data['costo_receta']) ? floatval($data['costo_receta']) : 0;
    }

    $id_producto_term = isset($data['id_producto_term']) ? intval($data['id_producto_term']) : 0;
    $opcion = isset($data['opcion']) ? intval($data['opcion']) : 0;
    $id = isset($data['id']) ? intval($data['id']) : 0;

    if (empty($nomb_receta)) {
        throw new Exception('El nombre de la receta es requerido');
    }

    if ($id_producto_term <= 0) {
        throw new Exception('Debe seleccionar un producto válido');
    }

    switch($opcion) {
        case 1: // Alta de receta
            $conexion->beginTransaction();

            // Insertar receta con parámetros preparados
            $consulta = "INSERT INTO receta_estandar (nomb_receta, costo_receta, id_producto_term) 
                        VALUES(:costo_receta, :nomb_receta, :id_producto_term)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':costo_receta' => $costo_receta,
                ':nomb_receta' => $nomb_receta,
                ':id_producto_term' => $id_producto_term
            ]);
            
            $id_receta = $conexion->lastInsertId();

            // Insertar ingredientes si existen
            if(isset($data['ingredientes']) && is_array($data['ingredientes'])) {
                $stmt = $conexion->prepare("INSERT INTO detalle_receta 
                    (cantidad_nec, unidad_medida, id_receta_estandar, id_materiales) 
                    VALUES (:cantidad, :unidad, :id_receta, :id_materiales)");
                
                foreach($data['ingredientes'] as $ingrediente) {
                    $id_material = intval($ingrediente['id_materiales'] ?? 0);
                    $cantidad = floatval($ingrediente['cantidad_nec'] ?? 0);
                    $unidad = trim($ingrediente['unidad_medida'] ?? '');
                    
                    if ($id_material <= 0 || $cantidad <= 0 || empty($unidad)) {
                        throw new Exception('Datos de ingrediente inválidos');
                    }
                    
                    $stmt->execute([
                        ':cantidad' => $cantidad,
                        ':unidad' => $unidad,
                        ':id_receta' => $id_receta,
                        ':id_materiales' => $id_material
                    ]);
                }
            }

            $conexion->commit();

            // Obtener datos para respuesta
            $consulta = "SELECT re.id_receta_estandar, re.nomb_receta, re.costo_receta, pt.nomb_producto 
                        FROM receta_estandar re 
                        JOIN producto_terminado pt ON re.id_producto_term = pt.id_producto_term 
                        WHERE re.id_receta_estandar = :id_receta";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([':id_receta' => $id_receta]);
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $data,
                'message' => 'Receta agregada correctamente'
            ]);
            break;

        // ... (los otros casos case 2 y case 3 pueden mantenerse similares con las mismas mejoras)

        default:
            throw new Exception('Opción no válida');
    }
} catch (Exception $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conexion = NULL;
?>