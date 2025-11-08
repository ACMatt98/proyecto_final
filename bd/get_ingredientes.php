<?php
include_once 'conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id_receta = (isset($data['id_receta'])) ? $data['id_receta'] : '';

$consulta = "SELECT m.nombre_material, dr.cantidad_nec, dr.unidad_medida, dr.id_materiales
            FROM detalle_receta dr 
            JOIN materiales m ON dr.id_materiales = m.id_materiales 
            WHERE dr.id_receta_estandar = ?";
            
$resultado = $conexion->prepare($consulta);
$resultado->execute([$id_receta]);
$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

print json_encode($data, JSON_UNESCAPED_UNICODE);
$conexion = NULL;