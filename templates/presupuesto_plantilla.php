<?php
// Valores por defecto para evitar errores
$n_presupuesto = $n_presupuesto ?? '---';
$fecha_emision = $fecha_emision ?? date('d/m/Y');
$validez = $validez ?? '15 días'; // Puedes ajustar esto
$cliente = $cliente ?? 'Consumidor Final';
$direccion = $direccion ?? '';
$telefono = $telefono ?? '';
$items = $items ?? [];
$total = $total ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto #<?php echo $n_presupuesto; ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #555; max-width: 800px; margin: auto; padding: 20px; }
        .invoice-box { padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 16px; line-height: 24px; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .top-title { font-size: 45px; line-height: 45px; color: #333; }
        .information-table td { padding-bottom: 40px; }
        .heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .item td { border-bottom: 1px solid #eee; }
        .item.last td { border-bottom: none; }
        .total td { border-top: 2px solid #333; font-weight: bold; font-size: 1.2em; padding-top: 10px;}
        .text-right { text-align: right; }
        
        /* Estilos solo para Impresión */
        @media print {
            body { margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; border: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <h1 style="margin:0; color:#d4a373;">LO DE MIRTA REPOSTERÍA</h1>
                            </td>
                            <td>
                                <strong>Presupuesto #: <?php echo str_pad($n_presupuesto, 8, '0', STR_PAD_LEFT); ?></strong><br>
                                Fecha: <?php echo $fecha_emision; ?><br>
                                Validez: <?php echo $validez; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>CLIENTE:</strong><br>
                                <?php echo $cliente; ?><br>
                                <?php echo $direccion ? $direccion . '<br>' : ''; ?>
                                <?php echo $telefono; ?>
                            </td>
                            <td>
                                <strong>EMISOR:</strong><br>
                                Lo de Mirta Reposteria<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table style="margin-top: 20px;">
            <tr class="heading">
                <td>Producto / Servicio</td>
                <td style="text-align: center;">Cant.</td>
                <td style="text-align: right;">Precio Unit.</td>
                <td style="text-align: right;">Subtotal</td>
            </tr>

            <?php foreach ($items as $item): ?>
            <tr class="item">
                <td><?php echo $item['nombre']; ?></td>
                <td style="text-align: center;"><?php echo $item['cantidad']; ?></td>
                <td style="text-align: right;">$ <?php echo number_format($item['precio'], 2, ',', '.'); ?></td>
                <td style="text-align: right;">$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>

            <tr class="total">
                <td colspan="3" class="text-right">TOTAL:</td>
                <td class="text-right">$ <?php echo number_format($total, 2, ',', '.'); ?></td>
            </tr>
        </table>
        
        <div style="margin-top: 40px; font-size: 12px; color: #777; text-align: center;">
            <p>Este documento es un presupuesto preliminar y no representa una factura fiscal.</p>
        </div>
    </div>
</body>
</html>