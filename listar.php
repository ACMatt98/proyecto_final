<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista</title>
</head>
<body>
    
    <?php
        include_once('conexion.php');
        $sql = "SELECT id_cliente, nombre_cliente, 
        apellido_cliente, telefono_cliente, direccion_cliente
        FROM cliente";

        $resultado = $conexion->query($sql);

        //se valida y muestra datos
        if($resultado->num_rows > 0){

            //hay data
            while ($row = $resultado->fetch_assoc()){

                echo "<hr> id:" . $row ["id_cliente"] . 
                "-Nombre Cliente: " . $row["nombre_cliente"] . 
                " " . $row["apellido_cliente"] . "<hr>" . 
                "-Telefono: " . $row["telefono_cliente"] . 
                " " . "-Direccion: " . $row["direccion_cliente"] . 
                "<hr>";
            }
        }else{
                echo "Aun no hay informacion";
        }
            

        $conexion ->close();
    ?>

</body>
</html>