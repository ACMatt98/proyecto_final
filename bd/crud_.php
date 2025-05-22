<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recibir el JSON y convertirlo a array PHP
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Recepción de los datos enviados mediante JSON desde el JS   
$nombre_cliente = (isset($data['nombre_cliente'])) ? $data['nombre_cliente'] : '';
$apellido_cliente = (isset($data['apellido_cliente'])) ? $data['apellido_cliente'] : '';
$telefono_cliente = (isset($data['telefono_cliente'])) ? $data['telefono_cliente'] : '';
$direccion_cliente = (isset($data['direccion_cliente'])) ? $data['direccion_cliente'] : '';
$opcion = (isset($data['opcion'])) ? $data['opcion'] : '';
$id = (isset($data['id'])) ? $data['id'] : '';

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO cliente (nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente) 
                    VALUES('$nombre_cliente', '$apellido_cliente', '$telefono_cliente', '$direccion_cliente') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente 
                    FROM cliente ORDER BY id_cliente DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: //modificación
        $consulta = "UPDATE cliente SET nombre_cliente='$nombre_cliente', apellido_cliente='$apellido_cliente', 
                    telefono_cliente='$telefono_cliente', direccion_cliente='$direccion_cliente' 
                    WHERE id_cliente='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente 
                    FROM cliente WHERE id_cliente='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3://baja
        $consulta = "DELETE FROM cliente WHERE id_cliente='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;