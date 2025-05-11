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
            color: #A6D52F;
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
            border: 1px solid #1E679A;
            width: 100%;
            font-size: 10px;
        }
        #tabla2{
            border: 1px solid #1E679A;
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
            @if($general_setting->site_logo)
                <img src="{{url('public/logo', $general_setting->site_logo)}}" class="logo">
            @endif
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
                <p style="margin-top: 0px; margin-bottom: 0px; text-align: center; font-size: 14px;">NOTA DE CRÉDITO</p>              
            </div>
        
            <div class="container">
                <div class="row">             
                    <div class="col-4">
                        <div class="textContri" style="margin-top: 40px; padding-left: 150px;">
                            <p class="dte">Código generación: {{ $lims_return_data->codgeneracion }}</p>
                            <br>
                            <p class="dte">Número de control: {{ $lims_return_data->numerocontrol }}</p>
                            <br>
                            <p class="dte">Sello de recepción: {{ $lims_return_data->sello }}</p>                        
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
                                    <p class="dte" style="margin-left: 3px;">Fecha de emisión: {{ \Carbon\Carbon::parse($lims_return_data->created_at)->format('d/m/Y') }}</p>
                                </div>                        
                            </div>
                            <br>
                            <div class="row dte">                            
                                <div class="col-6">
                                    <p class="dte">Hora de emisión: {{ $lims_return_data->created_at->toTimeString() }}</p>            
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
            $fecha = \Carbon\Carbon::parse($lims_return_data->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
        @endphp
        @php $ylibro = \Carbon\Carbon::parse($lims_return_data->created_at)->format('Y')  @endphp
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
                <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
            </div>
        </div>
        
       {{--  <div class="header">
            @foreach($lims_product_return_data as $qr)
                <div class="logo" style="margin-top: -90px; padding-left: 250px;">
                    <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
                    <p style="font-size: 9px;">Código generación</p>
                </div> 

                <div class="logo" style="margin-top: -90px; padding-left: 400px;">
                    <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
                    <p style="font-size: 9px;">Sello recibido</p>
                </div>

                <div class="logo" style="margin-top: -90px; padding-left: 550px;">
                    <img src="data:image/svg+xml;base64,{{ base64_encode($valor) }}">
                    <p style="font-size: 9px;">Número de control</p>
                </div>
            @endforeach    
        </div>        

        <div class="centered">
            @foreach($lims_product_return_data as $qr)
                <div class="right">
                    {!! QrCode::size(60)->generate($qr->qty) !!}
                </div> 
            @endforeach    
        </div> --}}
        

    <div id="receipt-data">
        @php $mlibro=0 @endphp
        @php $ylibro=0 @endphp
        @php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = \Carbon\Carbon::parse($lims_return_data->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
        @endphp
        @php $ylibro = \Carbon\Carbon::parse($lims_return_data->created_at)->format('Y')  @endphp
        <!-- <div style="max-width:600px;margin:0 auto"> -->
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea 1 (Nombre): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 77%;'>Nombre o Razón Social: {{ $lims_customer_data->name }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 2 (Actividad y Nit): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Act. Económica: {{ $lims_customer_data->name }} </td>
                        <td>NIT: {{ $lims_customer_data->nit }}</td>                        
                    </tr>
                </table>
            </div>
            
            <!--Formato a  linea 3: (Correo y registro) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Correo: {{ $lims_customer_data->email }}</td>
                        <td>NRC: {{ $lims_customer_data->tax_no }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 4: (Dirección y telefono) -->
            <div class="">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Dirección: {{ $lims_customer_data->address }}</td>
                        <td>Municipio: {{ $lims_customer_data->state }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 5: (Municipio y forma pago) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'>Nombre comercial: {{ $lims_customer_data->company_name }}</td> 
                        <td>Departamento: {{ $lims_customer_data->state }}</td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea 6: (Departamento y Moneda) -->
            <div class="">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        @if($lims_return_data->payment_method=="contado")
                            <td style='width: 70%'>Forma pago: Contado</td>
                        @else
                            <td style='width: 70%'>Forma pago: Crédito</td>
                        @endif
                        <td>Moneda: USD</td>
                    </tr>
                </table> 
            </div>
        </div>

        <br>

        @php $i = 1 @endphp
        @if($lims_return_data->payment_method=="credito")
        <div id=tabla1>
            <div id=cabtab1>
                VENTA A CUENTA DE TERCEROS
            </div>
            <table  style='min-height:10px; max-height:10px;'>
                <tr>
                    <td>NIT: {{ $lims_customer_data->nit }}</td>                        
                    <td style='width: 70%;'>Nombre, denominación o razón social: {{ $lims_customer_data->name }}</td>
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
                    <th style="width:15px;">Tipo de Documento</th>
                    <th style="width:15px;">Número de documento</th>
                    <th style="width:5px; text-align: center;">Fecha de emisión</th>
                </tr>
                <tr>
                    <td style='width: 60%; text-align: center;'>Comprobante de crédito fiscal</td>                        
                    <td style='width: 28%; text-align: center;'>{{ $lims_return_data->numeroControlInput }}</td>
                    <td style='width: 28%; text-align: center;'>{{ \Carbon\Carbon::parse($lims_return_data->dateSale)->format('d/m/Y') }}</td>
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

                <?php $total_product_tax = 0;?>
                @php $i = 1 @endphp
                @foreach($lims_product_return_data as $product_return_data)
                    <?php
                        $lims_product_data = \App\Product::find($product_return_data->product_id);
                        if($product_return_data->variant_id) {
                            $variant_data = \App\Variant::find($product_return_data->variant_id);
                            $product_name = $lims_product_data->name.' ['.$variant_data->name.']';
                            $description = $product_return_data->description;
                            $licitacion = $lims_return_data->licitacion;
                        }
                        else
                            $product_name = $lims_product_data->name;
                            $description = $product_return_data->description;
                            $licitacion = $lims_return_data->licitacion;
                    ?>
                    <tr>
                        <?php
                            $total_product_tax += $product_return_data->tax;
                        ?>
                        <td style="width:15px; text-align: center;">{{ $i++ }}</td>
                        <td style="width:15px; text-align: right;">{{ number_format((float) $product_return_data->qty, 2, '.', '')}}</td>
                        <td style="width:5px; text-align: center;">Unidad</td>
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
                            {{ number_format((float)(($product_return_data->total / 1.13) / $product_return_data->qty), 2, '.', '') }}
                        </td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px;"></td>
                        <td style="width:3px; text-align: right;">
                            {{ number_format((float)($product_return_data->total / 1.13 ), 2, '.', '') }}
                        </td>
                    </tr>
                @endforeach
            </table>          
        </div>
                
        <div class="container-fluid" id="tabla1">
            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <th style='width: 244%; text-align: left; background-color: red;'>Valor en letras: {{ $todo }}</th>
                        <td style='width: 100%; text-align: left; padding-right: 10px;'>Sumas de ventas:</td>
                        <td style='width: 8%; text-align: right; padding-left: 5px; padding-right: 25px;'>0.00</td>
                        <td>0.00</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->grand_total / 1.13, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 227%; background-color: red;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Suma Total de Operaciones:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->grand_total / 1.13, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total descuentos): -->
            <div>
                <div class="">
                    <table style='min-height:10px; max-height:10px;'>
                        <tr>
                            <td id=cabtab2 style='width: 67.8%;'>OBSERVACIONES</td>
                            <td>
                                Total descuentos: 
                            </td>
                            <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                                {{number_format((float)$lims_return_data->discount / 1.13, 2, '.', '')}} 
                            </td>
                        </tr>
                    </table>
                </div>    
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 227%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Impuesto al Valor Agregado 13%:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$total_product_tax, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 227%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px;'>Sub-total:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->grand_total / 1.13, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>

             <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'></td>
                        @if($lims_return_data->discount > 0)
                            <td style='width:70%; text-align: right; padding-left: 40px; padding-right: 124px;'>IVA Percibido:</td>
                        @else
                            <td style='width:70%; text-align: right; padding-left: 40px; padding-right: 131px;'>IVA Percibido:</td>
                        @endif
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->discount / 1.13, 2, '.', '')}}
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
                            {{number_format((float)$lims_return_data->discount / 1.13, 2, '.', '')}} 
                        </td>
                    </tr>
                </table>
            </div>    

            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 127%; font-size: 9px; background-color: red;'>Nombre entrega: {{ $userName }}</td>
                        <td style='width: 100%; text-align: left; padding-left: 2px; padding-right: 40px; background-color: blue;'>No. Documento: {{ $lims_customer_data->nit }}</td>
                        @if($lims_return_data->discount > 0)
                            <td style='width: 100%; text-align: left; padding-right: 40px;'>Retención Renta:</td>
                        @else
                            <td style='width: 100%; text-align: left; padding-right: 40px; background-color: green;'>Retención Renta:</td>
                        @endif
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->discount / 1.13, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>


            <!--Formato a  linea (Valor en letras): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 127%; font-size: 9px; background-color: red;'>Nombre recibe: {{ $lims_customer_data->name }}</td>
                        <td style='width: 100%; text-align: left; padding-left: 2px; padding-right: 40px; background-color: blue;'>No. Documento: {{ $lims_customer_data->nit }}</td>
                        <td style='width: 100%; text-align: left; padding-right: 40px; background-color: green;'>Monto Total de la Operación:</td>
                        <td style='width: 8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->grand_total / 1.13, 2, '.', ',')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total otros montos no afectos): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 70%;'></td>
                        @if($lims_return_data->discount > 0)
                            <td style='width:70%; text-align: right; padding-left: 20px; padding-right: 67px;'>Total otros montos no Afectos:</td>
                        @else
                            <td style='width:70%; text-align: right; padding-left: 20px; padding-right: 75px;'>Total otros montos no Afectos:</td>
                        @endif                        
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->discount / 1.13, 2, '.', '')}}
                        </td>
                    </tr>
                </table>
            </div>

            <!--Formato a  linea (Total a pagar): -->
            <div class="">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 227%;'></td>
                        <td style='width: 100%; text-align: left; padding-right: 40px; background-color: green;'>TOTAL A PAGAR:</td>
                        <td style='width:8%; text-align: right; padding-left: 20px; padding-right: 1px;'>
                            {{number_format((float)$lims_return_data->grand_total, 2, '.', ',')}} 
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
</body>
</html>
