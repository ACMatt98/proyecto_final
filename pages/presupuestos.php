<?php
    include_once 'bd/conexion.php';
    $objeto = new Conexion();
    $conexion = $objeto->Conectar();

    $consulta = "SELECT p.id_presupuesto, 
                        CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
                        p.fecha_presup,
                        p.precio_total_presup,
                        e.estado
                 FROM presupuesto p
                 JOIN cliente c ON p.id_cliente = c.id_cliente
                 JOIN estado_presup e ON p.id_estado_presupuesto = e.id_estado";
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
    
    <!-- Iconos Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Gestión de Presupuestos</h1>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <button id="btnNuevo" type="button" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuevo Presupuesto
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaPresupuestos" class="table table-striped table-bordered table-condensed" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th style="display:none;">ID</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Monto Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                                <th>Gestión</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $dat): ?>
                            <tr>
                                <td style="display:none;"><?= $dat['id_presupuesto'] ?></td>
                                <td><?= $dat['cliente'] ?></td>
                                <td><?= $dat['fecha_presup'] ?></td>
                                <td>$<?= number_format($dat['precio_total_presup'], 2) ?></td>
                                <td><?= $dat['estado'] ?></td>
                                <td>
                                    <div class='text-center'>
                                        <div class='btn-group'>
                                            <button class='btn btn-primary btnEditar'><i class="bi bi-pencil-square"></i></button>
                                             <button class='btn btn-info btnVisualizar'><i class="bi bi-eye"></i></button>
                                            <button class='btn btn-danger btnBorrar'><i class="bi bi-trash"></i></button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class='text-center'>
                                        <div class='btn-group'>
                                            <button class='btn btn-info btnSeguimiento'><i class="bi bi-clipboard-check"></i></button>
                                            <button class='btn btn-success btnCobro'><i class="bi bi-cash-coin"></i></button>
                                            <button class='btn btn-secondary btnImprimir'><i class="bi bi-printer"></i></button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal CRUD -->
    <div class="modal fade" id="modalCRUD" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formPresupuestos">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Cliente</label>
                                    <select class="form-select" id="id_cliente" required>
                                        <?php
                                        $consulta = "SELECT id_cliente, CONCAT(nombre_cliente, ' ', apellido_cliente) AS nombre FROM cliente";
                                        $resultado = $conexion->prepare($consulta);
                                        $resultado->execute();
                                        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$row['id_cliente']}'>{$row['nombre']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha_presup" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="id_estado_presupuesto" required>
                                        <?php
                                        $consulta = "SELECT * FROM estado_presup";
                                        $resultado = $conexion->prepare($consulta);
                                        $resultado->execute();
                                        while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value='{$row['id_estado']}'>{$row['estado']}</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Monto Total</label>
                                    <input type="number" step="0.01" class="form-control" id="precio_total_presup" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lugar de Entrega</label>
                            <input type="text" class="form-control" id="lugar" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mano de Obra</label>
                            <input type="number" step="0.01" class="form-control" id="mano_obra" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Productos</label>
                            <div class="border p-2 mb-2" style="max-height: 200px; overflow-y: auto;" id="contenedor-productos">
                                <!-- Productos se agregarán aquí dinámicamente -->
                            </div>
                            <select class="form-select mb-2" id="select-productos">
                                <?php
                                $consulta = "SELECT id_producto_term, nomb_producto, precio_venta FROM producto_terminado";
                                $resultado = $conexion->prepare($consulta);
                                $resultado->execute();
                                while ($row = $resultado->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['id_producto_term']}' data-precio='{$row['precio_venta']}'>{$row['nomb_producto']} - $".number_format($row['precio_venta'], 2)."</option>";
                                }
                                ?>
                            </select>
                            <button type="button" class="btn btn-sm btn-success" id="btn-agregar-producto">Agregar Producto</button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" rows="3"></textarea>
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Seguimiento -->
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Seguimiento de Presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Estado Actual</label>
                        <select class="form-select" id="estado_seguimiento">
                            <option value="1">Completado</option>
                            <option value="2">En proceso</option>
                            <option value="3">Cancelado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones_seguimiento" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Materiales Requeridos</label>
                        <ul class="list-group" id="lista-materiales">
                            <!-- Se llenará dinámicamente -->
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Visualizar Presupuesto -->
<div class="modal fade" id="modalVisualizar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Detalles del Presupuesto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><strong>Cliente:</strong></h6>
                        <p id="visualizar-cliente"></p>
                    </div>
                    <div class="col-md-3">
                        <h6><strong>Fecha:</strong></h6>
                        <p id="visualizar-fecha"></p>
                    </div>
                    <div class="col-md-3">
                        <h6><strong>Estado:</strong></h6>
                        <p id="visualizar-estado"></p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><strong>Lugar de Entrega:</strong></h6>
                    <p id="visualizar-lugar"></p>
                </div>
                
                <h5 class="mt-4 mb-3">Productos</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" id="tabla-productos">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpo-tabla-productos">
                            <!-- Productos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6><strong>Observaciones:</strong></h6>
                        <p id="visualizar-observaciones"></p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5><strong>Mano de Obra: $<span id="visualizar-mano-obra">0.00</span></strong></h5>
                        <h4><strong>Total: $<span id="visualizar-total">0.00</span></strong></h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

    <script src="js/presupuestoScript.js"></script>
</body>
</html>