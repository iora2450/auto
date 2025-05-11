<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="<?php echo e(url('public/logo', $general_setting->site_logo)); ?>" />
    <title><?php echo e($general_setting->site_title); ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
   <script>


    </script>
    <style>        
        .logo{
            width: 122px;
            height: 80px;
            margin: none;
            float: left;
        }
        #header {
            position: fixed;
            top: 0cm;
            left: 0cm;
        }
        #footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            width: 100%;
        }
        .textFooter {
            text-align: center;
            width: 100%;
            font-size: 9px;
        }
        .infoHeader {
            float: right;
            margin-left: 10cm;
            color: #40c1ac;
            font-weight: bold;
            font-size: 10px;
        }
        .textContri {
            float: left;
            margin: none;
        }
        .dteIzq {
            font-size: 10px;
            margin-left: -60px;
        }
        .dte {
            font-size: 10px;
            margin-right: 50px;
            margin-top:0;
            margin-bottom:0;
            float: left;
        }
        #tabla1{
            border: 1px solid #40c1ac;
            width: 100%;
            font-size: 10px;
        }
        #tabla2{
            border: 1px solid #40c1ac;
            width: 50%;
            font-size: 10px;
        }
        #cabtab1{
            background-color: #00A3AD;
            font-weight: bold;
            color: #ffffff;
            padding: 2 2 2 2px;
            text-align: center;
        }        
        #cabtab2{
            background-color: #00A3AD;
            font-weight: bold;
            color: #ffffff;
            padding: 2 2 2 2px;
            text-align: center;
            width: 60%;
        }
        #cabtab3{
            font-weight: bold;
            color: #ffffff;
            padding: 2 2 2 2px;
            text-align: left;
            width: 60%;
        }
        #cuerpotab1{
            padding: 4 4 4 4px;
            background-color: #ffffcc;
        }
        #colDere {
            column-gap: 3em;
            columns: 3;
            text-align: justify;
        }        
        td,
        th,
        tr,
        table {
            border-collapse: collapse;
        }
        table {
            width: 100%;
            font-size: 9px;
        }
        tfoot tr th:first-child {
            text-align: left;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body> 
    <div class="container-fluid">      
        <div class="centered">
            <?php if($general_setting->site_logo): ?>
                <img src="<?php echo e(url('public/logo', $general_setting->site_logo)); ?>" class="logo">
            <?php endif; ?>
        </div> 

        <div id="header">
            <div class="textContri" style="margin-top: 55px; margin-left: -45;">
                <p class="dteIzq" style="margin-top: 0px; margin-bottom: 0px;">
                   <br>
                   <br>
                   TRAYNE S.A DE C.V
                   <br>
                    Agecia de tramitaciones aduanales,
                    <br>
                    Servicios para el transporte NCP
                    <br>
                   RESID, MIRALVALLE II, · SAN SALVADOR
                    <br>
                    Tel.: 22844959             
                    <br>
                    Tipo establecimiento: Casa Matriz
                    <br>
                    NIT: 0614-150404-104-7
                    <br>
                    NCR: 156023-0
                    <br>      
                </p>
            </div>      
        </div>


        <div id="header">       
            <div class="infoHeader">
                <p style="margin-top: 0px; margin-bottom: 0px; font-size: 14px;">DOCUMENTO TRIBUTARIO ELECTRÓNICO</p>
                <p style="margin-top: 0px; margin-bottom: 0px; text-align: center; font-size: 14px;">COMPROBANTE DE CREDITO FISCAL</p>              
            </div>
        
            <div class="container">
                <div class="row">             
                    <div class="col-4">
                        <div class="textContri" style="margin-top: 40px; padding-left: 150px;">
                            <p class="dte">Código generación: <?php echo e($lims_sale_data->codgeneracion); ?></p>
                            <br>
                            <p class="dte">Número de control: <?php echo e($lims_sale_data->numerocontrol); ?></p>
                            <br>
                            <p class="dte">Sello de recepción: <?php echo e($lims_sale_data->sello); ?></p>                        
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Modelo facturación: Previo</p>            
                                </div>
                                <div class="col-6">
                                    <p class="dte" style="margin-left: 10px;">Versión del JSON: 3</p>
                                </div>
                            </div>
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Tipo de transmisión: Normal</p>            
                                </div>
                                <div class="col-6">
                                    <p class="dte" style="margin-left: 3px;">Fecha de emisión: <?php echo e(\Carbon\Carbon::parse($lims_sale_data->created_at)->format('dmY')); ?></p>
                                </div>                        
                            </div>
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Hora de emisión: <?php echo e($lims_sale_data->created_at->toTimeString()); ?></p>
                                </div>                        
                            </div>
                        </div>
                    <div class="col-4"></div>
                </div>
            </div>
        </div>
        <br>
        <br>
        <br>        
    </div>

    <div id="receipt-data">
        <?php $mlibro=0 ?>
        <?php $ylibro=0 ?>
        <?php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = \Carbon\Carbon::parse($lims_sale_data->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
        ?>
        <?php $ylibro = \Carbon\Carbon::parse($lims_sale_data->created_at)->format('Y')  ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        
        <div id="footer">
            <p class="textFooter"></p>
        </div>
        
        <div class="header">
            <div class="qr" style="margin-top: -130px; padding-left: 240px;">            
                <img src="data:image/svg+xml;base64,<?php echo e(base64_encode($valor)); ?>">
            </div>
        </div>
  

    <div id="receipt-data">
        <?php $mlibro=0 ?>
        <?php $ylibro=0 ?>
        <?php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = \Carbon\Carbon::parse($lims_sale_data->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
        ?>
        <?php $ylibro = \Carbon\Carbon::parse($lims_sale_data->created_at)->format('Y')  ?>
        <!-- <div style="max-width:600px;margin:0 auto"> -->
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea 1 (Nombre): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 77%;'>Razón Social: <?php echo e($lims_customer_data->name); ?></td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 2 (Actividad y Nit): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Act. Económica: <?php echo e($lims_customer_data->gire->name); ?></td>
                        <td>NIT: <?php echo e($lims_customer_data->nit); ?></td>                        
                    </tr>
                </table>
            </div>
            
            <!--Formato a  linea 3: (Correo y registro) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Correo: <?php echo e($lims_customer_data->email); ?></td>
                        <td>NRC: <?php echo e($lims_customer_data->tax_no); ?></td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 4: (Dirección y telefono) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Dirección: <?php echo e($lims_customer_data->address); ?></td>
                        <td>Telefono: <?php echo e($lims_customer_data->phone_number); ?></td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 5: (Municipio y forma pago) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Municipio: <?php echo e($lims_customer_data->municipio->name); ?></td>
                        <?php if($lims_sale_data->payment_method=="contado"): ?>
                            <td>Forma pago: Contado</td>
                        <?php else: ?>
                            <td>Forma pago: Crédito</td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 6: (Departamento y Moneda) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%'>Departamento: <?php echo e($lims_customer_data->estado->name); ?></td>
                        <td>Moneda: USD</td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

        <?php $i = 1 ?>
        <?php if($lims_sale_data->tercero=="on"): ?>
        <div id=tabla1>
            <div id=cabtab1>
                VENTA A CUENTA DE TERCEROS
            </div>
            <table  style='min-height:10px; max-height:10px;'>
                <tr>
                    <td>NIT:</td>                        
                    <td style='width: 70%;'>Nombre, denominación o razón social:</td>
                </tr>    
            </table>  
        </div>
        <?php endif; ?>

<!-- Nueva sección: Casa Exportador, Factura, DUCA, Aduana -->
<div class="container-fluid" id="tabla1">
    <div id="cabtab1">
        NUEVA SECCIÓN
    </div>
    <table style='width: 100%; border: 1px solid black; border-collapse: collapse;'>
        <tr>
            <td style='width: 100%; border: 1px solid black;'><strong>CASA EXPORTADOR:</strong> <?php echo e($lims_sale_data->casa_exportador); ?></td>
        </tr>
        <tr>
            <td style='width: 100%; border: 1px solid black;'><strong>FACTURAS:</strong> <?php echo e($lims_sale_data->facturas); ?></td>
        </tr>
        <tr>
            <td style='width: 50%; border: 1px solid black;'><strong>DUCA:</strong> <?php echo e($lims_sale_data->duca); ?></td>

        </tr>
        <tr>
            <td style='width: 50%; border: 1px solid black;'><strong>ADUANA:</strong> <?php echo e($lims_sale_data->aduana); ?></td>
        </tr>
    </table>
</div>






        <!-- Linea 7: Lineas  de detalle -->
        <div class="container-fluid" id="tabla1">
            <?php $total_product_tax=0;
                $total_total=0;
            ?>
            <div id=cabtab1>
                CUERPO DEL DOCUMENTO
            </div>
        
           <table class="default">
    <tr>
        <th style="width:15px;">No. Item</th>
        <th style="width:15px;">Cantidad</th>
        <th style="width:5px; text-align: center;">Unidad Medida</th>
        <th style="width:80px; text-align: left;">Descripción</th>
        <th style="width:5px; text-align: left;">Precio Unitario</th>
        <th style="width:3px;">Descuento por Item</th>
        <th style="width:3px; text-align: left;">Otros montos no Sujetos</th>
        <th style="width:3px;">Ventas no Sujetas</th>
        <th style="width:3px;">Ventas Exentas</th>
        <th style="width:3px;">Ventas Gravadas</th>
    </tr>

    <?php $total_product_tax = 0; ?>
    <?php $i = 1 ?>
    <?php $__currentLoopData = $lims_product_sale_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_sale_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $lims_product_data = \App\Product::find($product_sale_data->product_id);
            if($product_sale_data->variant_id) {
                $variant_data = \App\Variant::find($product_sale_data->variant_id);
                $product_name = $lims_product_data->name.' ['.$variant_data->name.']';
                $description = $product_sale_data->description;
                $licitacion = $lims_sale_data->licitacion;
            }
            else {
                $product_name = $lims_product_data->name;
                $description = $product_sale_data->description;
                $licitacion = $lims_sale_data->licitacion;
            }

            // Verificar si el producto es exento de IVA
            $is_exempt = $product_sale_data->tax == 0 ? $product_sale_data->total : 0;
            $gravado = $product_sale_data->tax > 0 ? $product_sale_data->total / 1.13 : 0;

            $total_product_tax += $product_sale_data->tax;
        ?>
        <tr>
            <td style="width:15px; text-align: center;"><?php echo e($i++); ?></td>
            <td style="width:15px; text-align: right;"><?php echo e(number_format((float) $product_sale_data->qty, 4, '.', '')); ?></td>
            <td style="width:5px; text-align: center;">Unidad</td>
            <td style="width:80px;">
                <?php
                    if($licitacion != "off") {
                        echo $description;
                    } else {
                        echo $product_name;
                    }
                ?>     
            </td>
         <td style="width:5px; text-align: right;">
    <?php if($product_sale_data->tax > 0): ?>
        <?php echo e(number_format((float)(($product_sale_data->total / 1.13) / $product_sale_data->qty), 2, '.', '')); ?>

    <?php else: ?>
        <?php echo e(number_format((float)($product_sale_data->total / $product_sale_data->qty), 2, '.', '')); ?>

    <?php endif; ?>
</td>

            <td style="width:3px;"></td>
            <td style="width:3px;"></td>
            <td style="width:3px;text-align: right;"><?php echo e(number_format((float)$is_exempt, 2, '.', '')); ?></td>
            <td style="width:3px; "></td>
            <td style="width:3px; text-align: right;">
                <?php echo e(number_format((float)$gravado, 2, '.', '')); ?>

            </td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
    
        </div>
                <?php
    $ventas_exentas = 0;
    $ventas_gravadas = 0;
?>

<?php $__currentLoopData = $lims_product_sale_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product_sale_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if($product_sale_data->tax == 0): ?>
        <?php $ventas_exentas += $product_sale_data->total; ?>
    <?php else: ?>
        <?php $ventas_gravadas += $product_sale_data->total / 1.13; ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                      <tr>
            <td id=cabtab3 style='width: 67.8%;'></td>
            <td style='text-align: left;'>Gastos por cuenta del cliente:</td>
            <td style='text-align: right;'>
            <?php echo e(number_format((float)$ventas_exentas, 2, '.', ',')); ?>

            </td>
            <td style='text-align: right;'>
           0.00
            </td>
            <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                <?php echo e(number_format((float)$ventas_gravadas, 2, '.', ',')); ?>

                <!--<?php echo e(number_format((float)($ventas_exentas + $ventas_gravadas), 2, '.', ',')); ?> -->
            </td>
        </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                     <tr>
            <td id=cabtab3 style='width: 67.8%;'></td>
            <td style='text-align: left;'>Suma Total de Operaciones:</td>
            <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                <?php echo e(number_format((float)($ventas_exentas + $ventas_gravadas), 2, '.', ',')); ?>

            </td>
        </tr>
                </table>
            </div>

            <!--Formato a  linea (Total descuentos): -->
            <div>
                <div class="">
                    <table style='min-height:10px; max-height:10px;'>
                        <tr>
                        <td  style='width: 67.8%;'>
                   
                    <br>
                    OBSERVACIONES:
                    <?php echo e($lims_sale_data->sale_note ?? 'Sin observaciones'); ?>

                    <br>
                    Valor en letras: <?php echo e($todo); ?>

                </td>
                            <td>
                                Total descuentos: 
                            </td>
                            <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                                <?php echo e(number_format((float)$lims_sale_data->discount / 1.13, 2, '.', ',')); ?> 
                            </td>
                        </tr>
                    </table>
                </div>    
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab3 style='width: 67.8%;'></td>
                        <td style='text-align: left;'>Impuesto al Valor Agregado 13%:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$total_product_tax, 2, '.', ',')); ?>

                        </td>
                    </tr>
                </table>
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab3 style='width: 67.8%;'></td>
                        <td style='text-align: left;'>Sub-total:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                              <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                <?php echo e(number_format((float)($ventas_exentas + $ventas_gravadas), 2, '.', ',')); ?>

            </td>
                        </td>
                    </tr>
                </table>
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab3 style='width: 67.8%;'></td>
                        <?php if($lims_sale_data->discount > 0): ?>
                            <td style='text-align: left;'>IVA Percibido:</td>
                        <?php else: ?>
                            <td style='text-align: left;'>IVA Percibido:</td>
                        <?php endif; ?>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->discount / 1.13, 2, '.', '')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab2 style='width: 67.8%;'>EXTENSIÓN</td>
                        <td>
                            IVA Retenido:
                        </td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->discount / 1.13, 2, '.', '')); ?> 
                        </td>
                    </tr>
                </table>
            </div>    

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 110%; font-size: 9px;'>Nombre entrega: <?php echo e($userName); ?></td>
                        <td style='width: 80%; text-align: left; padding-left: 2px; padding-right: 40px;'>No. Documento: <?php echo e($userNit); ?></td>
                        <?php if($lims_sale_data->discount > 0): ?>
                            <td style='width: 120%; text-align: left; padding-left: 40px; padding-right: 117px;'>Retención Renta:</td>
                        <?php else: ?>
                            <td style='width: 120%; text-align: left; padding-left: 60px; padding-right: 100px;'>Retención Renta:</td>
                        <?php endif; ?>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->discount / 1.13, 2, '.', ',')); ?>

                        </td>
                    </tr>
                </table>
            </div>


            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 110%; font-size: 9px;'>Nombre recibe:</td>
                        <td style='width: 80%; text-align: left; padding-left: 2px; padding-right: 40px;'>No. Documento:</td>
                        <td style='width: 120%; text-align: left; padding-left: 60px; padding-right: 60px;'>Monto Total de la Operación:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->grand_total, 2, '.', ',')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total otros montos no afectos): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab3 style='width: 67.8%;'></td>
                        <?php if($lims_sale_data->discount > 0): ?>
                            <td style='text-align: left;'>Total otros montos no Afectos:</td>
                        <?php else: ?>
                            <td style='text-align: left;'>Total otros montos no Afectos:</td>
                        <?php endif; ?>                        
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->discount / 1.13, 2, '.', ',')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total a pagar): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td id=cabtab3 style='width: 67.8%;'></td>
                        <td style='text-align: left;'>TOTAL A PAGAR:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                        <?php echo e(number_format((float)$lims_sale_data->grand_total , 2, '.', ',')); ?>

                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    
    

    <script type="text/javascript">
        localStorage.clear();
        function auto_print() {
            window.print()
        }
        setTimeout(auto_print, 1000);
    </script>
    
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("helvetica", "bold");
            $pdf->page_text(270, 780, "Pag {PAGE_NUM} / {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script>

    <script type="text/javascript">
        total= document.getElementById("total_gral").value
        strvalor= NumeroALetras(total)

        document.getElementById("letras").innerHTML=strvalor
        document.getElementById("letras2").innerHTML=strvalor

        function Unidades(num){
            switch(num)
            {
                case 1: return "UN";
                case 2: return "DOS";
                case 3: return "TRES";
                case 4: return "CUATRO";
                case 5: return "CINCO";
                case 6: return "SEIS";
                case 7: return "SIETE";
                case 8: return "OCHO";
                case 9: return "NUEVE";
            }

            return "";
        }

        function Decenas(num){

            decena = Math.floor(num/10);
            unidad = num - (decena * 10);

            switch(decena)
            {
                case 1:
                    switch(unidad)
                    {
                        case 0: return "DIEZ";
                        case 1: return "ONCE";
                        case 2: return "DOCE";
                        case 3: return "TRECE";
                        case 4: return "CATORCE";
                        case 5: return "QUINCE";
                        default: return "DIECI" + Unidades(unidad);
                    }
                case 2:
                    switch(unidad)
                    {
                        case 0: return "VEINTE";
                        default: return "VEINTI" + Unidades(unidad);
                    }
                case 3: return DecenasY("TREINTA", unidad);
                case 4: return DecenasY("CUARENTA", unidad);
                case 5: return DecenasY("CINCUENTA", unidad);
                case 6: return DecenasY("SESENTA", unidad);
                case 7: return DecenasY("SETENTA", unidad);
                case 8: return DecenasY("OCHENTA", unidad);
                case 9: return DecenasY("NOVENTA", unidad);
                case 0: return Unidades(unidad);
            }
        }//Unidades()

        function DecenasY(strSin, numUnidades){
            if (numUnidades > 0)
                return strSin + " Y " + Unidades(numUnidades)

            return strSin;
        }//DecenasY()

        function Centenas(num){

            centenas = Math.floor(num / 100);
            decenas = num - (centenas * 100);

            switch(centenas)
            {
                case 1:
                    if (decenas > 0)
                        return "CIENTO " + Decenas(decenas);
                        return "CIEN";
                case 2: return "DOSCIENTOS " + Decenas(decenas);
                case 3: return "TRESCIENTOS " + Decenas(decenas);
                case 4: return "CUATROCIENTOS " + Decenas(decenas);
                case 5: return "QUINIENTOS " + Decenas(decenas);
                case 6: return "SEISCIENTOS " + Decenas(decenas);
                case 7: return "SETECIENTOS " + Decenas(decenas);
                case 8: return "OCHOCIENTOS " + Decenas(decenas);
                case 9: return "NOVECIENTOS " + Decenas(decenas);
            }

            return Decenas(decenas);
        }//Centenas()

        function Seccion(num, divisor, strSingular, strPlural){
            cientos = Math.floor(num / divisor)
            resto = num - (cientos * divisor)

            letras = "";

            if (cientos > 0)
                if (cientos > 1)
                    letras = Centenas(cientos) + " " + strPlural;
                else
                    letras = strSingular;

                if (resto > 0)
                    letras += "";

            return letras;
        }//Seccion()

        function Miles(num){
            divisor = 1000;
            cientos = Math.floor(num / divisor)
            resto = num - (cientos * divisor)

            strMiles = Seccion(num, divisor, "MIL", "MIL");
            strCentenas = Centenas(resto);

            if(strMiles == "")
                return strCentenas;

                return strMiles + " " + strCentenas;

        }//Miles()

        function Millones(num){
            divisor = 1000000;
            cientos = Math.floor(num / divisor)
            resto = num - (cientos * divisor)

            strMillones = Seccion(num, divisor, "UN MILLON", "MILLONES");
            strMiles = Miles(resto);

            if(strMillones == "")
                return strMiles;

                return strMillones + " " + strMiles;

        }//Millones()

        function NumeroALetras(num,centavos){
            var data = {
                numero: num,
                enteros: Math.floor(num),
                centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
                letrasCentavos: "",
            };
            if(centavos == undefined || centavos==false) {
                data.letrasMonedaPlural="";
                data.letrasMonedaSingular="";
            }else{
                data.letrasMonedaPlural="CENTAVOS";
                data.letrasMonedaSingular="CENTAVO";
            }

            if (data.centavos > 0)
                data.letrasCentavos = "" + data.centavos + "/100 USD";
                //data.letrasCentavos = "CON " + NumeroALetras(data.centavos,true);
            else
                return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos + "00/100 USD";

                if(data.enteros == 0)
                    return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
                    if (data.enteros == 1)
                        return Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
                else
                    return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
        }//NumeroALetras()

        var content = $("#myTextarea").val();
        content = content.replace(/\n|\r\n/g,"<br>");
    </script>
</body>
</html>
<?php /**PATH /home/tramites/public_html/auto/resources/views/sale/efact.blade.php ENDPATH**/ ?>