@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Consulta de Ventas por Rango de Fecha</h2>
    
    <!-- Formulario para seleccionar el rango de fecha -->
    <form action="{{ route('sales.byDate') }}" method="GET" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="start_date" class="mr-2">Fecha Inicio:</label>
            <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate ?? '' }}">
        </div>
        <div class="form-group mr-2">
            <label for="end_date" class="mr-2">Fecha Fin:</label>
            <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate ?? '' }}">
        </div>
        <button type="submit" class="btn btn-primary mr-2">Consultar</button>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </form>

    @if($sales->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Referencia</th>
                    <th>DUCA</th>
                    <th>Biller</th>
                    <th>Customer</th>
                    <th>Estado de Venta</th>
                    <th>Estado de Pago</th>
                    <th>IVA</th>
                    <th>SUB TOTAL</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $sale->reference_no }}</td>
                    <td>{{ $sale->duca }}</td>
                    <td>{{ $sale->biller->name ?? '' }}</td>
                    <td>{{ $sale->customer->name ?? '' }}</td>
                    <td>
                        @if($sale->sale_status == 1)
                            <span class="badge badge-success">{{ trans('file.Completed') }}</span>
                        @elseif($sale->sale_status == 2)
                            <span class="badge badge-danger">{{ trans('file.Pending') }}</span>
                        @else
                            <span class="badge badge-warning">{{ trans('file.Draft') }}</span>
                        @endif
                    </td>
                    <td>
                        @if($sale->payment_status == 1)
                            <span class="badge badge-danger">{{ trans('file.Pending') }}</span>
                        @elseif($sale->payment_status == 2)
                            <span class="badge badge-danger">{{ trans('file.Due') }}</span>
                        @elseif($sale->payment_status == 3)
                            <span class="badge badge-warning">{{ trans('file.Partial') }}</span>
                        @else
                            <span class="badge badge-success">{{ trans('file.Paid') }}</span>
                        @endif
                    </td>
                    <td>{{ number_format($sale->total_tax, 2) }}</td>
                    <td>{{ number_format($sale->total_price, 2) }}</td>
                    <td>{{ number_format($sale->grand_total, 2) }}</td>
                </tr>
                @endforeach

                <!-- Fila de totales -->
                <tr>
                    <th colspan="8" class="text-right">Totales:</th>
                    <th>{{ number_format($totalTax, 2) }}</th>
                    <th>{{ number_format($totalSubTotal, 2) }}</th>
                    <th>{{ number_format($totalGrandTotal, 2) }}</th>
                </tr>
            </tbody>
        </table>
    @else
        @if($startDate || $endDate)
            <p>No se encontraron ventas para el rango de fecha seleccionado.</p>
        @endif
    @endif
</div>
@endsection
