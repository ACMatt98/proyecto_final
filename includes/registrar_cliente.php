<?php 
//INCLUDE
include '../conexion.php';


//if (isset($_POST['registrar'])){

 //   $nombre= $_POST[''];
  //  $apellido=
   // $telefon=
  //  $direccion=

//}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Registro de cliente</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
     <link href="https://unnpkg.com/aos2.3.1/dist/aos.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="col-sm-12 col-md-12 col-lg-12" >
        <h1>REGSITRO DE CLIENTES EN BASE DE DATOS</h1> 
        <form class="" action="<?php echo $_SERVER['PHP_SELF'];  ?>" method= "post" >
            <input type="text" name="nombre_cliente" placeholder="Nombre" class="form-control" required>
            <input type="text" name="apellido_cliente" placeholder="Apellido" class="form-control" required>
            <input type="text" name="telefono_cliente" placeholder="Telefono" class="form-control" required>
            <input type="text" name="direccion_cliente" placeholder="Direccion" class="form-control" required>
            <input type="submit" name="registrar" value="registrar"  class="btn btn-sm btn-block btn-succes" required>
        </form>
    </div>
   





  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>
</html>