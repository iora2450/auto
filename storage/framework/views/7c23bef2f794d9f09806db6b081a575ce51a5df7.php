<div class="search-invoices">
  
    <form action="<?php echo e(route('sale.search_invoices')); ?>" method="GET">
        <!-- Puedes agregar campos de búsqueda aquí si los necesitas -->

    </form>

    <?php if($invoices ?? false): ?>
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
            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $totalSum += $invoice->total_price; ?>
                <tr>
                    <td><?php echo e($invoice->numerocontrol); ?></td>
                    <td><?php echo e($invoice->customer->codgeneracion); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($invoice->customer->created_at)->format('d/m/Y')); ?></td>
                    <td><?php echo e($invoice->total_price); ?></td>
                    <td>
                        <button class="btn btn-primary select-button" data-reference="<?php echo e($invoice->reference_no); ?>" data-numerocontrol="<?php echo e($invoice->numerocontrol); ?>" data-fechasale="<?php echo e($invoice->created_at); ?>">Seleccionar</button>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <p>Total Sum: <?php echo e($totalSum); ?></p>
<?php else: ?>
    <p>No se encontraron datos.</p>
<?php endif; ?>

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

</script><?php /**PATH /home/ltygkxsm/public_html/mrjbnew/resources/views/sale/search_invoices.blade.php ENDPATH**/ ?>