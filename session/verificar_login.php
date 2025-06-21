<?php
session_start();
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($nombre && $password) {
        $consulta = "SELECT id_usuario, nombre, apellido, id_rol 
                    FROM usuario 
                    WHERE nombre = ? AND password = ?";
        
        $resultado = $conexion->prepare($consulta);
        $resultado->execute([$nombre, $password]);

        if ($resultado->rowCount() > 0) {
            $data = $resultado->fetch(PDO::FETCH_ASSOC);
            $_SESSION['id_usuario'] = $data['id_usuario'];
            $_SESSION['nombre'] = $data['nombre'];
            $_SESSION['apellido'] = $data['apellido'];
            $_SESSION['id_rol'] = $data['id_rol'];
            
            header("Location: ../index.php");
            exit();
        } else {
            header("Location: ../pages/login.php?error=1");
            exit();
        }
    } else {
        header("Location: ../pages/login.php?error=2");
        exit();
    }
}

header("Location: ../pages/login.php");
exit();
?>