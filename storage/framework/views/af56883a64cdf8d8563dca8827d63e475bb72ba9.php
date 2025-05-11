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

    <style>        
        .logo{
            width: 122px;
            height: 62px;
            margin: none;
            float: left;
        }
        .qr {
            width: 150px;
            height: 90px;
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
            color: #297E3B;
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
            border: 1px solid #297E3B;
            width: 100%;
            font-size: 10px;
        }
        #tabla2{
            border: 1px solid #297E3B;
            width: 50%;
            font-size: 10px;
        }
        #cabtab1{
            background-color: #739900;
            font-weight: bold;
            color: #ffffff;
            padding: 2 2 2 2px;
            text-align: center;
        }
        #cabtab2{
            background-color: #739900;
            font-weight: bold;
            color: #ffffff;
            padding: 2 2 2 2px;
            text-align: center;
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
                    Venta al por mayor de frutas, hortalizas (verduras),
                    <br>
                    legumbres y tubérculos
                    <br>
                    11 AV. SUR LOCAL 317 C. URB.
                    <br>
                    San Salvador
                    <br>
                    Tel.: 2345-6780
                    <br>
                    mr.jbinversionessadecv@gmail.com
                    <br>
                    Categoría: Otros
                    <br>
                    Tipo establecimiento: Casa Matriz
                    <br>
                    NIT: 0614-011022-104-3
                    <br>
                    NCR: 320113-6
                </p>
            </div>      
        </div>

        <div id="header">       
            <div class="infoHeader">
                <p style="margin-top: 0px; margin-bottom: 0px; font-size: 14px;">DOCUMENTO TRIBUTARIO ELECTRÓNICO</p>
                <p style="margin-top: 0px; margin-bottom: 0px; text-align: center; font-size: 14px;">FACTURA SUJETO EXCLUIDO</p>              
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
                                    <p class="dte" style="margin-left: 16px;">Versión del JSON: 3</p>
                                </div>
                            </div>
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Tipo de transmisión: Normal</p>            
                                </div>
                                <div class="col-6">
                                    <p class="dte" style="margin-left: 3px;">Fecha de emisión: <?php echo e(\Carbon\Carbon::parse($lims_sale_data->created_at)->format('d/m/Y')); ?></p>
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
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
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
        <br>
        <br>
        <br>
        
        <div id="footer">
            <p class="textFooter">DTE generado por la plataforma SFE de SL SERVICES IT</p>
        </div>
        
        <div class="header">
            <div class="qr" style="margin-top: -120px; padding-left: 200px;">            
                <img src="data:image/svg+xml;base64,<?php echo e(base64_encode($valor)); ?>">
            </div>
        </div>
        
       
        

    <div id="receipt-data">
        <?php $mlibro=0 ?>
        <?php $ylibro=0 ?>
        <?php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
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
                        <td style='width: 70%;'>Tipo de doc. de Identificación: DUI </td>
                        <td>N° de doc. de Identificación: <?php echo e($lims_customer_data->dui); ?></td>                        
                    </tr>
                </table>
            </div>
            
            <!--Formato a  linea 3: (Correo y registro) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td>N° de Telefono: <?php echo e($lims_customer_data->phone); ?></td>
                        <td style='width: 70%;'>Dirección: <?php echo e($lims_customer_data->address); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

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
                    <th style="width:5px; text-align: center;">Unidad</th>
                    <th style="width:80px; text-align: left;">Descripción</th>
                    <th style="width:5px; text-align: left;">Precio Unitario</th>
                    <th style="width:3px;">Descuento por Item</th>
                    <th style="width:3px; text-align: right;">Ventas</th>
                </tr>

                <?php $total_product_tax = 0;?>
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
                        else
                            $product_name = $lims_product_data->name;
                            $description = $product_sale_data->description;
                            $licitacion = $lims_sale_data->licitacion;
                    ?>
                    <tr>
                        <?php
                            $total_product_tax += $product_sale_data->tax;
                        ?>
                        <td style="width:15px; text-align: center;"><?php echo e($i++); ?></td>
                        <td style="width:15px; text-align: right;"><?php echo e(number_format((float) $product_sale_data->qty, 2, '.', '')); ?></td>
                         <?php
                            $lims_product_data = \App\Unit::find($product_sale_data->purchase_unit_id);
                            
                            $product_name = $lims_product_data->unit_name;
                        ?>
                        <td style="width:5px; text-align: center;"><?php echo $product_name ?></td>
                        <td style="width:80px;">
                            <?php
                                if($licitacion !="off"){
                                    echo $description;
                                }else{
                                    echo $product_name;
                                }
                            ?>     
                        </td>
                        <td style="width:5px; text-align: right;">
                            <?php echo e(number_format((float)(($product_sale_data->net_unit_cost) / $product_sale_data->qty), 2, '.', '')); ?>

                        </td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px; text-align: right;">
                            <?php echo e(number_format((float)($product_sale_data->net_unit_cost), 2, '.', '')); ?>

                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </table>          
        </div>
                
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <th style='width: 244%; text-align: left;'></th>
                        <td style='width: 100%; text-align: left; padding-right: 10px;'>Sumatoria de ventas:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->grand_total+$lims_sale_data->total_discount+$total_product_tax, 2, '.', '')); ?> 
                        </td>
                    </tr>
                </table>
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 244%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Sub-Total:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->grand_total+$lims_sale_data->total_discount+$total_product_tax, 2, '.', '')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 244%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Retención Renta:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->total_discount, 2, '.', '')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 244%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Iva Retenido:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$total_product_tax, 2, '.', '')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 244%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Total a Pagar:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            <?php echo e(number_format((float)$lims_sale_data->grand_total, 2, '.', '')); ?>

                        </td>
                    </tr>
                </table>
            </div>

            <br>
        </div>

        <br>

        <div class="container-fluid" id="tabla1">  
            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <th style='width: 225%; text-align: left;'>Valor en letras: <?php echo e($todo); ?></th>
                        <?php if($lims_sale_data->payment_method=="contado"): ?>
                            <td style='width: 100%; text-align: left; padding-right: 40px;'>Condición de la Operación: Contado</td>
                        <?php else: ?>
                            <td style='width: 100%; text-align: left; padding-right: 40px;'>Condición de la Operación: Crédito</td>
                        <?php endif; ?>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Observaciones): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <th style='width: 240%; text-align: left;'>Observaciones: <?php echo e($lims_sale_data->sexcluded_note); ?></th>
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
</body>
</html>
<?php /**PATH /home2/mrjbinve/public_html/factue/resources/views/sexcluded/efact.blade.php ENDPATH**/ ?>