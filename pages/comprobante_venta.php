<?php
include_once 'bd/conexion.php';
include_once 'bd/funciones.php';

$objeto = new Conexion();
$conexion = $objeto->Conectar();

$consulta = "SELECT cv.id_comprob_vta, cv.n_comprob_vta, cv.tipo_factura_vta, 
            CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) as cliente,
            co.fecha_cobro as fecha, co.monto as monto
            FROM comprobantevta cv
            LEFT JOIN cliente c ON cv.id_cliente = c.id_cliente
            LEFT JOIN cobro co ON cv.id_comprob_vta = co.id_comprob_vta
            ORDER BY cv.n_comprob_vta DESC";
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
        <h1 class="h3 mb-2 text-gray-800">Comprobantes de Venta</h1>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <button id="btnNuevo" class="btn btn-success">+ Agregar</button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaComprobantes" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>N° Comprobante</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Factura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $dat) { ?>
                            <tr data-id="<?php echo $dat['id_comprob_vta'] ?>">
                                <td><?php echo $dat['n_comprob_vta'] ?></td>
                                <td><?php echo $dat['cliente'] ?></td>
                                <td><?php echo FormatoFechas::cambiaFormatoFecha($dat['fecha']) ?></td>
                                <td>$<?php echo number_format($dat['monto'], 2) ?></td>
                                <td><?php echo $dat['tipo_factura_vta'] ?></td>
                                <td></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal para nuevo comprobante -->
        <div class="modal fade" id="modalCRUD" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo Comprobante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formComprobantes" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="n_comprob_vta">Número de Comprobante:</label>
                                <input type="text" class="form-control" id="n_comprob_vta" required>
                            </div>
                            <div class="form-group">
                                <label for="cliente">Cliente:</label>
                                <select class="form-control" id="cliente" required>
                                    <option value="">Seleccione un cliente</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="fecha">Fecha:</label>
                                <input type="date" class="form-control" id="fecha" required>
                            </div>
                            <div class="form-group">
                                <label for="monto">Monto:</label>
                                <input type="number" step="0.01" class="form-control" id="monto" required>
                            </div>
                            <div class="form-group">
                                <label for="tipo_factura">Tipo Factura:</label>
                                <select class="form-control" id="tipo_factura" required>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="archivo">Comprobante escaneado:</label>
                                <input type="file" class="form-control" id="archivo" accept="image/*,.pdf">
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

        <!-- Modal para visualizar comprobante -->
        <div class="modal fade" id="modalVisualizador" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Visualizar Comprobante</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="visorArchivo"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/comprobantesVentaScript.js"></script>
</body>
</html>