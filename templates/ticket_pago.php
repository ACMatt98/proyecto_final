<?php
// Asegurarnos de que las variables existan para evitar errores si se abre directo
$n_comprobante = isset($n_comprobante) ? $n_comprobante : '---';
$fecha_emision = isset($fecha_emision) ? $fecha_emision : date('d/m/Y');
$nombre_cliente = isset($nombre_cliente) ? $nombre_cliente : 'Consumidor Final';
$direccion = isset($direccion) ? $direccion : '';
$concepto = isset($concepto) ? $concepto : 'Varios';
$monto = isset($monto) ? $monto : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Pago</title>
    <style>
        /* Estilos base para asegurar que se vea bien en el modal */
        #ticket-container {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }
        .ticket-box {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 25px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .header-table, .details-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .divider { border-bottom: 1px solid #eee; }
        .total-row { border-top: 2px solid #333; font-size: 1.2em; }
        .logo-text { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .sub-text { font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div id="ticket-container">
        <div class="ticket-box">
            <table class="header-table">
                <tr>
                    <td class="text-center">
                        <div class="logo-text">REPOSTERÍA</div>
                        <div class="sub-text">Comprobante de Pago Electrónico</div>
                        <div style="margin-top:10px; font-size: 14px;">
                            <strong>N°: <?php echo str_pad($n_comprobante, 8, '0', STR_PAD_LEFT); ?></strong>
                        </div>
                        <div style="font-size: 12px; color: #555;">
                            Fecha: <?php echo $fecha_emision; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <hr style="border: 0; border-top: 1px dashed #ccc; margin: 15px 0;">

            <table class="details-table">
                <tr>
                    <td style="padding-bottom: 5px;"><strong>Cliente:</strong></td>
                    <td class="text-right" style="padding-bottom: 5px;"><?php echo $nombre_cliente; ?></td>
                </tr>
                <?php if(!empty($direccion)): ?>
                <tr>
                    <td style="padding-bottom: 5px;"><strong>Dirección:</strong></td>
                    <td class="text-right" style="padding-bottom: 5px; font-size: 12px;"><?php echo $direccion; ?></td>
                </tr>
                <?php endif; ?>
            </table>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">

            <p style="font-size: 12px; color: #777; margin-bottom: 10px;">DETALLE DE LA OPERACIÓN</p>
            
            <table class="items-table">
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="text-align: left; padding: 8px;">Concepto</th>
                        <th class="text-right" style="padding: 8px;">Importe</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 10px 5px; border-bottom: 1px solid #eee;">
                            <?php echo $concepto; ?>
                        </td>
                        <td class="text-right" style="padding: 10px 5px; border-bottom: 1px solid #eee;">
                            $ <?php echo number_format($monto, 2, ',', '.'); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br>

            <table class="totals-table">
                <tr>
                    <td class="text-right" style="padding: 5px;">Subtotal:</td>
                    <td class="text-right" style="width: 30%; padding: 5px;">$ <?php echo number_format($monto, 2, ',', '.'); ?></td>
                </tr>
                <tr class="total-row">
                    <td class="text-right" style="padding: 10px;"><strong>TOTAL:</strong></td>
                    <td class="text-right" style="padding: 10px; color: #d63384;">
                        <strong>$ <?php echo number_format($monto, 2, ',', '.'); ?></strong>
                    </td>
                </tr>
            </table>

            <div style="margin-top: 30px; text-align: center; font-size: 11px; color: #aaa;">
                <p>¡Gracias por elegirnos!</p>
                <p>Este documento es un comprobante de pago no fiscal válido como recibo.</p>
            </div>
        </div>
    </div>
</body>
</html>