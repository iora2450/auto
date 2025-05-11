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

    <style type="text/css">
        * {
            font-size: 14px;

            font-family: 'Ubuntu', sans-serif;
            text-transform: capitalize;
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
        }
        small{font-size:11px;}

        @media print {
            * {
                font-size:12px;
               /* line-height:16px;*/
            }
          /*  td,th {padding: 5px 0;}*/
            .hidden-print {
                display: none !important;
            }
            @page { margin: 0; } body { margin: 0.5cm; margin-bottom:1.6cm; }
        }

        div.e2 {
            margin-left:40px;
            max-width:650px;
        }
    </style>
</head>
<body>
    <div style="max-width:560px;margin:0 auto">
        @if(preg_match('~[0-9]~', url()->previous()))
            @php $url = '../../pos'; @endphp
        @else
            @php $url = url()->previous(); @endphp
        @endif

        <div class="hidden-print">
            <table>
                <tr>
                    <td>
                        <a href="{{$url}}" class="btn btn-info">
                            <i class="fa fa-arrow-left"></i>
                            {{trans('file.Back')}}
                        </a>
                    </td>
                    <td>
                        <button onclick="window.print();" class="btn btn-primary">
                            <i class="dripicons-print"></i>
                            {{trans('file.Print')}}
                        </button>
                    </td>
                </tr>
            </table>
            <br>
        </div>
    </div>

    <div id="receipt-data">
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
        @php $mlibro=0 @endphp
        @php $ylibro=0 @endphp
        @php
            $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
            $fecha = \Carbon\Carbon::parse($lims_sale_data->created_at);
            $mlibro = $meses[($fecha->format('n')) - 1];
        @endphp
        @php $ylibro = \Carbon\Carbon::parse($lims_sale_data->created_at)->format('Y')  @endphp
        <div style="max-width:560px;margin:0 auto">
            <!--Formato a  linea 1 (Nombre y fecha): -->
            <div class="" style="margin-top:-10px;">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 65%'> {{$lims_customer_data->name}} </td>
                        <td>
                            {{ \Carbon\Carbon::parse($lims_sale_data->created_at)->format('d').'-'.$mlibro.'-'.$ylibro }}
                        </td>
                    </tr>
                </table>
            </div>
            <!--Formato a  linea 2: (Direccion y registro) -->
            <div class="" style="margin-top:5px;">
                <table   style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 67%' > {{$lims_customer_data->address .', '.$lims_customer_data->city}}</td>
                        <td>{{$lims_customer_data->tax_no}}  </td>
                    </tr>
                </table>
            </div>
            <!--Formato a  linea 3: (municipio, nit) -->
            <div class="" style="margin-top:5px;">
                <table  style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 62%; padding-left: 105px;' >{{$lims_customer_data->state}} </td>
                        <td>{{$lims_customer_data->nit}}  </td>
                    </tr>
                </table>
            </div>
            <!--Formato a  linea 4: (giro) -->
            <div>
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style="width: 13%; padding-left: 312px;"></td>
                        <td NOWRAP>{{$lims_customer_data->postal_code}}</td>
                    </tr>
                </table>
            </div>
            <!--Formato a  linea 5: (forma de pago) -->
            <div class="" style="margin-top:5px;">
                <table style='min-height:10px; max-height:10px;'>
                    <tr>
                        <td style='width: 13%; padding-left: 200px;'></td>
                        @if($lims_sale_data->payment_method=="contado")
                            <td style='width: 6%'>Contado</td>
                        @else
                            <td style='width: 6%'>Crédito</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>

        <!-- Linea 6: Lineas  de detalle -->
        <div class="e2">
            <br>
            <br>
            <br>
            <div style='min-height:170px; max-height:170px; overflow:hidden; width: 730px;'>
                <?php $total_product_tax=0;
                    $total_total=0;
                ?>
                <table>
                    <tbody>
                        <?php $total_product_tax = 0;?>
                        @foreach($lims_product_sale_data as $product_sale_data)
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
                                <td style='width: 1%;'></td>
                                <td style='width: 5%; float:left;'>
                                    {{number_format((float) $product_sale_data->qty, 2, '.', '')}}
                                </td>
                                <td style='width: 62%; padding-left: 10px;'><?php
                                    if($licitacion !="off"){
                                        echo $description;
                                    }else{
                                        echo $product_name;
                                    }
                                ?></td>
                                <td style='width:8%; text-align: right; padding-right: 48px;'>
                                    {{number_format((float)($product_sale_data->net_unit_price), 2)}}
                                </td>
                                <td style='width:8%;'></td>
                                <td style='width:8%;'></td>
                                <td style='width:8%; padding-right: 85px; text-align: right;'>
                                    {{number_format((float)($product_sale_data->net_unit_price * $product_sale_data->qty ), 2)}}
                                </td>
                            </tr>
                            <tr>
                                <td style='width: 1%;'></td>
                                <td style='width:5%; float:left;'></td>
                                <td style='width: 62%; padding-left: 12px; padding-bottom: 10px;'>
                                    <?php
                                        if($licitacion !="on"){
                                            echo $description;
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                                $total_total+= $product_sale_data->net_unit_price * $product_sale_data->qty ;
                            ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <!--Formato a  linea 7 sumas ventas no sujetas, retencion, ventas exentas, ventas afecta) -->
        <div style="max-width:710px; margin-top:90px; margin-bottom: 0 auto; margin-right: 0 auto; margin-left: 25px;">
            <div class="">
                <table>
                    <tbody>
                        <tr>
                            <td style='width: 3%;'></td>
                            <td style='width: 5%;'></td>
                            <td style='width: 60%; font-size: 9px;' id='letras'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%; text-align: right; padding-right: 50px; padding-bottom:5px;'>{{number_format((float)$total_total, 2, '.', '')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!--Formato a  linea 8 iva) -->
            <div class="">
                <table>
                    <tbody>
                        <tr>
                            <td style='width: 3%;'></td>
                            <td style='width: 5%;'></td>
                            <td style='width: 60%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%; text-align: right; padding-right: 50px;'>{{number_format((float)$total_product_tax, 2)}}
                                <input type='hidden'  value="{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}" id=''>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--Formato a  linea 9  venta con iva-->
            <div class="">
                <table>
                    <tbody>
                        <tr>
                            <td  style='width: 3%;'></td>
                            <td  style='width: 5%;'></td>
                            <td  style='width: 60%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%; text-align: right; padding-right: 50px;'>{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}
                                <input type='hidden' value='{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}' id='total_gral'>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--Formato a  linea 8  venta Exenta-->
            <div class="">
                <table>
                    <tbody>
                        <tr>
                            <td  style='width: 3%;'></td>
                            <td  style='width: 60%'></td>
                            <td  style='width: 5%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%; text-align: right; padding-right: 50px;'>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <br>
            <br>
            <!--Formato a  linea 9  venta total-->
            <div class="">
                <table>
                    <tbody>
                        <tr>
                            <td  style='width: 3%;'></td>
                            <td  style='width: 5%;'></td>
                            <td  style='width: 60%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%;'></td>
                            <td style='width:8%; text-align: right; padding-right: 50px;'>{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}
                                <input type='hidden' value='{{number_format((float)$lims_sale_data->grand_total, 2, '.', '')}}' id='total_gral'>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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


<script>
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
