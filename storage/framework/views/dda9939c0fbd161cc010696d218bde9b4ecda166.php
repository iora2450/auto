



<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Anulación de DTE</h1>
    <form action="<?php echo e(route('sale.anulardtemh', ['id' => $sale->id])); ?>" method="POST">
        <?php echo csrf_field(); ?> <!-- Protección contra CSRF -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Referencia</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Total iva</th>
                    <th>Código Generación</th>
                    <th>Sello Anular</th>
                    <th>Estado anular</th>

                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo e($sale->date); ?></td>
                    <td><?php echo e($sale->reference_no); ?></td>
                    <td><?php echo e($sale->customer->name); ?></td>
                    <td><?php echo e(number_format($sale->grand_total, 2)); ?></td>
                    <td><?php echo e(number_format($sale->total_tax, 2)); ?></td>
                    <td><?php echo e($sale->codgeneracionanular); ?></td>
                    <td><?php echo e($sale->selloanular); ?></td>
                    <td><?php echo e($sale->estadodteanular); ?></td>
                    <td>
                        <button type="submit" class="btn btn-danger">Anular DTE</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="form-group">
            <label for="duiSolicitante">DUI del Solicitante:</label>
            <input type="text" name="duiSolicitante" id="duiSolicitante" required class="form-control">
        </div>
        <div class="form-group">
            <label for="nombreSolicitante">Nombre del Solicitante:</label>
            <input type="text" name="nombreSolicitante" id="nombreSolicitante" required class="form-control">
        </div>
        <div class="form-group">
            <label for="duiEjecutor">DUI del Ejecutor:</label>
            <input type="text" name="duiEjecutor" id="duiEjecutor" required class="form-control">
        </div>
        <div class="form-group">
            <label for="nombreEjecutor">Nombre del Ejecutor:</label>
            <input type="text" name="nombreEjecutor" id="nombreEjecutor" required class="form-control">
        </div>
        <div class="form-group">
            <label for="motivo">Motivo de la Anulación:</label>
            <textarea name="motivo" id="motivo" required class="form-control"></textarea>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/tramites/public_html/auto/resources/views/sale/anulardte.blade.php ENDPATH**/ ?>