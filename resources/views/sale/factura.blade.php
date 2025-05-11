<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/png" href="{{url('public/logo', $general_setting->site_logo)}}" />
    <title>{{$general_setting->site_title}}</title>
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
        /* Agregar aquí el CSS para saltos de página */
        .page-break-before {
            page-break-before: always;
        }

        .page-break-after {
            page-break-after: always;
        }
        /* Asegúrate de no tener márgenes o padding que puedan causar una página en blanco */
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body> 
    @foreach ($pdfData as $data)
    <div class="container-fluid">      
        <div class="centered">
            @if($general_setting->site_logo)
                <img src="{{url('public/logo', $general_setting->site_logo)}}" class="logo">
            @endif
        </div> 

        <div id="header">
            <div class="textContri" style="margin-top: 55px; margin-left: -45;">
                <p class="dteIzq" style="margin-top: 0px; margin-bottom: 0px;">
                    <br>
                    <br>
                    AUTOLOTE GUTIERREZ,
                    <br>
                    5TA. CALLE pTE Y 2DA AV NORTE #2-2
                    <br>
                    Tel.: 2228-2984
                    <br>
                    autolote.gutierrez@yahoo.com
                    <br>
                    
                    Tipo establecimiento: Casa Matriz
                    <br>
                   
                    <br>
                   
                </p>
            </div>      
        </div>

        <div id="header">       
            <div class="infoHeader">
                <p style="margin-top: 0px; margin-bottom: 0px; font-size: 14px;">DOCUMENTO TRIBUTARIO ELECTRÓNICO</p>
                <p style="margin-top: 0px; margin-bottom: 0px; text-align: center; font-size: 14px;">FACTURA CONSUMIDOR FINAL</p>              
            </div>
        
            <div class="container">
                <div class="row">             
                    <div class="col-4">
                        <div class="textContri" style="margin-top: 40px; padding-left: 150px;">
                            <<p class="dte">Código generación: {{ $data['lims_sale_data']->codgeneracion }}</p>
                            <br>
                            <p class="dte">Número de control: {{ $data['lims_sale_data']->numerocontrol }}</p>
                            <br>
                            <p class="dte">Sello de recepción: {{ $data['lims_sale_data']->sello }}</p>
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
                                    <p class="dte" style="margin-left: 3px;">
                                        Fecha de emisión: {{ \Carbon\Carbon::parse($data['lims_sale_data']->created_at)->format('dmY') }}
                                    </p>
                                </div>                         
                            </div>
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Hora de emisión: {{ \Carbon\Carbon::parse($data['lims_sale_data']->created_at)->toTimeString() }}</p>
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
        @php $mlibro=0 @endphp
        @php $ylibro=0 @endphp
        @php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");

            // Obtener los datos desde el primer elemento del array
            $lims_sale_data = $data['lims_sale_data'];
            $valor = $data['valor'];

            $fecha = \Carbon\Carbon::parse($data['lims_sale_data']->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
            $ylibro = $fecha->format('Y');
        @endphp        
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
            <p class="textFooter"></p>
        </div>
        
        <div class="dheader">
            <div class="qr" style="margin-top: -120px; padding-left: 200px;">            
                <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
            </div>
        </div>
    </div>

    <div id="receipt-data">
        @php $mlibro=0 @endphp
        @php $ylibro=0 @endphp
        @php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = \Carbon\Carbon::parse($data['lims_sale_data']->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
            $ylibro = $fecha->format('Y');
        @endphp
        @php
            // Obtener los datos desde el primer elemento del array
            $lims_customer_data = $data['lims_customer_data'];
        @endphp
        <!-- <div style="max-width:600px;margin:0 auto"> -->
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea 1 (Nombre): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 77%;'>Nombre o razón social: {{ $lims_customer_data->name }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 2 (Tipo documento y documento): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        @if($lims_customer_data->type_taxpayer_id == 5)
                            <td style='width: 70%;'>Tipo de doc. de Identificación: DUI</td>
                            <td>DUI: {{ $lims_customer_data->nit }}</td>                        
                        @else
                            <td style='width: 70%;'>Tipo de doc. de Identificación: NIT</td>
                            <td>NIT: {{ $lims_customer_data->nit }}</td>                        
                        @endif  
                    </tr>
                </table>
            </div>
            
            <!--Formato a  linea 3: (Correo y nombre comercial) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Correo Electrónico: {{ $lims_customer_data->email }}</td>
                        <td>Nombre comercial: {{ $lims_customer_data->company_name }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 4: (Departamento y forma pago) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>                        
                        <td style='width: 70%'>Departamento: {{ $lims_customer_data->estado->name }}</td>                        
                        @if($lims_sale_data->payment_method=="contado")
                            <td>Forma pago: Contado</td>
                        @else
                            <td>Forma pago: Crédito</td>
                        @endif
                    </tr>t
                </table>
            </div>

            <!--Formato a  linea 5: (Municipio y Moneda) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Municipio: {{ $lims_customer_data->municipio->name }}</td>
                        <td>Moneda: USD</td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

        @php $i = 1 @endphp
        @if($lims_sale_data->terceros=="on")
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
        @else
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
        @endif

        <br>

        <div id=tabla1>
            <div id=cabtab1>
                DOCUMENTOS RELACIONADOS
            </div>
            <table  style='min-height:10px; max-height:10px;'>
                <tr>
                    <td style="width:15px;">Tipo de Documento</td>
                    <td style="width:15px;">Número de documento</td>
                    <td style="width:5px; text-align: center;">Fecha de emisión</td>
                </tr>    
            </table>  
        </div>

        <br>

        <div id=tabla1>
            <div id=cabtab1>
                OTROS DOCUMENTOS ASOCIADOS
            </div>
            <table  style='min-height:10px; max-height:10px;'>
                <tr>
                    <td>Identificación documento:</td>                        
                    <td style='width: 70%;'>Descripción:</td>
                </tr>
            </table>  
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
                    <th style="width:15px;">No.</th>
                    <th style="width:15px;">Cantidad</th>
                    <th style="width:5px; text-align: center;">Unidad</th>
                    <th style="width:80px; text-align: left;">Descripción</th>
                    <th style="width:5px; text-align: left;">Precio Unitario</th>
                    <th style="width:3px;">Otros montos no afectos</th>
                    <th style="width:3px;">Descto. por Item</th>
                    <th style="width:3px;">Ventas no Sujetas</th>
                    <th style="width:3px;">Ventas Exentas</th>
                    <th style="width:3px;">Ventas Gravadas</th>
                </tr>

                <?php $total_product_tax = 0;?>
                @php $i = 1 @endphp
                @php
                    // Obtener los datos desde el primer elemento del array
                    $lims_product_sale_data = $data['lims_product_sale_data'];
                @endphp                
                @foreach($lims_product_sale_data as $product_sale_data)
                    <?php
                        $lims_product_data = \App\Product::find($product_sale_data->product_id);
                        if($product_sale_data->variant_id) {
                            $variant_data = \App\Variant::find($product_sale_data->variant_id);
                            $product_name = $lims_product_data->name.' ['.$variant_data->name.']';
                            $description = $product_sale_data->description;
                            $licitacion = $lims_sale_data->licitacion;
                            $medida = $lims_product_data->unit->unit_name;
                        }
                        else
                            $product_name = $lims_product_data->name;
                            $description = $product_sale_data->description;
                            $licitacion = $lims_sale_data->licitacion;
                            $medida = $lims_product_data->unit->unit_name;
                    ?>
                    <tr>
                        <?php
                            $total_product_tax += $product_sale_data->tax;
                        ?>
                        <td style="width:15px; text-align: center;">{{ $i++ }}</td>
                        <td style="width:15px; text-align: right;">{{ number_format((float) $product_sale_data->qty, 4, '.', '')}}</td>
                        <td style="width:5px; text-align: center;"><?php echo $medida; ?></td>
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
                            {{ number_format((float)(($product_sale_data->total) / $product_sale_data->qty), 2, '.', '') }}
                        </td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px; text-align: right;">
                            {{ number_format((float)($product_sale_data->total ), 2, '.', ',') }}
                        </td>
                    </tr>
                @endforeach
            </table>          
        </div>
                
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea (Sumatoria): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <th style='width: 61.8%;'></th>
                        <td>Sumatoria de ventas:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->grand_total, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Desc. no sujetas): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Monto global Desc., Rebajas y otros a ventas no sujetas:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->discount, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Desc. exentas): -->
            <div>
                <div class="">
                    <table style='min-height:10px; max-height:10px;'>
                        <tr>
                            <td style='width: 61.8%;'></td>
                            <td>Monto global Desc., Rebajas y otros a ventas exentas:</td>
                            <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                                {{number_format((float)$lims_sale_data->discount, 2, '.', ',')}} 
                            </td>
                        </tr>
                    </table>
                </div>    
            </div>

             <!--Formato a  linea (Desc. gravadas): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Monto global Desc., Rebajas y otros a ventas gravadas:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->discount, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

             <!--Formato a  linea (Subtotal): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Sub-total:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->grand_total, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Iva retenido): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>IVA Retenido:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->order_tax, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Renta): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Retención Renta:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->discount, 2, '.', ',')}} 
                        </td>
                    </tr>
                </table>
            </div>    

            <!--Formato a  linea (Monto total): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Monto Total de la Operación:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->grand_total, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total otros no afectos): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Total Otros Montos No Afectos:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->discount, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total a pagar): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 61.8%;'></td>
                        <td>Total a Pagar:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_sale_data->grand_total, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

        <div class="container-fluid" id="tabla1">  
            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 225%; text-align: left; font-size: 9px;'>Valor en letras: {{ $data['todo'] }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Observaciones): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 240%; text-align: left; font-size: 9px;'>Observaciones: {{ $lims_sale_data->sale_note }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <br>

        <div class="container-fluid" id="tabla1">  
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 127%; font-size: 9px;'>Responsable por parte del Emisor: {{ $data['userName'] }}</td>
                        <td style='width: 100%; text-align: left; padding-left: 2px; padding-right: 40px;'>No. Documento: {{ $data['userNit'] }}</td>
                    </tr>
                </table>
            </div>

            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 127%; font-size: 9px;'>Responsable por parte del Receptor:</td>
                        <td style='width: 100%; text-align: left; padding-left: 2px; padding-right: 40px;'>No. Documento:</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    @if (!$loop->last)
        <div class="page-break-after"></div>
    @endif

    @endforeach
    

    <script type="text/javascript">
        localStorage.clear();
        function auto_print() {
            window.print()
        }
        setTimeout(auto_print, 1000);
    </script>
    
    <script type="text/php">
        if ( isset($pdf) ) {
            $font = $fontMetrics->get_font("helvetica", "normal");
            $pdf->page_text(270, 760, "Pag {PAGE_NUM} / {PAGE_COUNT}", $font, 10);
        }
    </script>
</body>
</html>
