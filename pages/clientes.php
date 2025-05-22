<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = 'SELECT id_cliente, nombre_cliente, apellido_cliente, telefono_cliente, direccion_cliente FROM cliente';
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Clientes</title>
        
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        
        <!-- DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
        
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Gestión de Clientes</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <button id="btnNuevo" type="button" class="btn btn-success">Nuevo Cliente</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaClientes" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Telefono</th>
                                    <th>Direccion</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($data as $dat) {
                                ?>
                                <tr>
                                    <td><?php echo $dat['id_cliente'] ?></td>
                                    <td><?php echo $dat['nombre_cliente'] ?></td>
                                    <td><?php echo $dat['apellido_cliente'] ?></td>
                                    <td><?php echo $dat['telefono_cliente'] ?></td>
                                    <td><?php echo $dat['direccion_cliente'] ?></td>
                                    <td></td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para CRUD -->
        <div class="modal fade" id="modalCRUD" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formClientes">    
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nombre_cliente" class="col-form-label">Nombre:</label>
                                <input type="text" class="form-control" id="nombre_cliente">
                            </div>
                            <div class="form-group">
                                <label for="apellido_cliente" class="col-form-label">Apellido:</label>
                                <input type="text" class="form-control" id="apellido_cliente">
                            </div>
                            <div class="form-group">
                                <label for="telefono_cliente" class="col-form-label">Telefono:</label>
                                <input type="text" class="form-control" id="telefono_cliente">
                            </div>
                            <div class="form-group">
                                <label for="direccion_cliente" class="col-form-label">Direccion:</label>
                                <input type="text" class="form-control" id="direccion_cliente">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
                        </div>
                    </form>    
                </div>
            </div>
        </div>
        <script src="js/clientesScript.js"></script>
    </body>
</html>