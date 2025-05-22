<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT p.id_presupuesto, p.fecha_presup, p.lugar, p.precio_total_presup, 
                        p.mano_obra, e.estado, c.nombre_cliente, c.apellido_cliente
                 FROM presupuesto p 
                 LEFT JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado
                 LEFT JOIN cliente c ON p.id_cliente = c.id_cliente";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Presupuestos</title>
        
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
            <h1 class="h3 mb-2 text-gray-800">Gestión de Presupuestos</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <button id="btnNuevo" type="button" class="btn btn-success">Nuevo Presupuesto</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaPresupuestos" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Lugar</th>
                                    <th>Precio Total</th>
                                    <th>Mano de Obra</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($data as $dat) {
                                ?>
                                <tr>
                                    <td><?php echo $dat['id_presupuesto'] ?></td>
                                    <td><?php echo $dat['fecha_presup'] ?></td>
                                    <td><?php echo $dat['nombre_cliente'] . ' ' . $dat['apellido_cliente'] ?></td>
                                    <td><?php echo $dat['lugar'] ?></td>
                                    <td><?php echo $dat['precio_total_presup'] ?></td>
                                    <td><?php echo $dat['mano_obra'] ?></td>
                                    <td><?php echo $dat['estado'] ?></td>
                                    <td>
                                        <div class='text-center'>
                                            <div class='btn-group'>
                                                <button class='btn btn-primary btnEditar'>Editar</button>
                                                <button class='btn btn-danger btnBorrar'>Borrar</button>
                                            </div>
                                        </div>
                                    </td>
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
                    <form id="formPresupuestos">    
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="fecha_presup">Fecha</label>
                                <input type="date" class="form-control" id="fecha_presup">
                            </div>
                            <div class="form-group">
                                <label for="id_cliente">Cliente</label>
                                <select class="form-control" id="id_cliente">
                                    <?php
                                    $consulta = "SELECT id_cliente, nombre_cliente, apellido_cliente FROM cliente";
                                    $resultado = $conexion->prepare($consulta);
                                    $resultado->execute();
                                    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($data as $dat) {
                                        echo '<option value="'.$dat['id_cliente'].'">'.$dat['nombre_cliente'].' '.$dat['apellido_cliente'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="lugar">Lugar</label>
                                <input type="text" class="form-control" id="lugar">
                            </div>
                            <div class="form-group">
                                <label for="precio_total_presup">Precio Total</label>
                                <input type="number" step="0.01" class="form-control" id="precio_total_presup">
                            </div>
                            <div class="form-group">
                                <label for="mano_obra">Mano de Obra</label>
                                <input type="number" step="0.01" class="form-control" id="mano_obra">
                            </div>
                            <div class="form-group">
                                <label for="id_estado_presupuesto">Estado</label>
                                <select class="form-control" id="id_estado_presupuesto">
                                    <?php
                                    $consulta = "SELECT id_estado, estado FROM estado_presup";
                                    $resultado = $conexion->prepare($consulta);
                                    $resultado->execute();
                                    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($data as $dat) {
                                        echo '<option value="'.$dat['id_estado'].'">'.$dat['estado'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
                    </form>    
                </div>
            </div>
        </div>
        <script src="js/presupuestoScript.js"></script>
    </body>
</html>