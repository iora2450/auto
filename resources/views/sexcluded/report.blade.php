@extends('layout.main')

@section('content')
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
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('sexcluded.report') }}" method="GET" id="reporte-section">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            @include('sexcluded.form')
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
                            Seleccione las fechas
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

        <form action="{{ route('sexcluded.excel') }}" method="GET" id="excel-section">
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
                                <option value="1">Excluidos</option>
                                <option value="2">Retenciones</option>
                            </select>
                        </div>
                        <div class="card-body">
                            @include('sexcluded.form')
                            <div class="form-actions" id="button-excel-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> {{ __('Export') }}</button>
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
                            <h2 class="">PSA</h2><br/>
                            <h3 class="">{{trans('file.Suject Excluid Report')}}</h3><br/>
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
                                <th>Número Venta</th>
                                <th>Cliente</th>
                                <th>Tipo de identificación</th>
                                <th class="text-center" colspan="1">Sub-total</th>
                                <th class="text-center" colspan="1">Impuesto</th>
                                <th class="text-center" colspan="1">Exento</th>
                                <th class="text-center" colspan="1">Percepción</th>
                                <th class="text-center" colspan="1">Total (USD$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $stotal=0 @endphp
                            @php $sniva=0 @endphp
                            @foreach($sales as $comp)
                            @php $stotal += $comp->subtotal @endphp
                            @php $sniva += $comp->impuesto @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($comp->created_at)->format('d/m/Y') }}</td>
                                @if($comp->tercero=="off")
                                    <td>{{$comp->reference_no}}</td>
                                @else
                                    <td class="text-danger">{{$comp->reference_no}}</td>
                                @endif    
                                @if($comp->status=="Anulado")
                                    <td>A N U L A D O</td>
                                @else
                                    <td>{{$comp->cliente}}</td>
                                @endif
                                @if($comp->document_id==1)
                                    <td>CF</td>
                                @elseif($comp->identification_type=="04")
                                    <td>FA</td>
                                @else
                                    <td>NC</td>
                                @endif
                                <td class="text-right" colspan="1">${{number_format($comp->subtotal,2)}}</td>
                                <td class="text-right" colspan="1">${{number_format($comp->impuesto,2)}}</td>
                                <td class="text-right" colspan="1">${{number_format($comp->exento,2)}}</td>
                                <td class="text-right" colspan="1">${{number_format($comp->percepcion,2)}}</td>
                                @if($comp->identification_type == "1")
                                    <td class="text-right" colspan="1">${{number_format($comp->exento,2)}}</td>
                                @else
                                    <td class="text-right" colspan="1">${{number_format($comp->total,2)}}</td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>SUMA:</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right" colspan="1">${{ number_format($stotal,2) }}</td>
                                <td class="text-right" colspan="1">${{ number_format($sniva,2) }}</td>
                                <td class="text-right">${{ number_format(0.00,2) }}</td>
                                <td class="text-right">${{ number_format(0.00,2) }}</td>
                                <td class="text-right" colspan="1">${{ number_format($sexento,2) }}</td>
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
        $("#excel-section").hide();
        $("#libro-consumidor").hide();
        $("#libro-contribuyente").hide();

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
            }else{
                $("#reporte-section").show();
                $("#form-section").show();
                $("#libro-section").hide();
                $("#excel-section").hide();
                $("#libro-consumidor").hide();
                $("#libro-contribuyente").hide();
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

    </script>
@endsection
@endsection
