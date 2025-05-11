

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2>Consulta de Ventas por Rango de Fecha</h2>
    
    <!-- Formulario para seleccionar el rango de fecha -->
    <form action="<?php echo e(route('sales.byDate')); ?>" method="GET" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="start_date" class="mr-2">Fecha Inicio:</label>
            <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo e($startDate ?? ''); ?>">
        </div>
        <div class="form-group mr-2">
            <label for="end_date" class="mr-2">Fecha Fin:</label>
            <input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo e($endDate ?? ''); ?>">
        </div>
        <button type="submit" class="btn btn-primary mr-2">Consultar</button>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Imprimir</button>
    </form>

    <?php if($sales->count() > 0): ?>
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
                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($sale->id); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($sale->created_at)->format('d/m/Y')); ?></td>
                    <td><?php echo e($sale->reference_no); ?></td>
                    <td><?php echo e($sale->duca); ?></td>
                    <td><?php echo e($sale->biller->name ?? ''); ?></td>
                    <td><?php echo e($sale->customer->name ?? ''); ?></td>
                    <td>
                        <?php if($sale->sale_status == 1): ?>
                            <span class="badge badge-success"><?php echo e(trans('file.Completed')); ?></span>
                        <?php elseif($sale->sale_status == 2): ?>
                            <span class="badge badge-danger"><?php echo e(trans('file.Pending')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-warning"><?php echo e(trans('file.Draft')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($sale->payment_status == 1): ?>
                            <span class="badge badge-danger"><?php echo e(trans('file.Pending')); ?></span>
                        <?php elseif($sale->payment_status == 2): ?>
                            <span class="badge badge-danger"><?php echo e(trans('file.Due')); ?></span>
                        <?php elseif($sale->payment_status == 3): ?>
                            <span class="badge badge-warning"><?php echo e(trans('file.Partial')); ?></span>
                        <?php else: ?>
                            <span class="badge badge-success"><?php echo e(trans('file.Paid')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e(number_format($sale->total_tax, 2)); ?></td>
                    <td><?php echo e(number_format($sale->total_price, 2)); ?></td>
                    <td><?php echo e(number_format($sale->grand_total, 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- Fila de totales -->
                <tr>
                    <th colspan="8" class="text-right">Totales:</th>
                    <th><?php echo e(number_format($totalTax, 2)); ?></th>
                    <th><?php echo e(number_format($totalSubTotal, 2)); ?></th>
                    <th><?php echo e(number_format($totalGrandTotal, 2)); ?></th>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <?php if($startDate || $endDate): ?>
            <p>No se encontraron ventas para el rango de fecha seleccionado.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/tramites/public_html/auto/resources/views/sale/date_range.blade.php ENDPATH**/ ?>