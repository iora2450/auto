
<script>



 
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
    data.letrasMonedaPlural="";
    data.letrasMonedaSingular="";
  }else{
    data.letrasMonedaPlural="CENTAVOS";
    data.letrasMonedaSingular="CENTAVO";
  }
 
  if (data.centavos > 0)
    data.letrasCentavos = "" + data.centavos + "/100 USD";
 
  if(data.enteros == 0)
    return "CERO " + data.letrasMonedaPlural + " " + data.letrasCentavos;
  if (data.enteros == 1)
    return Millones(data.enteros) + " " + data.letrasMonedaSingular + " " + data.letrasCentavos;
  else
    return Millones(data.enteros) + " " + data.letrasMonedaPlural + " " + data.letrasCentavos;
}//NumeroALetras()




</script>



<style>
@media print { body {-webkit-print-color-adjust: exact;} }

.pagebreak { page-break-before: always; } /* page-break-after works, as well */
</style>
@extends('layout.main') @section('content')
@if(session()->has('message'))
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{!! session()->get('message') !!}</div> 
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif




<section>
    <div class="container-fluid">
        @if(in_array("quotes-add", $all_permission))
            <a href="{{route('quotations.create')}}" class="btn btn-info"><i class="dripicons-plus"></i> {{trans('file.Add Quotation')}} / Remision</a>
        @endif
    </div>
    <div class="table-responsive">
        <table id="quotation-table" class="table quotation-list">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>{{trans('file.Date')}}</th>
                    <th>{{trans('file.reference')}}</th>
                    <th>{{trans('file.Biller')}}</th>
                    <th>{{trans('file.customer')}}</th>
                    <th>{{trans('file.Supplier')}}</th>
                    <th>Estado</th>
                    <th>{{trans('file.grand total')}}</th>
                     <th>Tipo</th>
                    <th class="not-exported">{{trans('file.action')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lims_quotation_all as $key=>$quotation)
                <?php
                    if($quotation->quotation_status == 1)
                        $status = trans('file.Pending');
                    else
                        $status = trans('file.Sent');
                ?>
                <tr class="quotation-link" data-quotation='["{{date($general_setting->date_format, strtotime($quotation->created_at->toDateString()))}}", "{{$quotation->reference_no}}", "{{$status}}", "{{$quotation->biller->name}}","{{$quotation->biller->company_name}}","{{$quotation->biller->email}}", "{{$quotation->biller->phone_number}}", "{{$quotation->biller->address}}", "{{$quotation->biller->city}}", "{{$quotation->customer->name}}", "{{$quotation->customer->phone_number}}","{{$quotation->customer->address}}", "{{$quotation->customer->city}}", "{{$quotation->id}}","{{$quotation->total_tax}}","{{$quotation->total_discount}}", "{{$quotation->total_price}}","{{$quotation->order_tax}}","{{$quotation->order_tax_rate}}","{{$quotation->order_discount}}", "{{$quotation->shipping_cost}}","{{$quotation->grand_total}}","{{$quotation->note}}","{{$quotation->user->name}}","{{$quotation->user->email}}","{{$quotation->credit_days}}","{{$quotation->method_pay}}","{{$quotation->delivery_time}}","{{$quotation->validity}}","{{$quotation->attention}}"
                    ,"{{$quotation->tittle}}","{{$quotation->name_seller}}","{{$quotation->cell_seller}}","{{$quotation->biller->image_firma}}" ]'>
                    <td>{{$key}}</td>
                    <td>{{ date($general_setting->date_format, strtotime($quotation->created_at->toDateString())) . ' '. $quotation->created_at->toTimeString() }}</td>
                    <td>{{ $quotation->reference_no }}</td>
                    <td>{{ $quotation->biller->name }}</td>
                    <td>{{ $quotation->customer->name }}</td>
                    @if($quotation->supplier_id)
                    <td>{{ $quotation->supplier->name }}</td>
                    @else
                    <td>N/A</td>
                    @endif
                    @if($quotation->quotation_status == 1)
                        <td><div class="badge badge-danger">{{$status}}</div></td>
                    @else
                        <td><div class="badge badge-success">{{$status}}</div></td>
                    @endif
                    <td>{{ $quotation->grand_total }}</td>
                    <td>{{ $quotation->type_quotations }}</td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{trans('file.action')}}
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <li>
                                    <button type="button" class="btn btn-link view"><i class="fa fa-eye"></i>  {{trans('file.View')}}</button>
                                </li>
                                @if(in_array("quotes-edit", $all_permission))
                                <li>
                                    <a class="btn btn-link" href="{{ route('quotations.edit', $quotation->id) }}"><i class="dripicons-document-edit"></i> {{trans('file.edit')}}</a></button> 
                                </li>
                                @endif
                                <li>
                                    <a class="btn btn-link" href="{{ route('quotation.create_sale', ['id' => $quotation->id]) }}"><i class="fa fa-shopping-cart"></i> {{trans('file.Create Sale')}}</a></button> 
                                </li>
                                <li>
                                    <a class="btn btn-link" href="{{ route('quotation.create_purchase', ['id' => $quotation->id]) }}"><i class="fa fa-shopping-basket"></i> {{trans('file.Create Purchase')}}</a></button> 
                                </li>
                                <li class="divider"></li>
                                @if(in_array("quotes-delete", $all_permission))
                                {{ Form::open(['route' => ['quotations.destroy', $quotation->id], 'method' => 'DELETE'] ) }}
                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> {{trans('file.delete')}}</button>
                                </li>
                                {{ Form::close() }}
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="tfoot active">
                <th></th>
                <th>{{trans('file.Total')}}</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                 <th></th>
                <th></th>
            </tfoot>
        </table>
    </div>
</section>

<div id="quotation-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog" style='width: 100% !important'>
      <div class="modal-content" >
        <header style='width: 100%'><img  src='./public/img/header.jpg' style="width: 100%;"></header>

        <div class="container">
            <div class="row">
                <div class="col-md-3">
                     <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none"><i class="dripicons-print"></i> {{trans('file.Print')}}</button>

                    {{ Form::open(['route' => 'quotation.sendmail', 'method' => 'post', 'class' => 'sendmail-form'] ) }}
                        <input type="hidden" name="quotation_id">
                        <button class="btn btn-default btn-sm d-print-none"><i class="dripicons-mail"></i> {{trans('file.Email')}}</button>
                    {{ Form::close() }}
                </div>
                <br><br>
                 <br><br>
                  <br><br>

                
                <div class="col-md-12">
                    <h5  class="modal-title text-left container-fluid">
                        <label id='tittle'></label>

                    </h5>
                </div>
              <!--
                <div class="col-md-3">
                    <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
            -->
            <style>
                .fondoAzul{background-color:#63a7d2 !important; color:white;  font-weight:bold; -webkit-print-color-adjust: exact !important;}
            </style>
            <br><br>
            




        </div>
        </div>
            <div id="quotation-content" class="modal-body">
            </div>
            <br>
<div class='container paging'>
          <style>
                .fondoAzul{background-color:#63a7d2; color:white;  font-weight:bold; -webkit-print-color-adjust: exact !important;}
            </style>

         <table class="table  product-quotation-list">

                   <thead>
                    <tr>
                    <th class=fondoAzul>#</th>
                    
                    <th class='fondoAzul'>Qty</th>
                    <th class='fondoAzul'>No. de parte / Descripción</th>
                   
                    <th class='fondoAzul'>Precio Unitario <br>(US $)</th>
                    <th class='fondoAzul'>Precio Total <br>(US $)</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

</div>

<div class="container">
    <center>
        <b>Total cotizado:</b>
        <label id="total"></label>
        
    </center>
<table style='width: 100%' border=1><tr><td><b>NOTA:<label id='notas'></label></b></td></tr></table>
<br>
<b><label style='text-decoration: underline;'>Condiciones comerciales:</label></b>
<table style='width: 100%'>
    <tr>
        <td style="width: 80%">
            <table>
                <tr>
                    <td><b>1)  Términos de pago:</b></td>
                    <td align=left> <label id='credit_days' style="padding-top: 8px;"></label></td>
                </tr>
                <tr>
                    <td><b>2)  Forma de pago: </b></td>
                    <td align=left><label id='method_pay' style="padding-top: 8px;"></label></td>
                </tr>
                <tr>
                    <td><b>3)  Tiempo de entrega:</b> </td>
                    <td align=left><label id='delivery_time' style="padding-top: 8px;"></label></td>
                </tr>
                <tr>
                    <td><b>4)  Validez de precios:</b></td>
                    <td align=left><label id='validity' style="padding-top: 8px;"></label></td>
                </tr>
    </table>


        </td>
        <td style='width: 20%; '></td>

    </tr>
</table>

<br>
<table style='width: 100%'>
    <tr>
        <td style="width: 30%">

<table style='width: 100%'>
    <tr>
        <td><b>Vendedor: <label id="name_seller"></label></b>
        </td>
        <td><b>Tel: <label id="cell_seller"></label></b></td>
    </tr>
</table>

        </td>
        <td style='width: 10%; '></td>
        <td style='width: 60%; '>
<img src='./public/img/logo.jpg' style='width: 47%;' >
        </td>
    </tr>
</table>




    </div>

            <footer><img src='./public/img/footer.jpg'  style="width: 100%;"></footer>

         


      </div>

      


    </div>

      <div class="pagebreak"> </div>

<div class='container'>
    <div class='container-fluid' style='background-color: white;'>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>

<center>
<b>TÉRMINOS Y CONDICIONES</b>
</center>
<br><br>

<b>•   Instalación: </b>
<br>
<br>

Los equipos o partes de repuestos vendidos por “INSTRUQUIMICA S.A.DE C.V.” incluyen instalación sin cargo adicional, salvo que se exprese lo contrario en esta cotización. El equipo o partes de repuestos deberán instalarse dentro de los 15 días calendario posteriores a la entrega física en su almacén. En caso de no instalarse dentro del periodo establecido por razones ajenas a nuestra empresa el equipo o repuestos se considerarán como aceptado de conformidad, la garantía iniciará su vigencia; así como, el plazo establecido para realizar el pago. El “Cliente” es responsable del suministro de energía eléctrica, el sitio de instalación y las condiciones ambientales requeridas por el equipo y que están descritas en la Guía de Preinstalación provista.
<br>
<br>
<b>•   Capacitación y Soporte en aplicaciones: </b>
<br>
<br>
Los equipos vendidos por “INSTRUQUIMICA” incluyen capacitación y soporte en aplicaciones sin cargo adicional, salvo que se exprese lo contrario en esta cotización.
<br>
<br>
<b>•   Aceptación: </b>
<br>
<br>

Al momento de la firma de aceptación de esta propuesta o envío de orden de compra o contrato, se asume que acepta y se obliga a cumplir con los términos y condiciones aquí citados.
<br>
<br>

<b>•   Condiciones de precio:</b>
<br>
<br>

La cancelación de la orden de compra tiene penalización del 10% del monto de la misma.
*EN EL EXTRANJERO: Para transferencias bancarias internacional el cliente debe agregar $30 en concepto de comisión bancaria. Los precios no incluyen ningún tipo de impuesto ni retenciones.
<br>
<br>
<b>•   Entrega de productos y/o servicios:</b>
<br>
<br>
La entrega de los productos fuera del área metropolitana de San Salvador es por compras mínimas de $400.00, de lo contrario el cliente deberá esperar a que se programe visita a la zona.
No se aceptan devoluciones de producto de ningún tipo.
En caso de que exista retraso en las condiciones de pago acordadas, “INSTRUQUIMICA S.A.DE C.V.” se reserva el derecho de detener cualquier entrega, independientemente de que se trate de pedidos diferentes.
Si las condiciones de instalación, capacitación, desarrollo de método o cualquier condición acordada y confirmada no es cumplida por el “Cliente”, no será motivo para el retraso del pago total de esta oferta.
En caso de compra de servicios de mantenimiento preventivo, calibración, calificación y/o ajuste y al equipo no se le pueda ejecutar el servicio debido a fallas en este, se presentará una nueva cotización por el servicio de reparación. En el caso que el cliente decida no repararlo deberá pagar el diagnostico por el mismo valor del servicio aprobado previamente.
<br>
<br>
<b>•   Garantía:</b>
<br>
<br>

La Garantía cubre desperfectos de fábrica y no incluye daños por mal uso, consumibles, daños por variaciones de voltaje, caídas, golpes o cualquier uso diferente al destinado normalmente. 
La garantía se pierde si el equipo es manipulado por personal técnico diferente a nuestro Departamento de Soporte técnico.
Antes de la aceptación de esta propuesta firmada, orden de compra o contrato por parte de “INSTRUQUIMICA S.A.DE C.V.”, el tipo y fechas de pago deben ser negociados, acordados y definidos por escrito en la cotización y aprobados en la confirmación de pedido.  

</div>
 
</div>
</div>




<script type="text/javascript">

    $("ul#quotation").siblings('a').attr('aria-expanded','true');
    $("ul#quotation").addClass("show");
    $("ul#quotation #quotation-list-menu").addClass("active");
    var all_permission = <?php echo json_encode($all_permission) ?>;
    var quotation_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $("tr.quotation-link td:not(:first-child, :last-child)").on("click", function(){
        var quotation = $(this).parent().data('quotation');
        credit_days=quotation[25]
        method_pay=quotation[26]
        delivery_time=quotation[27]
        validity=quotation[28]
        attention=quotation[29]


        tittle=quotation[30]
        name_seller=quotation[3]
        cell_seller=quotation[32]
        imagen_firma=quotation[33]
       
        $("#firma").html("<img src='public/images/biller/"+imagen_firma+"'>")

        //alert(tittle)
        //attention=quotation[29]
        ///attention=quotation[29]



        $("#credit_days").text(credit_days)
        $("#method_pay").text(method_pay)
        $("#delivery_time").text(delivery_time)
        $("#validity").text(validity)
        $("#attention").text(attention)

        $("#tittle").text(tittle)
        $("#name_seller").text(name_seller)
        $("#cell_seller").text(cell_seller)

         $("#notas").text(quotation[22])
        



        /*Set values to label*/
  

        quotationDetails(quotation);
    });

    $(".view").on("click", function(){
        var quotation = $(this).parent().parent().parent().parent().parent().data('quotation');
       // alert(quotation)
        quotationDetails(quotation);
    });


function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}



    $("#print-btn").on("click", function(){
       
         // sleep(3000)

       // alert("as")
          var divToPrint=document.getElementById('quotation-details');
          var newWin=window.open('','Print-Window');

          var html2= '<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrapImprimir.css') ?>" type="text/css"><style type="text/css"> .modal-dialog { max-width: 100%; } }   @page {size: letter;margin: .5in;}@media print {table.paging thead td, table.paging tfoot td {height: .30in; top:10; }  }header, footer {width: 100%; height: .5in; }header {position: absolute;top: 10; width:100%; }@media print {header, footer {position: fixed; }footer {bottom: 70;} header {text-align: center;} footer {text-align: center;} table.paging{padding-top: 100px;  margin:0 auto;} } @media print { table th.fondoAzul { background-color: #red !important; webkit-print-color-adjust: exact; } .pagebreak { page-break-before: always; } }</style>'
         // alert(html2)
         var html2 =html2+ '<body onload="window.print()">'+divToPrint.innerHTML+'</body>'


          newWin.document.open();
          

          newWin.document.write(html2);
          //newWin.document.write();

          newWin.document.close();
          setTimeout(function(){newWin.close();},1000);


          /*
                var divContents = document.getElementById("quotation-details").innerHTML; 
        var a = window.open('', 'Print-Window'); 
        a.document.write('<link rel="stylesheet" href="<?php echo asset('public/vendor/bootstrap/css/bootstrapImprimir.css') ?>" type="text/css"><style type="text/css"> .modal-dialog { max-width: 100%; } }   @page {size: letter;margin: .5in;}@media print {table.paging thead td, table.paging tfoot td {height: .30in; top:10; }  }header, footer {width: 100%; height: .5in; }header {position: absolute;top: 0; width:100%; }@media print {header, footer {position: fixed; }footer {bottom: 50;} header {text-align: center;} footer {text-align: center;} table.paging{padding-top: 100px;  margin:0 auto;} } @media print { table th.fondoAzul { background-color: #red !important; webkit-print-color-adjust: exact; } .pagebreak { page-break-before: always; } }</style>'); 
     

        a.document.write('<body>'); 
        a.document.write(divContents); 
        a.document.write('</body>'); 
        a.document.close(); 
        a.print(); 
*/




    });

    $('#quotation-table').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ {{trans("file.records per page")}}',
             "info":      '<small>{{trans("file.Showing")}} _START_ - _END_ (_TOTAL_)</small>',
            "search":  '{{trans("file.Search")}}',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 8]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '{{trans("file.PDF")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '{{trans("file.CSV")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '{{trans("file.Print")}}',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible'
                },
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                text: '{{trans("file.delete")}}',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        quotation_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                var quotation = $(this).closest('tr').data('quotation');
                                quotation_id[i-1] = quotation[13];
                            }
                        });
                        if(quotation_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'quotations/deletebyselection',
                                data:{
                                    quotationIdArray: quotation_id
                                },
                                success:function(data){
                                    alert(data);
                                    dt.rows({ page: 'current', selected: true }).remove().draw(false);
                                }
                            });
                            
                        }
                        else if(!quotation_id.length)
                            alert('Nothing is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '{{trans("file.Column visibility")}}',
                columns: ':gt(0)'
            },
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
        }
    }

    if(all_permission.indexOf("quotes-delete") == -1)
        $('.buttons-delete').addClass('d-none');

    function quotationDetails(quotation){
        $('input[name="quotation_id"]').val(quotation[13]);
 
 tablita_datos= "<div class='container'><table style='width: 100%;'><tr><td style='width: 60% !important'><table border=1 STYLE='width:100%;'><tr><td class='fondoAzul' STYLE='width:25%;'>CLIENTE:</td><td STYLE='width:65%;'>"+quotation[9]+"</td></tr><tr><td class='fondoAzul'>ATENCIÓN:</td><td>"+quotation[29]+"</td></tr></table></td><td style='width: 40% !important'><table border=1 style='width: 35%; MARGIN: 0 AUTO;'><tr><td class='fondoAzul'>FECHA</td></tr><tr><td >"+quotation[0]+"</td></tr><tr><td class='fondoAzul'>COTIZACION</td></tr><tr><td >"+quotation[1]+"</td></tr></table></td></tr></table></div>"

//alert(tablita_datos)
        var htmltext = tablita_datos;


        /*'<strong>{{trans("file.Date")}}: </strong>'+quotation[0]+'<br><strong>{{trans("file.reference")}}: </strong>'+quotation[1]+'<br><strong>{{trans("file.Status")}}: </strong>'+quotation[2]+'<br><br><div class="row"><div class="col-md-6"><strong>{{trans("file.From")}}:</strong><br>'+quotation[3]+'<br>'+quotation[4]+'<br>'+quotation[5]+'<br>'+quotation[6]+'<br>'+quotation[7]+'<br>'+quotation[8]+'</div><div class="col-md-6"><div class="float-right"><strong>{{trans("file.To")}}:</strong><br>'+quotation[9]+'<br>'+quotation[10]+'<br>'+quotation[11]+'<br>'+quotation[12]+'</div></div></div>';

        */
        $.get('quotations/product_quotation/' + quotation[13], function(data){
            $(".product-quotation-list tbody").remove();
            var name_code = data[0];
            var qty = data[1];
            var unit_code = data[2];
            var tax = data[3];
            var tax_rate = data[4];
            var discount = data[5];
            var subtotal = data[6];
             
             

            var newBody = $("<tbody>");

            subtotal_suma=0;

            $.each(name_code, function(index){
         var newRow = $("<tr>");
                var cols = '';
                cols += '<td><strong>' + (index+1) + '</strong></td>';
                 cols += '<td>' + qty[index] + ' ' + unit_code[index] + '</td>';
                 cols += '<td>' + name_code[index] + '</td>';
                
               
               
                cols += '<td  align=right>' + parseFloat(subtotal[index] / qty[index]).toFixed(2) + '</td>';
                /*cols += '<td>' + tax[index] + '(' + tax_rate[index] + '%)' + '</td>';
                cols += '<td>' + discount[index] + '</td>';
                */
                cols += '<td  align=right>' + subtotal[index] + '</td>';
                newRow.append(cols);
                newBody.append(newRow);

                 subtotal_suma+=subtotal[index];


            });

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=4 style="text-align:right;"><strong>Sub-Total:</strong></td>';
            cols += '<td align=right> '+subtotal_suma.toFixed(2)+'</td>';
           
            newRow.append(cols);
            newBody.append(newRow);
            var iva = subtotal_suma*0.13;

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=4 style="text-align:right;"><strong>IVA 13%:</strong></td>';
            cols += '<td  align=right>'+iva.toFixed(2)+'</td>';
            newRow.append(cols);
            newBody.append(newRow);

            var newRow = $("<tr>");
            cols = '';
            cols += '<td colspan=4 style="text-align:right;"><strong>TOTAL US$:</strong></td>';
            cols += '<td align=right>' + (iva+subtotal_suma).toFixed(2) + '</td>';
           
           document.getElementById("total").innerHTML=  NumeroALetras(iva+subtotal_suma)


            newRow.append(cols);
            newBody.append(newRow);

            
          
            //alert(newRow)

            $("table.product-quotation-list").append(newBody);
        });
        var htmlfooter = '<p><strong>{{trans("file.Note")}}:</strong> '+quotation[22]+'</p><strong>{{trans("file.Created By")}}:</strong><br>'+quotation[23]+'<br>'+quotation[24];
        $('#quotation-content').html("<table class=paging style='width:100%;'><thead><tr><td></td></tr></thead><tbody><tr><td><div class='container'>"+htmltext+"</div></td></tr></tbody><tfoot><tr><td>&nbsp;</td></tr></tfoot></table>");
        $('#quotation-footer').html(htmlfooter);
        $('#quotation-details').modal('show');
    }
</script>
@endsection
