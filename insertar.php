<?php

    include_once('conexion.php');

    $sql = "INSERT INTO cliente (nombre_cliente, apellido_cliente,
    telefono_cliente, direccion_cliente )

    VALUES('Mauricio','Sevilla','3454946783','Urquizaz 243');";

    if($conexion->query($sql) === TRUE){

        echo "Registros exitoso.";

    }else{

        echo $conexion->error; 
    }

    $conexion->close();
?>