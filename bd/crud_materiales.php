<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recibir el JSON y convertirlo a array PHP
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Recepción de los datos enviados mediante JSON desde el JS   
$nombre_material = (isset($data['nombre_material'])) ? $data['nombre_material'] : '';
$existencia = (isset($data['existencia'])) ? $data['existencia'] : '';
$marca = (isset($data['marca'])) ? $data['marca'] : '';
$opcion = (isset($data['opcion'])) ? $data['opcion'] : '';
$id = (isset($data['id'])) ? $data['id'] : '';

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO materiales (nombre_material, existencia, marca) 
                    VALUES('$nombre_material', '$existencia', '$marca') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT id_materiales, nombre_material, existencia, marca 
                    FROM materiales ORDER BY id_materiales DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: //modificación
        $consulta = "UPDATE materiales SET nombre_material='$nombre_material', existencia='$existencia', 
                    marca='$marca' WHERE id_materiales='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT id_materiales, nombre_material, existencia, marca 
                    FROM materiales WHERE id_materiales='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3://baja
        $consulta = "DELETE FROM materiales WHERE id_materiales='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;