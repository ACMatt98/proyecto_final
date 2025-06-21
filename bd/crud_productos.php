<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$nomb_producto = (isset($data['nomb_producto'])) ? $data['nomb_producto'] : '';
$precio_venta = (isset($data['precio_venta'])) ? $data['precio_venta'] : '';
$costo_produccion = (isset($data['costo_produccion'])) ? $data['costo_produccion'] : '';
$stock = (isset($data['stock'])) ? $data['stock'] : '';
$opcion = (isset($data['opcion'])) ? $data['opcion'] : '';
$id = (isset($data['id'])) ? $data['id'] : '';

switch($opcion){
    case 1: // alta
        $consulta = "INSERT INTO producto_terminado (nomb_producto, precio_venta, costo_produccion, stock) 
                    VALUES('$nomb_producto', '$precio_venta', '$costo_produccion', '$stock') ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT id_producto_term, nomb_producto, precio_venta, costo_produccion, stock 
                    FROM producto_terminado ORDER BY id_producto_term DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: // modificación
        $consulta = "UPDATE producto_terminado 
                    SET nomb_producto='$nomb_producto', precio_venta='$precio_venta', 
                        costo_produccion='$costo_produccion', stock='$stock' 
                    WHERE id_producto_term='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT id_producto_term, nomb_producto, precio_venta, costo_produccion, stock 
                    FROM producto_terminado WHERE id_producto_term='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3: // baja
        $consulta = "DELETE FROM producto_terminado WHERE id_producto_term='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;
?>