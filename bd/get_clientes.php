<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

try {
    $consulta = "SELECT id_cliente, nombre_cliente, apellido_cliente 
                FROM cliente 
                ORDER BY apellido_cliente, nombre_cliente";
    
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data);
} catch(Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conexion = NULL;
?>