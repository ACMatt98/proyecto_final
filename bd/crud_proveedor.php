<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Receive JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Extract data from JSON
$nombre_proveedor = isset($data['nombre_proveedor']) ? $data['nombre_proveedor'] : '';
$direccion_proveedor = isset($data['direccion_proveedor']) ? $data['direccion_proveedor'] : '';
$telefono_proveedor = isset($data['telefono_proveedor']) ? $data['telefono_proveedor'] : '';
$opcion = isset($data['opcion']) ? $data['opcion'] : '';
$id = isset($data['id']) ? $data['id'] : '';

$data = array(); // Initialize response array

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO proveedor (nombre_proveedor, direccion_proveedor, telefono_proveedor) VALUES('$nombre_proveedor', '$direccion_proveedor', '$telefono_proveedor') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT id_proveedor, nombre_proveedor, direccion_proveedor, telefono_proveedor FROM proveedor ORDER BY id_proveedor DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: //modificación
        $consulta = "UPDATE proveedor SET nombre_proveedor='$nombre_proveedor', direccion_proveedor='$direccion_proveedor', telefono_proveedor='$telefono_proveedor' WHERE id_proveedor='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT id_proveedor, nombre_proveedor, direccion_proveedor, telefono_proveedor FROM proveedor WHERE id_proveedor='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3://baja
        $consulta = "DELETE FROM proveedor WHERE id_proveedor='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = array("success" => true);
        break;        
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;