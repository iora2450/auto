<div class="search-invoices">
  
    <form action="{{ route('sale.search_invoices') }}" method="GET">
        <!-- Puedes agregar campos de búsqueda aquí si los necesitas -->

    </form>

    @if ($invoices ?? false)
    <table class="table">
        <thead>
            <tr>
                <th>Número de Control</th>       
                <th>Nombre del Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th></th> <!-- Columna para el botón -->
            </tr>
        </thead>
        <tbody>
            <?php $totalSum = 0; ?>
            @foreach ($invoices as $invoice)
                <?php $totalSum += $invoice->total_price; ?>
                <tr>
                    <td>{{ $invoice->numerocontrol }}</td>
                    <td>{{ $invoice->customer->codgeneracion }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->customer->created_at)->format('d/m/Y') }}</td>
                    <td>{{ $invoice->total_price }}</td>
                    <td>
                        <button class="btn btn-primary select-button" data-reference="{{ $invoice->reference_no }}" data-numerocontrol="{{ $invoice->numerocontrol }}" data-fechasale="{{ $invoice->created_at }}">Seleccionar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Total Sum: {{ $totalSum }}</p>
@else
    <p>No se encontraron datos.</p>
@endif

</div>

<script> 
     
    $(document).ready(function () {
        $(".select-button").click(function () {
  
            var reference = $(this).data("reference");
            var numeroControl = $(this).data("numerocontrol");
            var fechasale = $(this).data("fechasale");

            // Actualiza el valor del campo de texto en la vista principal
           // alert(numeroControl);
            $("#numeroControlInput").val(numeroControl);
            $("#dateSale").val(fechasale);

            // Cierra la modal
            $("#searchInvoicesModal").modal("hide");
        });
    });

</script>