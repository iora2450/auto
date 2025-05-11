@extends('layout.main')

@section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<div class="container-fluid">
    <main class="main mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="class_document">{{ __('Class document') }}</label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="class_document" id="class_document" required>
                            <option value="0" disabled>{{ __('Select') }}</option>
                            <option value="1">Reporte</option>
                            <option value="2">Libro</option>
                            <option value="3">Excel</option>
                            <option value="4">Descargas PDF</option>
                            <option value="5">Descargas JSON</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.report') }}" method="GET" id="reporte-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="boton-reporte-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('View') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row" id="libro-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="class_report">{{ __('Class report') }}</label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="class_report" id="class_report" required>
                            <option value="0" enabled>{{ __('Select') }}</option>
                            <option value="1">Contribuyente</option>
                            <option value="2">Consumidor</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.libro') }}" method="GET" id="libro-contribuyente">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-libro-section">
                                <button type="submit" class="btn btn-primary" target="_blank"><i class="fa fa-save fa-1x"></i> {{ __('Print') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

         <form action="{{ route('sales.report') }}" method="GET" id="libro-consumidor">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de FACturas
                        </div>
                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-libro-section">
                                <button type="submit" class="btn btn-primary" target="_blank"><i class="fa fa-save fa-1x"></i> {{ __('Consu') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('sales.excel') }}" method="GET" id="excel-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas y tipo de libro a exportar
                        </div>
                         <div class="card-body col-md-3">
                            <label>{{ __('Type report') }}</label>
                            <select class="form-control" name="type_libro" id="type_libro" required>
                                <option value="0" disabled>{{ __('Select') }}</option>
                                <option value="1">Contribuyentes</option>
                                <option value="2">Consumidor Final</option>
                            </select>
                        </div>
                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-excel-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Export') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row" id="pdf-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="pdf_report">{{ __('PDF report') }}</label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="pdf_report" id="pdf_report" required>
                            <option value="0" enabled>{{ __('Select') }}</option>
                            <option value="1">Creditos Fiscales</option>
                            <option value="2">Consumidor Final</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.pdfreport') }}" method="GET" id="ccf-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los CCF a descargar
                        </div>

                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-ccf-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Export') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('sales.pdfreportFac') }}" method="GET" id="fac-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los FACTURAS a descargar
                        </div>

                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-fac-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Export') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

         <div class="row" id="json-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="json_report">{{ __('JSON report') }}</label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="json_report" id="json_report" required>
                            <option value="0" enabled>{{ __('Select') }}</option>
                            <option value="1">Creditos Fiscales</option>
                            <option value="2">Consumidor Final</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('sales.download-json-ccf') }}" method="GET" id="jsonccf-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los JSON de ccf a descargar
                        </div>

                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-jsonccf-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Download Zip') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="{{ route('sales.download-json-fac') }}" method="GET" id="jsonfac-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los JSON de facturas a descargar
                        </div>

                        <div class="card-body">
                            @include('sale.form')
                            <div class="form-actions" id="button-jsonfac-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Download Zip') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>


        <div id="form-section" class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h2 class="">Tramites y negocios</h2><br/>
                            <h3 class="">{{trans('file.Sale Report')}}</h3><br/>
                        </div>
                        <div class="col-6">
                            <b>Fecha de Consulta</b> {{ \Carbon\Carbon::now()->format('d-m-Y') }}
                            <br>
                            <b>Cantidad Registros</b> {{ $sales->count() }}
                            <br>
                            <b>Total Ventas </b>$ {{ number_format($sumaTotal,2) }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
    <table class="table table-bordered table-striped table-sm">
    <thead>
    <tr class="bg-primary text-white">
        <th>Fecha Venta</th>
        <th>Número Control</th>
        <th>Código de Generación</th>
        <th>Sello</th>
        <th>Número Venta</th>
        <th>NRC</th>
        <th>NIT</th>
        <th>Cliente</th>
        <th>Tipo de Identificación</th>
        <th class="text-center">Sub-total</th>
        <th class="text-center">Impuesto</th>
        <th class="text-center">Exento</th>
        <th class="text-center">Percepción</th>
        <th class="text-center">Servicio de Transporte</th>
        <!-- Nueva columna para el total de servicios de aduana -->
        <th class="text-center">Servicios de Aduana</th>
        <!-- Nueva columna para gastos por cuenta del cliente -->
        <th class="text-center">Gastos por cuenta del cliente</th>
        <th class="text-center">Total (USD$)</th>
    </tr>
</thead>


        <tbody>
        @php 
    $stotal = 0;
    $sniva = 0;
    $sexentoTotal = 0;
    $spercepcionTotal = 0;
    $sTransporteTotal = 0;
    $sAduanaTotal = 0;
    $sGastosCuentaCliente = 0;
@endphp
@foreach($sales as $comp)
    @php 
        $stotal += $comp->subtotal;
        $sniva += $comp->impuesto;
        $sexentoTotal += $comp->total_exenta;
        $spercepcionTotal += $comp->total_price;
        $sTransporteTotal += $comp->servicio_trasporte;
        $sAduanaTotal += $comp->servicio_aduana;
        $sGastosCuentaCliente += $comp->gastos_por_cuenta_cliente;
    @endphp
    <tr>
        <td>{{ \Carbon\Carbon::parse($comp->created_at)->format('d/m/Y') }}</td>
        <td>{{ $comp->numerocontrol }}</td>
        <td>{{ $comp->codgeneracion }}</td>
        <td>{{ $comp->sello }}</td>
        @if($comp->tercero == "off")
            <td>{{ $comp->reference_no }}</td>
        @else
            <td class="text-danger">{{ $comp->reference_no }}</td>
        @endif
        <td>{{ $comp->tax_no }}</td>
        <td>{{ $comp->nit }}</td>
        @if($comp->status == "Anulado")
            <td>A N U L A D O</td>
        @else
            <td>{{ $comp->cliente }}</td>
        @endif
        @if($comp->document_id == 1)
            <td>CF</td>
        @elseif($comp->document_id == 2)
            <td>FA</td>
        @else
            <td>NC</td>
        @endif
        <!-- Mostrar subtotal sin la parte exenta -->
        <td class="text-right">${{ number_format($comp->subtotal - $comp->total_exenta, 2) }}</td>
        <td class="text-right">${{ number_format($comp->impuesto, 2) }}</td>
        <td class="text-right">${{ number_format($comp->total_exenta, 2) }}</td>
        <!-- Percepción (según lógica actual, se muestra $0.00) -->
        <td class="text-right">$0.00</td>
        <!-- Servicio de Transporte -->
        <td class="text-right">${{ number_format($comp->servicio_trasporte, 2) }}</td>
        <!-- Servicios de Aduana -->
        <td class="text-right">${{ number_format($comp->servicio_aduana, 2) }}</td>
        <!-- Gastos por cuenta del cliente (net_unit_price cuando tax_rate es 0 para producto 705) -->
        <td class="text-right">${{ number_format($comp->gastos_por_cuenta_cliente, 2) }}</td>
        @if($comp->identification_type == "1")
            <td class="text-right">${{ number_format($comp->total_exenta, 2) }}</td>
        @else
            <td class="text-right">${{ number_format($comp->total, 2) }}</td>
        @endif
    </tr>
@endforeach


        </tbody>
        <tfoot>
    <tr>
        <td colspan="9" class="text-right">SUMA:</td>
        <td class="text-right">${{ number_format($stotal, 2) }}</td>
        <td class="text-right">${{ number_format($sniva, 2) }}</td>
        <td class="text-right">${{ number_format($sexentoTotal, 2) }}</td>
        <td class="text-right">$0.00</td>
        <td class="text-right">${{ number_format($sTransporteTotal, 2) }}</td>
        <td class="text-right">${{ number_format($sAduanaTotal, 2) }}</td>
        <td class="text-right">${{ number_format($sGastosCuentaCliente, 2) }}</td>
        <td class="text-right">${{ number_format($stotal + $sniva + $sexentoTotal, 2) }}</td>
    </tr>
</tfoot>

    </table>
</div>


            </div>
        </div>
    </main>
</div>

@section('scripts')
    <script>
        $("#libro-section").hide();
        $("#pdf-section").hide();
        $("#json-section").hide();
        $("#excel-section").hide();
        $("#libro-consumidor").hide();
        $("#libro-contribuyente").hide();
        $("#ccf-section").hide();
        $("#fac-section").hide();
        $("#jsonccf-section").hide();
        $("#jsonfac-section").hide();

        $('#class_document').change(function(){
            var valorCambiado =$(this).val();
            console.log(valorCambiado);
            if(valorCambiado == '3'){
                $("#reporte-section").hide();
                $("#libro-section").hide();
                $("#form-section").hide();
                $("#excel-section").show();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();
            }else if(valorCambiado == '2'){
                $("#reporte-section").hide();
                $("#excel-section").hide();
                $("#form-section").hide();
                $("#libro-section").show();
                $("#pdf-section").hide();    
            }else if(valorCambiado == '3'){
                $("#reporte-section").show();
                $("#form-section").show();
                $("#libro-section").hide();
                $("#excel-section").hide();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();
                $("#pdf-section").hide();    
            }else if(valorCambiado == '4'){
                $("#reporte-section").hide();
                $("#form-section").hide();
                $("#libro-section").hide();
                $("#excel-section").hide();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();    
                $("#pdf-section").show();    
            }else if(valorCambiado == '5'){
                $("#reporte-section").hide();
                $("#form-section").hide();
                $("#libro-section").hide();
                $("#excel-section").hide();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();    
                $("#pdf-section").hide();
                $("#json-section").show();
            }else{
                $("#reporte-section").show();
                $("#form-section").show();
                $("#libro-section").hide();
                $("#excel-section").hide();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();
                $("#pdf-section").hide();
                $("#json-section").hide();    
            }
        });

        $('#class_report').change(function(){
            var valorCambiado =$(this).val();
            console.log(valorCambiado);
            if(valorCambiado == '1'){
                $("#libro-contribuyente").show();
                $("#form-section").hide();
                $("#libro-consumidor").hide();
            }else{
                $("#libro-contribuyente").hide();
                $("#form-section").hide();
                $("#libro-consumidor").show();
            }
        });

        $('#pdf_report').change(function(){
            var valorCambiado =$(this).val();
            console.log(valorCambiado);
            if(valorCambiado == '1'){
                $("#libro-section").hide();
                $("#form-section").hide();
                $("#pdf-section").show();
                $("#ccf-section").show();
                $("#fac-section").hide();
                $("#json-section").hide();
                $("#jsonccf-section").hide();
                $("#jsonfac-section").hide();
            }else{
                $("#libro-section").hide();
                $("#form-section").hide();
                $("#pdf-section").show();;
                $("#ccf-section").hide();
                $("#fac-section").show();
                $("#json-section").hide();
                $("#jsonccf-section").hide();
                $("#jsonfac-section").hide();
            }
        });

        $('#json_report').change(function(){
            var valorCambiado =$(this).val();
            console.log(valorCambiado);
            if(valorCambiado == '1'){
                $("#libro-section").hide();
                $("#form-section").hide();
                $("#pdf-section").hide();
                $("#ccf-section").hide();
                $("#fac-section").hide();
                $("#json-section").show();
                $("#jsonccf-section").show();
                $("#jsonfac-section").hide();
            }else{
                $("#libro-section").hide();
                $("#form-section").hide();
                $("#pdf-section").hide();;
                $("#ccf-section").hide();
                $("#fac-section").hide();
                $("#json-section").show();
                $("#jsonccf-section").hide();
                $("#jsonfac-section").show();
            }
        });

    </script>
@endsection
@endsection
