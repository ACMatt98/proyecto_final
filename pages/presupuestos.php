<?php
    include_once 'bd/conexion.php';
    include_once 'bd/funciones.php';

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
                                <td><?php echo FormatoFechas::cambiaFormatoFecha($dat['fecha_presup']) ?></td>
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
                                    <select class="form-select" id="id_cliente" name="id_cliente" required>
                                        <option value="">Seleccione un cliente...</option>
                                        <?php
                                        $consulta = "SELECT id_cliente, CONCAT(nombre_cliente, ' ', apellido_cliente) AS nombre FROM cliente ORDER BY nombre";
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
                                    <input type="date" class="form-control" id="fecha_presup" name="fecha_presup" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Estado</label>
                                    <select class="form-select" id="id_estado_presupuesto" name="id_estado_presupuesto" required>
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
                                    <input type="number" step="0.01" class="form-control" id="precio_total_presup" name="precio_total_presup" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lugar de Entrega</label>
                            <input type="text" class="form-control" id="lugar" name="lugar" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mano de Obra</label>
                            <input type="number" step="0.01" class="form-control" id="mano_obra" name="mano_obra" value="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Productos</label>
                            <div class="border p-2 mb-2" style="max-height: 200px; overflow-y: auto;" id="contenedor-productos">
                                <!-- Productos se agregarán aquí dinámicamente -->
                            </div>
                            <select class="form-select mb-2" id="select-productos">
                                <option value="">Seleccione un producto...</option>
                                <?php
                                $consulta = "SELECT id_producto_term, nomb_producto, precio_venta FROM producto_terminado ORDER BY nomb_producto";
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
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
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

    <!-- INICIO: Modal Seguimiento Mejorado -->
    <div class="modal fade" id="modalSeguimiento" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Seguimiento de Presupuesto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formSeguimiento">
                    <div class="modal-body">
                        <input type="hidden" id="seguimiento_id_presupuesto" name="id_presupuesto">
                        <div class="row">
                            <!-- Columna de Estado -->
                            <div class="col-md-4 border-end">
                                <h5>Estado</h5>
                                <p>Seleccione para cambiar el estado si lo desea:</p>
                                <div id="contenedor-estados-radio">
                                    <!-- Los estados se cargarán aquí desde la BD -->
                                </div>
                                <p class="mt-3">El presupuesto se encuentra en estado: <strong id="estado-actual-texto"></strong></p>
                                <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                            </div>

                            <!-- Columna de Observaciones y Materiales -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Observaciones</h5>
                                        <p>Detalles del trabajo:</p>
                                        <textarea class="form-control" id="seguimiento_observaciones" name="observaciones" rows="5" readonly></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Stock</h5>
                                        <p>Materiales necesarios para el trabajo:</p>
                                        <div class="border p-2" style="height: 200px; overflow-y: auto;">
                                            <ul class="list-group list-group-flush" id="lista-materiales-requeridos">
                                                <!-- Se llenará dinámicamente -->
                                            </ul>
                                        </div>
                                        <button type="button" class="btn btn-success mt-2" id="btn-descontar-stock" disabled>Descontar de Stock</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- FIN: Modal Seguimiento Mejorado -->


    <!-- INICIO: Modal para Cobros -->
    <div class="modal fade" id="modalCobro" tabindex="-1" aria-labelledby="modalCobroLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCobroLabel">Gestión de Cobro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="cobro_id_presupuesto">
                    
                    <h6>Resumen de Saldos</h6>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>Total Presupuesto</strong></td>
                                <td id="cobro-saldo-total" class="text-end">$0.00</td>
                            </tr>
                            <tr>
                                <td>Seña Recibida</td>
                                <td id="cobro-seña" class="text-end text-success">$0.00</td>
                            </tr>
                            <tr>
                                <td>Otros Pagos</td>
                                <td id="cobro-pagos" class="text-end text-success">$0.00</td>
                            </tr>
                            <tr class="table-secondary">
                                <td><strong>Saldo Pendiente</strong></td>
                                <td id="cobro-saldo-pendiente" class="text-end fw-bold">$0.00</td>
                            </tr>
                        </tbody>
                    </table>

                    <hr>

                    <h6>Registrar Pagos</h6>
                    <div class="mb-3">
                        <label for="cobro-input-seña" class="form-label">Registrar Seña</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="cobro-input-seña" placeholder="Monto de la seña">
                            <button class="btn btn-primary" type="button" id="btn-registrar-seña">Registrar</button>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success" type="button" id="btn-registrar-pago">Registrar Pago Final / Parcial</button>
                        <button class="btn btn-info" type="button" id="btn-imprimir-recibo" disabled>Imprimir Recibo de Pago</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- FIN: Modal para Cobros -->


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