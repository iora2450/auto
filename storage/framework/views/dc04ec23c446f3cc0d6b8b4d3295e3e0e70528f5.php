<!DOCTYPE>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Reporte de venta</title>
        <link href="<?php echo e(asset('css/font-awesome.min.css')); ?>" rel="stylesheet">

        <style type="text/css">
            * {
                font-size: 14px;
                font-family: 'Ubuntu', sans-serif;
                text-transform: capitalize;
            }
            body:after {
                content: "COPIA";
                font-size: 15em;
                color: rgba(22, 160, 133, 0.4);
                z-index: 9999;

                display: flex;
                align-items: center;
                justify-content: center;
                position: fixed;
                top: 600;
                right: 0;
                bottom: 0;
                left: 0;
            }
            .btn {
                padding: 7px 10px;
                text-decoration: none;
                border: none;
                display: block;
                text-align: center;
                margin: 7px;
                cursor:pointer;
            }
            .contenedor{
                height: 49%;
                margin: 1% 1%;
            }            
            .btn-info {
                background-color: #999;
                color: #FFF;
            }

            .btn-primary {
                background-color: #6449e7;
                color: #FFF;
                width: 100%;
            }
            td,
            th,
            tr,
            table {
                border-collapse: collapse;
            }

            /*td,th {padding: 7px 0;width: 50%;}*/

            table {width: 100%;}
            tfoot tr th:first-child {text-align: left;}

            .centered {
                text-align: center;
                align-content: center;
                padding-top: 0;
            }
            .logo{
                width: 62px;
                height: 62px;
                margin: none;
                float: left;
            }
            .right {
                width: 62px;
                height: 62px;
                margin: none;
                float: right;
            }

            small{font-size:11px;}

            div.e1 {
                margin-left: 60px;
                max-width:650px;
            }

            div.e2 {
                margin-left: -40px;
                max-width:650px;
            }

            .setingTotales{
                width:8%;
                padding-top:4px;
            }

            @media  print {
                * {
                    font-size:12px;
                /* line-height:16px;*/
                }
                /*  td,th {padding: 5px 0;}*/
                .hidden-print {
                    display: none !important;
                }
                @page  { margin: 0; } body { margin: 0.5cm; margin-bottom:1.6cm; }
            }
            #totales{
               text-align: right;
               width:15px;
            }
        </style>
    </head>
    <body>        
        <div style="max-width:560px; margin:0 auto; height: 0.00px;">
            <?php if(preg_match('~[0-9]~', url()->previous())): ?>
                <?php $url = '../sales'; ?>
            <?php else: ?>
                <?php $url = url()->previous(); ?>
            <?php endif; ?>
            <div class="hidden-print">
                <table>
                    <tr>
                        <td>
                            <button onclick="window.close();" class="btn btn-info" style="width: 150px;">
                                <i class="fa fa-arrow-left"></i>
                                <?php echo e(trans('Return')); ?>

                            </button>
                        </td>
                        <td>
                            <button onclick="window.print();" class="btn btn-primary" style="width: 150px;">
                                <i class="fa fa-print"></i>
                                <?php echo e(trans('Print')); ?>

                            </button>
                        </td>
                    </tr>
                </table>
                <br>
            </div>
        </div>

        <div id="receipt-data" class="contenedor">
            <?php $mlibro=0 ?>
            <?php $ylibro=0 ?>
                                
            <div class="centered">
                <?php if($general_setting->site_logo): ?>
                    <img src="<?php echo e(url('public/logo', $general_setting->site_logo)); ?>" class="logo">
                <?php endif; ?>
            </div>

            <div class="centered">
                <?php $__currentLoopData = $lims_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="right">
                        <?php echo QrCode::size(60)->generate($qr->reference_no); ?>

                    </div> 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
            </div>        

            <div class="centered">
                <p><strong>MR. J&B INVERSIONES DEL CIELO, S.A. DE C.V.</strong></p>

                <p>
                    Sucursal que Emite el Quedan: <?php echo e($lims_warehouse_data->name); ?>

                    <br>
                    Dirección: <?php echo e($lims_warehouse_data->address); ?>

                    <br>
                    Telefono: <?php echo e($lims_warehouse_data->phone); ?>

                </p>
            </div>  

              
            
            <?php $__currentLoopData = $lims_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
                    $fecha = \Carbon\Carbon::parse($v->created_at);
                    $mlibro = $meses[($fecha->format('n')) - 1];
                ?>
                <?php $ylibro = \Carbon\Carbon::parse($v->created_at)->format('Y')  ?>
                
                <div style="max-width:560px;margin:0 auto">
                    <!--Formato a  linea 1 (Numero y fecha): -->
                    <div class="">
                        <table  style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 60%'>Número de Quedan: <font color="#FF0000"> <?php echo e($v->id); ?> </font></td>
                                <td style='width: 87%'>Fecha de Emisión: <?php echo e(\Carbon\Carbon::parse($v->created_at)->format('d/m/Y')); ?> </td>

                            </tr>
                        </table>
                    </div>
                
                    <!--Formato a  linea 2: (Nombre) -->
                    <div class="">
                        <table   style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 60%'>a Favor de: <?php echo e($v->name); ?></td>
                                <td style='width: 87%'>Fecha estimada de Pago: <?php echo e(\Carbon\Carbon::parse($v->due_date)->format('d/m/Y')); ?> </td>
                            </tr>
                        </table>
                    </div>
                    
                    <br>
                    
                    <!--Formato a  linea 3: (municipio, venta a cuenta de) -->
                    <div class="">
                        <table  style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 34%; height: 15px;'>por un Valor de: $<?php echo e(number_format($v->total,2)); ?></td>
                                <td style='width: 380%; font-size: 9px; padding-left: 20px;' id='letras'></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <br>

                <div style="max-width:560px;margin:0 auto">
                    <table style='min-height:10px; max-height:10px;'>
                        <tr>
                            <td style='width: 34%; height: 15px; font-size: 15px;'>Facturas en Nuestro Poder para su Revisión</td>
                        </tr>
                    </table>
                </div>                    
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            <div class="e1" style="padding-top: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:15px; text-align: left;">Fecha Documento</th>
                            <th style="width:15px; text-align: left;">No. Documento</th>
                            <th style="width:15px; text-align: left;">Moneda</th>
                            <th id="totales">Valor Documento</th>
                        </tr>
                    </thead>

                    <?php $stotal=0 ?>
                    <?php $__currentLoopData = $lims_detalle_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $stotal += $d->grand_total ?>
                        <tr>
                            <td style="width:15px; text-align: left;"><?php echo e(\Carbon\Carbon::parse($d->created_at)->format('d/m/Y')); ?></td>
                            <td style="width:15px; text-align: left;"><?php echo e($d->invoice); ?></td>
                            <td style="width:15px; text-align: left;">USD</td>
                            <td id="totales" class="text-right" colspan="1" style="width:15px;"><?php echo e(number_format($d->grand_total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $lims_detalle_quedan_nc_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $stotal -= $d->grand_total ?>
                        <tr>
                            <td style="width:15px; text-align: left;"><?php echo e(\Carbon\Carbon::parse($d->created_at)->format('d/m/yy')); ?></td>
                            <td style="width:15px; text-align: left;">NC - <?php echo e($d->invoice); ?></td>
                            <td style="width:15px; text-align: left;">USD</td>
                            <td id="totales" class="text-right" colspan="1" style="width:15px;">-<?php echo e(number_format($d->grand_total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <tfoot>
                        <tr>
                            <th style="width:15px;">SUMA:</th>
                            <th style="width:15px;"></th>
                            <th style="width:15px;"></th>
                            <th id="totales" class="text-right" colspan="1">$<?php echo e(number_format($stotal,2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <br>
            
            <div style="max-width:560px;margin:0 auto">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 34%; height: 15px; font-size: 15px;'>
                            NOTA: ESTE DOCUMENTO QUEDA SIN EFECTO AL REALIZARSE EL PAGO POR MEDIO DE CHEQUE
                            O ABONO POR TRANSFERENCIA ELECTRONICA A SU CUENTA
                        </td>
                    </tr>
                </table>
            </div>           
        </div>

        <br>
        <br>
        
        <!---QUEDAN 2 SOLO COPIAR LA DATA DE EL DIV DE ARRIBA ---->
        
        <div id="receipt-data" class="contenedor">
            <?php $mlibro=0 ?>
            <?php $ylibro=0 ?>
            
            <div class="centered">
                <?php if($general_setting->site_logo): ?>
                    <img src="<?php echo e(url('public/logo', $general_setting->site_logo)); ?>" class="logo">
                <?php endif; ?>
            </div>

            <div class="centered">
                <?php $__currentLoopData = $lims_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="right">
                        <?php echo QrCode::size(60)->generate($qr->reference_no); ?>

                    </div> 
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>    
            </div>    
            
            <div class="centered">
                <p><strong>MR. J&B INVERSIONES DEL CIELO, S.A. DE C.V.</strong></p>

                <p>
                    Sucursal que Emite el Quedan: <?php echo e($lims_warehouse_data->name); ?>

                    <br>
                    Dirección: <?php echo e($lims_warehouse_data->address); ?>

                    <br>
                    Telefono: <?php echo e($lims_warehouse_data->phone); ?>

                </p>
            </div>

            
            <?php $__currentLoopData = $lims_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
                    $fecha = \Carbon\Carbon::parse($v->date_quedan);
                    $mlibro = $meses[($fecha->format('n')) - 1];
                ?>
                <?php $ylibro = \Carbon\Carbon::parse($v->date_quedan)->format('y')  ?>
                
                <div style="max-width:560px;margin:0 auto">
                    <!--Formato a  linea 1 (Numero y fecha): -->
                    <div class="">
                        <table  style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 60%'>Número de Quedan: <font color="#FF0000"> <?php echo e($v->id); ?> </font></td>
                                <td style='width: 87%'>Fecha de Emisión: <?php echo e(\Carbon\Carbon::parse($v->created_at)->format('d/m/Y')); ?> </td>
                            </tr>
                        </table>
                    </div>
                
                    <!--Formato a  linea 2: (Nombre) -->
                    <div class="">
                        <table   style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 60%'>a Favor de: <?php echo e($v->name); ?></td>
                                <td style='width: 87%'>Fecha estimada de Pago: <?php echo e(\Carbon\Carbon::parse($v->due_date)->format('d/m/Y')); ?> </td>
                            </tr>
                        </table>
                    </div>
                    
                    <br>
                    
                    <!--Formato a  linea 3: (municipio, venta a cuenta de) -->
                    <div class="">
                        <table  style='min-height:10px; max-height:10px;'>
                            <tr>
                                <td style='width: 34%; height: 15px;'>por un Valor de: $<?php echo e(number_format($v->total,2)); ?></td>
                                <td style='width: 380%; font-size: 9px; padding-left: 20px;' id='letras'></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <br>

                <div style="max-width:560px;margin:0 auto">
                    <table style='min-height:10px; max-height:10px;'>
                        <tr>
                            <td style='width: 34%; height: 15px; font-size: 15px;'>Facturas en Nuestro Poder para su Revisión</td>
                        </tr>
                    </table>
                </div>                    
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            <div class="e1" style="padding-top: 8px;">
                <table>
                    <thead>
                        <tr>
                            <th style="width:15px; text-align: left;">Fecha Documento</th>
                            <th style="width:15px; text-align: left;">No. Documento</th>
                            <th style="width:15px; text-align: left;">Moneda</th>
                            <th id="totales">Valor Documento</th>
                        </tr>
                    </thead>

                    <?php $stotal=0 ?>
                    <?php $__currentLoopData = $lims_detalle_quedan_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $stotal += $d->grand_total ?>
                        <tr>
                            <td style="width:15px; text-align: left;"><?php echo e(\Carbon\Carbon::parse($d->created_at)->format('d/m/yy')); ?></td>
                            <td style="width:15px; text-align: left;"><?php echo e($d->invoice); ?></td>
                            <td style="width:15px; text-align: left;">USD</td>
                            <td id="totales" class="text-right" colspan="1" style="width:15px;"><?php echo e(number_format($d->grand_total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $lims_detalle_quedan_nc_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $stotal -= $d->grand_total ?>
                        <tr>
                            <td style="width:15px; text-align: left;"><?php echo e(\Carbon\Carbon::parse($d->created_at)->format('d/m/Y')); ?></td>
                            <td style="width:15px; text-align: left;">NC - <?php echo e($d->invoice); ?></td>
                            <td style="width:15px; text-align: left;">USD</td>
                            <td id="totales" class="text-right" colspan="1" style="width:15px;">-<?php echo e(number_format($d->grand_total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <tfoot>
                        <tr>
                            <th style="width:15px;">SUMA:</th>
                            <th style="width:15px;"></th>
                            <th style="width:15px;"></th>
                            <th id="totales" class="text-right" colspan="1">$<?php echo e(number_format($stotal,2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <br>
            
            <div style="max-width:560px;margin:0 auto">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 34%; height: 15px; font-size: 15px;'>
                            NOTA: ESTE DOCUMENTO QUEDA SIN EFECTO AL REALIZARSE EL PAGO POR MEDIO DE CHEQUE
                            O ABONO POR TRANSFERENCIA ELECTRONICA A SU CUENTA
                        </td>
                    </tr>
                </table>
            </div>      
        </div>                


        <script type="text/javascript">
            localStorage.clear();
            function auto_print() {
                window.print()
            }
            setTimeout(auto_print, 1000);
        </script>

        <script>
            total= document.getElementById("total").value
            strvalor= NumeroALetras(total)

            document.getElementById("letras").innerHTML=strvalor
            //document.getElementById("letras2").innerHTML=strvalor
            //alert(strvalor)
            //alert(document.getElementById("total_gral").innerHTML=NumeroALetras(this.value))

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

  //return Seccion(num, divisor, "UN MIL", "MIL") + " " + Centenas(resto);
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

  //return Seccion(num, divisor, "UN MILLON", "MILLONES") + " " + Miles(resto);
}//Millones()

function NumeroALetras(num,centavos){
  var data = {
    numero: num,
    enteros: Math.floor(num),
    centavos: (((Math.round(num * 100)) - (Math.floor(num) * 100))),
    letrasCentavos: "",
  };
  if(centavos == undefined || centavos==false) {
    data.letrasMonedaPlural="DOLARES";
    data.letrasMonedaSingular="DOLAR";
  }else{
    data.letrasMonedaPlural="CENTAVOS";
    data.letrasMonedaSingular="CENTAVO";
  }

  if (data.centavos > 0)
    data.letrasCentavos = "CON " + NumeroALetras(data.centavos,true);

  if(data.enteros == 0)
    return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
  if (data.enteros == 1)
    return Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
  else
    return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
}//NumeroALetras()
</script>
    </body>
</html><?php /**PATH /home/ltygkxsm/public_html/mrjbnew/resources/views/quedan_purchase/pdf.blade.php ENDPATH**/ ?>