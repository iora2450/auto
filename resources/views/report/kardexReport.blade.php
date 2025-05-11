@extends('layout.main') @section('content')
@if(empty($datos))
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{'No Data exist between this date range!'}}</div>
@endif
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Reporte de kardex</h3>
            </div>
            {!! Form::open(['route' => 'report.kardexReport', 'method' => 'get']) !!}
            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>Fecha inicio:</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input name='fecha_inicio' type="date" value='{{$start_date}}' required />
                                
                            </div>
                        </div>
                    </div>
                </div>

              <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>Fecha final</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input name='fecha_final' type="date" value='{{$end_date}}' required />
                            </div>
                        </div>
                    </div>
                </div>


        <div class="col-md-12 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>Articulo</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                 <select name='articulo' id='articulo' class="selectpicker" data-show-subtext="true" data-live-search="true">
                                  <option value="">Seleccione un articulo</option>
                                  @foreach($datos_articulos as $key)
                                  <option value='{{$key->id}}'>{{$key->name}}</option>
                                  @endforeach
                              </select>
                               
                            </div>
                        </div>
                    </div>
                </div>

        

               
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <input name="b_print" type="button" class="ipt" onClick="printdiv('div_print');" value=" Print ">


    <div class="table-responsive mb-4" id="div_print">
         @if(count($datos)>0) 
<center>
        <b>MR. J&B INVERSIONES DEL CIELO, S.A. DE C.V.</b><br><b>Registro de control de inventario</b><br>Del {{$start_date}} al  {{$end_date}} 

        <br>
        <div id='izq' style='float:left;'>
            <b>Descripcion del producto:</b>{{$datos[0]->name}} <br>
            <b>Codigo del producto:    </b>  {{$datos[0]->code}}<br>
            <b>Unidad de medida:         </b> {{$datos[0]->unit_name}}<br> 
        </div>


        <div id='der' style='float:right;'>
            <b>NIT:</b>0614-011022-104-3<br>
            <b>NRC:</b>320113-6
        </div>
    </center>
     @endif

<br>

        <table id="report-table" class="table" style='line-height: 1.1;'>
            <thead>
                            <tr>
                   
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1 colspan="6" STYLE='text-align:center;'>MOVIMIENTOS</th>
                    

                     <th></th>


                </tr>
                     <tr>
                   
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1></th>
                    <th border=1 ></th>
                    <th border=1>UNIDADES</th>
                    <th border=1></th>

                    <th border=1></th>
                    <th border=1>COSTOS</th>
                    <th border=1></th>

                     <th></th>


                </tr>

                <tr>
                   
                    <th border=1>No</th>
                    <th border=1>Fecha</th>
                    <th >Numero de <br>Documento</th>
                    <th border=1>Nombre Proveedor</th>
                    <th border=1>Nacionalidad proveedor</th>
                    <th border=1>Referencia libro de compras<br> locales o retaceo</th>
                    <th border=1>Lote</th>
                    <th border=1>Concepto</th>
                    <th border=1>Costo Unitario</th>
                    <th border=1 >Entrada</th>
                    <th border=1>Salida</th>
                    <th border=1>Saldo</th>

                    <th border=1>Entrada</th>
                    <th border=1>Salida</th>
                    <th border=1>Saldo</th>

                     <th>Costo unitario promedio</th>


                </tr>
            </thead>
            <tbody>
                 <tr>
                <td></td>
                <td></td>
                <td></td>
                 <td></td>
                <td></td>
                <td></td>
                 <td></td>
                  <td></td>
                <td></td>
                <td><b>Saldo Inicial</b></td>
                <td></td>
                <td  style='text-align: right;'><b>{{$datos_iniciales[0]->saldo}}</b></td>
                <td></td>
                 <td></td>
                  <td  style='text-align: right;'><b>{{$datos_iniciales[0]->saldoc}}</b></td>
                  <td  style='text-align: right;'><b>{{$datos_iniciales[0]->costo_unitario_promedio}}</b></td>
       

               </tr>

                  
                  @foreach($datos as $key) 

               <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$key->fecha}}</td>
                <td>{{$key->numero_documento}}</td>
                 <td>{{$key->nombreproveedor}}</td>
                <td>{{$key->nacionalidadproveedor}}</td>
                <td>{{$key->referencia}}</td>
                 <td>{{$key->lote}}</td>
                  <td>{{$key->concepto}}</td>
                <td style='text-align: right;'>{{$key->cost}}</td>
                <td style='text-align: right;'>{{$key->entrada}}</td>
                <td style='text-align: right;'>{{$key->salida}}</td>
                <td style='text-align: right;'>{{round($key->saldo,2)}}</td>
                <td style='text-align: right;'>{{$key->entradac}}</td>
                 <td style='text-align: right;'>{{$key->salidac}}</td>
                  <td style='text-align: right;'>{{$key->saldoc}}</td>
                   <td style='text-align: right;'>{{$key->costounitariopromedio}}</td>
       

               </tr>
              
                @endforeach
            </tbody>
            <tfoot>



            </tfoot>
        </table>
    </div>
</section>

<script type="text/javascript">

    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #product-report-menu").addClass("active");

    $('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
    $('.selectpicker').selectpicker('refresh');


   function printdiv(printpage) {
            var headstr = "<html><head><title></title></head><body>" + ' <style>body{background-color:white !important;}@page { size: landscape; }  #izq{float:left; #der{float:right; text-align:right;   }  }</style></head>';
            var footstr = "</body>";
            var newstr = document.all.item(printpage).innerHTML;
            var oldstr = document.body.innerHTML;
            document.body.innerHTML = headstr + newstr + footstr;
            window.print();
            document.body.innerHTML = oldstr;
            return false;
        }

    $('#report-table1').DataTable( {
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
       
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '{{trans("file.PDF")}}',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                 text:      '<i class="fa fa-file-pdf-o">PDF</i> ',
                title: "",
                titleAttr: 'Exportar a PDF',
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
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
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
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
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '{{trans("file.Column visibility")}}',
                columns: ':gt(0)'
            }
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

 

$(".daterangepicker-field").daterangepicker({
  callback: function(startDate, endDate, period){
    var start_date = startDate.format('YYYY-MM-DD');
    var end_date = endDate.format('YYYY-MM-DD');
    var title = start_date + ' To ' + end_date;
    $(this).val(title);
    $('input[name="start_date"]').val(start_date);
    $('input[name="end_date"]').val(end_date);
  }
});

</script>
@endsection