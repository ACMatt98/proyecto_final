<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Recepción de los datos enviados mediante POST desde el JS   
$fecha_presup = (isset($_POST['fecha_presup'])) ? $_POST['fecha_presup'] : '';
$lugar = (isset($_POST['lugar'])) ? $_POST['lugar'] : '';
$precio_total_presup = (isset($_POST['precio_total_presup'])) ? $_POST['precio_total_presup'] : '';
$mano_obra = (isset($_POST['mano_obra'])) ? $_POST['mano_obra'] : '';
$id_estado_presupuesto = (isset($_POST['id_estado_presupuesto'])) ? $_POST['id_estado_presupuesto'] : '';
$id_cliente = (isset($_POST['id_cliente'])) ? $_POST['id_cliente'] : '';
$id_comprob_vta = (isset($_POST['id_comprob_vta'])) ? $_POST['id_comprob_vta'] : '1'; // Valor por defecto temporal
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';

switch($opcion){
    case 1: //alta
        $consulta = "INSERT INTO presupuesto (fecha_presup, lugar, precio_total_presup, mano_obra, id_estado_presupuesto, id_cliente, id_comprob_vta) 
                    VALUES('$fecha_presup', '$lugar', '$precio_total_presup', '$mano_obra', '$id_estado_presupuesto', '$id_cliente', '$id_comprob_vta')";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 

        $consulta = "SELECT p.*, e.estado, c.nombre_cliente, c.apellido_cliente 
                    FROM presupuesto p 
                    LEFT JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                    LEFT JOIN cliente c ON p.id_cliente = c.id_cliente 
                    WHERE p.id_presupuesto = LAST_INSERT_ID()";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 2: //modificación
        $consulta = "UPDATE presupuesto 
                    SET fecha_presup='$fecha_presup', 
                        lugar='$lugar', 
                        precio_total_presup='$precio_total_presup', 
                        mano_obra='$mano_obra', 
                        id_estado_presupuesto='$id_estado_presupuesto', 
                        id_cliente='$id_cliente' 
                    WHERE id_presupuesto='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        
        $consulta = "SELECT p.*, e.estado, c.nombre_cliente, c.apellido_cliente 
                    FROM presupuesto p 
                    LEFT JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                    LEFT JOIN cliente c ON p.id_cliente = c.id_cliente 
                    WHERE p.id_presupuesto='$id'";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;        

    case 3://baja
        $consulta = "DELETE FROM presupuesto WHERE id_presupuesto='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;        
}

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;