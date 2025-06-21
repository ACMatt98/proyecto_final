<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$nomb_receta = (isset($data['nomb_receta'])) ? $data['nomb_receta'] : '';
$costo_receta = (isset($data['costo_receta'])) ? $data['costo_receta'] : '';
$id_producto_term = (isset($data['id_producto_term'])) ? $data['id_producto_term'] : '';
$opcion = (isset($data['opcion'])) ? $data['opcion'] : '';
$id = (isset($data['id'])) ? $data['id'] : '';

switch($opcion){
    case 1: //alta
        try {
            $conexion->beginTransaction();

            // Insertar receta
            $consulta = "INSERT INTO receta_estandar (nomb_receta, costo_receta, id_producto_term) 
                        VALUES('$nomb_receta', '$costo_receta', '$id_producto_term') ";			
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            
            $id_receta = $conexion->lastInsertId();

            // Insertar ingredientes
            if(isset($data['ingredientes'])) {
                $stmt = $conexion->prepare("INSERT INTO detalle_receta (id_receta_estandar, id_materiales, cantidad_nec, unidad_medida) 
                                          VALUES (?, ?, ?, ?)");
                
                foreach($data['ingredientes'] as $ingrediente) {
                    $stmt->execute([
                        $id_receta,
                        $ingrediente['id_materiales'],
                        $ingrediente['cantidad_nec'],
                        $ingrediente['unidad_medida']
                    ]);
                }
            }

            $conexion->commit();

            // Obtener datos para respuesta
            $consulta = "SELECT re.id_receta_estandar, re.nomb_receta, re.costo_receta, pt.nomb_producto 
                        FROM receta_estandar re 
                        JOIN producto_terminado pt ON re.id_producto_term = pt.id_producto_term 
                        WHERE re.id_receta_estandar = $id_receta";
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            $conexion->rollBack();
            throw $e;
        }
        break;

    case 2: //modificación
        try {
            $conexion->beginTransaction();

            // Actualizar receta
            $consulta = "UPDATE receta_estandar SET nomb_receta='$nomb_receta', costo_receta='$costo_receta', 
                        id_producto_term='$id_producto_term' WHERE id_receta_estandar='$id' ";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();

            // Eliminar ingredientes anteriores
            $consulta = "DELETE FROM detalle_receta WHERE id_receta_estandar = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);

            // Insertar nuevos ingredientes
            if(isset($data['ingredientes'])) {
                $stmt = $conexion->prepare("INSERT INTO detalle_receta (id_receta_estandar, id_materiales, cantidad_nec, unidad_medida) 
                                          VALUES (?, ?, ?, ?)");
                
                foreach($data['ingredientes'] as $ingrediente) {
                    $stmt->execute([
                        $id,
                        $ingrediente['id_materiales'],
                        $ingrediente['cantidad_nec'],
                        $ingrediente['unidad_medida']
                    ]);
                }
            }

            $conexion->commit();

            // Obtener datos para respuesta
            $consulta = "SELECT re.id_receta_estandar, re.nomb_receta, re.costo_receta, pt.nomb_producto 
                        FROM receta_estandar re 
                        JOIN producto_terminado pt ON re.id_producto_term = pt.id_producto_term 
                        WHERE re.id_receta_estandar='$id' ";       
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
            $data = $resultado->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            $conexion->rollBack();
            throw $e;
        }
        break;        

    case 3://baja
        try {
            $conexion->beginTransaction();
            
            // Eliminar ingredientes primero
            $consulta = "DELETE FROM detalle_receta WHERE id_receta_estandar = ?";
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);
            
            // Luego eliminar la receta
            $consulta = "DELETE FROM receta_estandar WHERE id_receta_estandar = ?";		
            $stmt = $conexion->prepare($consulta);
            $stmt->execute([$id]);
            
            $conexion->commit();
            $data = null;
            
        } catch(Exception $e) {
            $conexion->rollBack();
            throw $e;
        }
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;