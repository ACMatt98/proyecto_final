<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT cc.id_compro_comp, cc.fecha, cc.n_de_comprob, cc.precio_total, 
                        p.nombre_proveedor 
                 FROM comprobantecompra cc 
                 JOIN proveedor p ON cc.id_proveedor = p.id_proveedor";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Comprobantes de Compra</title>
        
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
            <h1 class="h3 mb-2 text-gray-800">Gestión de Comprobantes de Compra</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <button id="btnNuevo" type="button" class="btn btn-success">Nuevo Comprobante</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaComprobantes" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>N° Comprobante</th>
                                    <th>Precio Total</th>
                                    <th>Proveedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($data as $dat) {
                                ?>
                                <tr>
                                    <td><?php echo $dat['id_compro_comp'] ?></td>
                                    <td><?php echo $dat['fecha'] ?></td>
                                    <td><?php echo $dat['n_de_comprob'] ?></td>
                                    <td><?php echo $dat['precio_total'] ?></td>
                                    <td><?php echo $dat['nombre_proveedor'] ?></td>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formComprobantes">    
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha" class="col-form-label">Fecha:</label>
                                <input type="date" class="form-control" id="fecha">
                            </div>
                            <div class="form-group">
                                <label for="n_de_comprob" class="col-form-label">N° Comprobante:</label>
                                <input type="number" class="form-control" id="n_de_comprob">
                            </div>
                            <div class="form-group">
                                <label for="precio_total" class="col-form-label">Precio Total:</label>
                                <input type="number" step="0.01" class="form-control" id="precio_total">
                            </div>
                            <div class="form-group">
                                <label for="id_proveedor" class="col-form-label">Proveedor:</label>
                                <select class="form-control" id="id_proveedor">
                                    <?php
                                    $consulta = "SELECT id_proveedor, nombre_proveedor FROM proveedor";
                                    $resultado = $conexion->prepare($consulta);
                                    $resultado->execute();
                                    $proveedores = $resultado->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($proveedores as $proveedor) {
                                        echo "<option value='" . $proveedor['id_proveedor'] . "'>" . $proveedor['nombre_proveedor'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-form-label">Materiales Comprados:</label>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tablaMaterialesCompra">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unitario</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody id="materialesCompraBody"></tbody>
                                    </table>
                                    <button type="button" class="btn btn-success btn-sm" id="btnAgregarMaterial">
                                        <i class="bi bi-plus-circle"></i> Agregar Material
                                    </button>
                                </div>
                            </div>
                        </div>
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

<template id="templateMaterial">
    <tr>
        <td>
            <select class="form-control material-select" required>
                <option value="">Seleccione material</option>
                <?php
                $consulta = "SELECT id_materiales, nombre_material FROM materiales";
                $resultado = $conexion->prepare($consulta);
                $resultado->execute();
                $materiales = $resultado->fetchAll(PDO::FETCH_ASSOC);
                foreach($materiales as $material) {
                    echo "<option value='".$material['id_materiales']."'>".$material['nombre_material']."</option>";
                }
                ?>
            </select>
        </td>
        <td><input type="number" step="0.01" class="form-control cantidad-input" required></td>
        <td><input type="number" step="0.01" class="form-control precio-input" required></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm btnEliminarMaterial">
                <i class="bi bi-trash"></i> Eliminar
            </button>
        </td>
    </tr>
</template>


        <script src="js/comprobanteCompraScript.js"></script>
    </body>
</html>