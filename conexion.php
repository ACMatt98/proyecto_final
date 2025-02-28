<?php
    $servidor    ='localhost';
    $usuario     = 'root';
    $contrasena  = '';
    $bd          = 'reposteria';

    //conexion creada
    $conexion = new mysqli($servidor, $usuario, $contrasena, $bd);


    //validacion
    if($conexion->connect_error){

        echo die('Hubo un error en la conexion' . $conexion->connect_error);
    };


?>