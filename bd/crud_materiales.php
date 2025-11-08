<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

header('Content-Type: application/json');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($data === null) {
        throw new Exception('Error al decodificar JSON');
    }

    $opcion = intval($data['opcion'] ?? 0);
    $id = isset($data['id']) ? intval($data['id']) : null;
    
    switch ($opcion) {
        case 1: // ALTA
            $nombre_material = trim($data['nombre_material'] ?? '');
            $existencia = floatval($data['existencia'] ?? 0);
            $marca = trim($data['marca'] ?? '');
            $unidad_medida = trim($data['unidad_medida'] ?? '');

            if (empty($nombre_material) || empty($marca) || empty($unidad_medida)) {
                throw new Exception('Todos los campos son obligatorios');
            }

            $consulta = "INSERT INTO materiales (nombre_material, existencia, marca, unidad_medida) VALUES (:nombre, :existencia, :marca, :unidad)";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':nombre' => $nombre_material,
                ':existencia' => $existencia,
                ':marca' => $marca,
                ':unidad' => $unidad_medida
            ]);
            $response = ['success' => true, 'message' => 'Material creado correctamente'];
            break;

        case 2: // MODIFICACIÓN
            if (!$id) throw new Exception('ID de material no válido');

            $nombre_material = trim($data['nombre_material'] ?? '');
            $existencia = floatval($data['existencia'] ?? 0);
            $marca = trim($data['marca'] ?? '');
            $unidad_medida = trim($data['unidad_medida'] ?? '');

            if (empty($nombre_material) || empty($marca) || empty($unidad_medida)) {
                throw new Exception('Todos los campos son obligatorios');
            }

            $consulta = "UPDATE materiales SET nombre_material=:nombre, existencia=:existencia, marca=:marca, unidad_medida=:unidad WHERE id_materiales=:id";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute([
                ':nombre' => $nombre_material,
                ':existencia' => $existencia,
                ':marca' => $marca,
                ':unidad' => $unidad_medida,
                ':id' => $id
            ]);
            $response = ['success' => true, 'message' => 'Material actualizado correctamente'];
            break;

        case 3: // BAJA
            if (!$id) throw new Exception('ID de material no válido');

            $consulta = "DELETE FROM materiales WHERE id_materiales=:id";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([':id' => $id]);
            $response = ['success' => true, 'message' => 'Material eliminado correctamente'];
            break;

        default:
            throw new Exception('Operación no válida');
    }

    echo json_encode($response);

} catch (Exception $e) {
    // CORRECCIÓN: Línea completada y con formato de error estándar
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conexion = NULL;
?>