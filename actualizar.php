<?php

include_once('conexion.php');

$sql = "UPDATE cliente SET 
direccion_cliente = 'Uruguay 18' WHERE id_cliente = 1";

if($conexion->query($sql) === TRUE ){

    echo'Registro actualizado correctamente';

}else{

    echo $conexion->error;

}

$conexion->close();

?>