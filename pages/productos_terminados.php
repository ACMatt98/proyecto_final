<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = 'SELECT id_producto_term, nomb_producto, precio_venta, costo_produccion, stock FROM producto_terminado';
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Productos Terminados</title>
        
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
            <h1 class="h3 mb-2 text-gray-800">Gestión de Productos Terminados</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <button id="btnNuevo" type="button" class="btn btn-success">Nuevo Producto</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaProductos" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Precio Venta</th>
                                    <th>Costo Producción</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($data as $dat) { ?>
                                <tr>
                                    <td><?php echo $dat['id_producto_term'] ?></td>
                                    <td><?php echo $dat['nomb_producto'] ?></td>
                                    <td><?php echo $dat['precio_venta'] ?></td>
                                    <td><?php echo $dat['costo_produccion'] ?></td>
                                    <td><?php echo $dat['stock'] ?></td>
                                    <td></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal CRUD -->
            <div class="modal fade" id="modalCRUD" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formProductos">    
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="nomb_producto">Nombre del Producto:</label>
                                    <input type="text" class="form-control" id="nomb_producto">
                                </div>
                                <div class="form-group">
                                    <label for="precio_venta">Precio de Venta:</label>
                                    <input type="number" step="0.01" class="form-control" id="precio_venta">
                                </div>
                                <div class="form-group">
                                    <label for="costo_produccion">Costo de Producción:</label>
                                    <input type="number" step="0.01" class="form-control" id="costo_produccion">
                                </div>
                                <div class="form-group">
                                    <label for="stock">Stock:</label>
                                    <input type="number" class="form-control" id="stock">
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
        </div>
        <script src="js/productosScript.js"></script>
    </body>
</html>