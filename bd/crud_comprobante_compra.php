<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Receive JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Extract data from JSON
$fecha = isset($data['fecha']) ? $data['fecha'] : '';
$n_de_comprob = isset($data['n_de_comprob']) ? $data['n_de_comprob'] : '';
$precio_total = isset($data['precio_total']) ? $data['precio_total'] : '';
$id_proveedor = isset($data['id_proveedor']) ? $data['id_proveedor'] : '';
$opcion = isset($data['opcion']) ? $data['opcion'] : '';
$id = isset($data['id']) ? $data['id'] : '';

$data = array(); // Initialize response array

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO comprobantecompra (fecha, n_de_comprob, precio_total, id_proveedor) 
                    VALUES('$fecha', '$n_de_comprob', '$precio_total', '$id_proveedor')";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT cc.id_compro_comp, cc.fecha, cc.n_de_comprob, cc.precio_total, 
                            p.nombre_proveedor, cc.id_proveedor
                     FROM comprobantecompra cc 
                     JOIN proveedor p ON cc.id_proveedor = p.id_proveedor 
                     WHERE cc.id_compro_comp = LAST_INSERT_ID()";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: //modificación
        $consulta = "UPDATE comprobantecompra 
                    SET fecha='$fecha', n_de_comprob='$n_de_comprob', 
                        precio_total='$precio_total', id_proveedor='$id_proveedor' 
                    WHERE id_compro_comp='$id'";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT cc.id_compro_comp, cc.fecha, cc.n_de_comprob, cc.precio_total, 
                            p.nombre_proveedor, cc.id_proveedor
                     FROM comprobantecompra cc 
                     JOIN proveedor p ON cc.id_proveedor = p.id_proveedor 
                     WHERE cc.id_compro_comp='$id'";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3://baja
        $consulta = "DELETE FROM comprobantecompra WHERE id_compro_comp='$id'";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data = array("success" => true);
        break;        
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;