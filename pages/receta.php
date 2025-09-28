<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT re.id_receta_estandar, re.nomb_receta, re.costo_receta, pt.nomb_producto 
                 FROM receta_estandar re 
                 JOIN producto_terminado pt ON re.id_producto_term = pt.id_producto_term";
    $resultado = $conexion->prepare($consulta);
    $resultado->execute();
    $data = $resultado->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestión de Recetas</title>
        
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        
        <!-- DataTables -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css" />
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.js"></script>
        
        <!-- Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    </head>
    <body>
        <div class="container-fluid">
            <h1 class="h3 mb-2 text-gray-800">Gestión de Recetas</h1>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <button id="btnNuevo" type="button" class="btn btn-success">Nueva Receta</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablaRecetas" class="table table-striped table-bordered table-condensed" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre Receta</th>
                                    <th>Costo</th>
                                    <th>Producto</th>
                                    <th>Ingredientes</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach($data as $dat) {
                                ?>
                                <tr>
                                    <td><?php echo $dat['id_receta_estandar'] ?></td>
                                    <td><?php echo $dat['nomb_receta'] ?></td>
                                    <td><?php echo $dat['costo_receta'] ?></td>
                                    <td><?php echo $dat['nomb_producto'] ?></td>
                                    <td>
                                        <button class="btn btn-info btnIngredientes">Ver Ingredientes</button>
                                    </td>
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
                    <form id="formRecetas">    
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nomb_receta" class="col-form-label">Nombre de la Receta:</label>
                                        <input type="text" class="form-control" id="nomb_receta" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="costo_receta" class="col-form-label">Costo:</label>
                                        <input type="number" step="0.01" class="form-control" id="costo_receta" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="id_producto_term" class="col-form-label">Producto:</label>
                                        <select class="form-control" id="id_producto_term" required>
                                            <option value="">Seleccione un producto</option>
                                            <?php
                                            $consulta = "SELECT id_producto_term, nomb_producto FROM producto_terminado";
                                            $resultado = $conexion->prepare($consulta);
                                            $resultado->execute();
                                            $productos = $resultado->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($productos as $producto) {
                                                echo "<option value='".$producto['id_producto_term']."'>".$producto['nomb_producto']."</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-form-label">Ingredientes:</label>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="tablaIngredientesForm">
                                                <thead>
                                                    <tr>
                                                        <th>Material</th>
                                                        <th>Cantidad</th>
                                                        <th>Unidad</th>
                                                        <th>Precio</th>
                                                        <th>Subtotal</th>
                                                        <th>Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ingredientesFormBody">
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-success btn-sm" id="btnAgregarIngrediente">
                                                <i class="bi bi-plus-circle"></i> Agregar Ingrediente
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

        <!-- Modal para ver Ingredientes -->
        <div class="modal fade" id="modalIngredientes" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ingredientes de la Receta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tablaIngredientes" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Material</th>
                                        <th>Cantidad</th>
                                        <th>Unidad de Medida</th>
                                    </tr>
                                </thead>
                                <tbody id="ingredientesBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Template para nueva fila de ingrediente -->
        <template id="templateIngrediente">
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
                <td>
                    <input type="number" step="0.01" class="form-control cantidad-input" required>
                </td>
                <td>
                    <input type="text" class="form-control unidad-input" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control precio-input" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control subtotal-input" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btnEliminarIngrediente">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        </template>

        <script src="js/recetasScript.js"></script>
    </body>
</html>