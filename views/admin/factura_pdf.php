<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice INV-<?php echo str_pad($factura['id_factura'], 5, '0', STR_PAD_LEFT); ?></title>
    <style>
        /* Estilos base para pantalla y PDF */
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f3f4f6; color: #000; }
        
        .a4-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }

        /* Cabecera del Documento */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .company-info h1 { margin: 0; color: #166534; font-size: 2.5em; font-style: italic; font-weight: bold; font-family: 'Times New Roman', Times, serif; }
        .company-info h2 { margin: 0; font-size: 1em; letter-spacing: 2px; font-weight: bold; }
        .company-info p { margin: 2px 0; font-size: 0.9em; font-weight: bold; }
        
        .admin-info { text-align: right; }
        .admin-info h2 { margin: 0; font-size: 1.4em; }
        .admin-info p { margin: 2px 0; font-size: 1.1em; font-weight: bold; }
        
        .invoice-title { text-align: center; font-size: 1.8em; font-weight: bold; letter-spacing: 5px; margin: 20px 0; color: #1f2937; text-transform: uppercase; }

        /* Cuadro de Información del Cliente */
        .client-box { border: 1px solid #000; margin-bottom: 20px; }
        .client-row { display: flex; border-bottom: 1px solid #000; }
        .client-row:last-child { border-bottom: none; }
        .client-col { padding: 8px; flex: 1; border-right: 1px solid #000; }
        .client-col:last-child { border-right: none; }
        .client-label { font-size: 0.7em; font-weight: bold; text-transform: uppercase; margin-bottom: 4px; display: block; }
        .client-data { font-size: 1em; }

        /* Tabla de Servicios */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #000; }
        th { border: 1px solid #000; padding: 10px; font-size: 0.9em; text-transform: uppercase; text-align: center; }
        td { border: 1px solid #000; padding: 10px; font-size: 0.95em; }
        .col-desc { width: 50%; }
        .col-qty, .col-price, .col-total { width: 15%; text-align: center; }
        .col-total { font-weight: bold; }
        
        .totals-row td { border: none; padding: 8px 10px; text-align: right; }
        .totals-row .total-label { font-weight: bold; font-size: 1.2em; }
        .totals-row .total-amount { font-weight: bold; font-size: 1.2em; border-top: 2px solid #000; border-bottom: 2px double #000; }

        /* Textos Legales */
        .terms { font-size: 0.75em; line-height: 1.4; margin-top: 30px; }
        .terms h4 { margin: 10px 0 5px 0; text-decoration: underline; font-size: 1.1em; }
        .terms p { margin: 0 0 10px 0; }
        .payment-info { text-align: center; margin-top: 30px; font-weight: bold; font-size: 0.9em; }

        /* Barra de Control (No se imprime) */
        .print-controls { text-align: center; padding: 15px; background: #3b82f6; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .btn-print { background: #1f2937; color: white; padding: 10px 20px; font-size: 1.1em; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; }
        .btn-print:hover { background: #111827; }

        /* Reglas Específicas para Impresión/PDF */
        @media print {
            body { background: white; }
            .a4-container { margin: 0; padding: 10mm; box-shadow: none; border-radius: 0; width: 100%; min-height: auto; }
            .print-controls { display: none; }
        }
    </style>
</head>
<body>

    <div class="print-controls">
        <button class="btn-print" onclick="window.print()">🖨️ Imprimir / Guardar como PDF</button>
    </div>

    <div class="a4-container">
        
        <div class="header">
            <div class="company-info">
                <h1>Green Village</h1>
                <h2>LANDSCAPE LLC</h2>
                <p>www.GreenVillageLandscape.com</p>
            </div>
            <div class="admin-info">
                <h2>Dennis Leon</h2>
                <p>(703) 209-6053</p>
            </div>
        </div>

        <div class="invoice-title">
            INVOICE #INV-<?php echo str_pad($factura['id_factura'], 5, '0', STR_PAD_LEFT); ?>
        </div>

        <div class="client-box">
            <div class="client-row">
                <div class="client-col">
                    <span class="client-label">Client Name</span>
                    <span class="client-data"><?php echo htmlspecialchars($factura['nombre_completo']); ?></span>
                </div>
                <div class="client-col" style="flex: 0.5;">
                    <span class="client-label">Date Issued</span>
                    <span class="client-data"><?php echo date('m/d/Y', strtotime($factura['fecha_emision'])); ?></span>
                </div>
                <div class="client-col" style="flex: 0.5;">
                    <span class="client-label">Due Date</span>
                    <span class="client-data" style="color: #b91c1c; font-weight: bold;"><?php echo date('m/d/Y', strtotime($factura['fecha_vencimiento'])); ?></span>
                </div>
            </div>
            <div class="client-row">
                <div class="client-col">
                    <span class="client-label">Service Address</span>
                    <span class="client-data"><?php echo htmlspecialchars($factura['direccion']); ?></span>
                </div>
                <div class="client-col" style="flex: 0.5;">
                    <span class="client-label">Phone Number</span>
                    <span class="client-data"><?php echo htmlspecialchars($factura['telefono']); ?></span>
                </div>
                <div class="client-col" style="flex: 0.5;">
                    <span class="client-label">Period</span>
                    <span class="client-data"><?php echo htmlspecialchars($factura['periodo'] ?? 'N/A'); ?></span>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-desc">Description of Services</th>
                    <th class="col-qty">Visits / Qty</th>
                    <th class="col-price">Unit Price</th>
                    <th class="col-total">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalles as $item): ?>
                <tr>
                    <td class="col-desc">
                        <b><?php echo htmlspecialchars($item['descripcion_custom']); ?></b>
                    </td>
                    <td class="col-qty"><?php echo number_format($item['cantidad'], 0); ?></td>
                    <td class="col-price">$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                    <td class="col-total">$<?php echo number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                
                <!-- Filas de relleno para diseño -->
                <?php for($i = count($detalles); $i < 5; $i++): ?>
                <tr><td class="col-desc"><br></td><td class="col-qty"></td><td class="col-price"></td><td class="col-total"></td></tr>
                <?php endfor; ?>
                
                <tr class="totals-row">
                    <td colspan="2"></td>
                    <td class="total-label">BALANCE DUE:</td>
                    <td class="total-amount">$<?php echo number_format($factura['total'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="terms">
            <h4>Billing/Payment:</h4>
            <p>Invoices may be paid by cash, check, money order, PayPal, Venmo and Zelle. Invoices will be delivered by mail or email on the 1st of each month with balance due on the 15th of each month. A $30.00 LATE FEE will be charged for any invoices not paid by due date.</p>
            
            <h4>Terms & Conditions:</h4>
            <p>Either party may terminate this contract at any time for any reason by supplying a written 30-day notice. If the contract is terminated early by the Client, a cancellation fee applies which will be the sum of 1 month worth of cuts or monthly package payments as set above, minus the monthly dues that the Client has already paid. Client signature approves agreement and acknowledges to have read the above terms by the Client and Green Village Landscape.</p>
            <p><i>Applicable Law: This contract shall be governed by the laws of the State of Virginia in the County of Loudoun and any applicable Federal Law.</i></p>
        </div>

        <div class="payment-info">
            PLEASE make a CHECK payable to Dennis Leon or Green Village Landscape PO BOX 154 Sterling VA 20167.<br>
            We accept Payments by ZELLE, PAYPAL, VENMO, CASHAPP
        </div>

    </div>
</body>
</html>