<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <main class="main mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="class_document"><?php echo e(__('Class document')); ?></label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="class_document" id="class_document" required>
                            <option value="0" disabled><?php echo e(__('Select')); ?></option>
                            <option value="1">Reporte</option>
                            <option value="2">Libro</option>
                            <option value="3">Excel</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('sexcluded.report')); ?>" method="GET" id="reporte-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('sexcluded.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="boton-reporte-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('View')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row" id="libro-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="class_report"><?php echo e(__('Class report')); ?></label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="class_report" id="class_report" required>
                            <option value="0" enabled><?php echo e(__('Select')); ?></option>
                            <option value="1">Contribuyente</option>
                            <option value="2">Consumidor</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('sales.libro')); ?>" method="GET" id="libro-contribuyente">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-libro-section">
                                <button type="submit" class="btn btn-primary" target="_blank"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Print')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

         <form action="<?php echo e(route('sales.report')); ?>" method="GET" id="libro-consumidor">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-libro-section">
                                <button type="submit" class="btn btn-primary" target="_blank"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Consu')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="<?php echo e(route('sexcluded.excel')); ?>" method="GET" id="excel-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas y tipo de libro a exportar
                        </div>
                         <div class="card-body col-md-3">
                            <label><?php echo e(__('Type report')); ?></label>
                            <select class="form-control" name="type_libro" id="type_libro" required>
                                <option value="0" disabled><?php echo e(__('Select')); ?></option>
                                <option value="1">Excluidos</option>
                                <option value="2">Retenciones</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('sexcluded.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-excel-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Export')); ?></button>
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
                            <h3 class=""><?php echo e(trans('file.Suject Excluid Report')); ?></h3><br/>
                        </div>
                        <div class="col-6">
                            <b>Fecha de Consulta</b> <?php echo e(\Carbon\Carbon::now()->format('d-m-Y')); ?>

                            <br>
                            <b>Cantidad Registros</b> <?php echo e($sales->count()); ?>

                            <br>
                            <b>Total Ventas </b>$ <?php echo e(number_format($sumaTotal,2)); ?>

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
                            <?php $stotal=0 ?>
                            <?php $sniva=0 ?>
                            <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $stotal += $comp->subtotal ?>
                            <?php $sniva += $comp->impuesto ?>
                            <tr>
                                <td><?php echo e(\Carbon\Carbon::parse($comp->created_at)->format('d/m/Y')); ?></td>
                                <?php if($comp->tercero=="off"): ?>
                                    <td><?php echo e($comp->reference_no); ?></td>
                                <?php else: ?>
                                    <td class="text-danger"><?php echo e($comp->reference_no); ?></td>
                                <?php endif; ?>    
                                <?php if($comp->status=="Anulado"): ?>
                                    <td>A N U L A D O</td>
                                <?php else: ?>
                                    <td><?php echo e($comp->cliente); ?></td>
                                <?php endif; ?>
                                <?php if($comp->document_id==1): ?>
                                    <td>CF</td>
                                <?php elseif($comp->identification_type=="04"): ?>
                                    <td>FA</td>
                                <?php else: ?>
                                    <td>NC</td>
                                <?php endif; ?>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($comp->subtotal,2)); ?></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($comp->impuesto,2)); ?></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($comp->exento,2)); ?></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($comp->percepcion,2)); ?></td>
                                <?php if($comp->identification_type == "1"): ?>
                                    <td class="text-right" colspan="1">$<?php echo e(number_format($comp->exento,2)); ?></td>
                                <?php else: ?>
                                    <td class="text-right" colspan="1">$<?php echo e(number_format($comp->total,2)); ?></td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>SUMA:</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($stotal,2)); ?></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($sniva,2)); ?></td>
                                <td class="text-right">$<?php echo e(number_format(0.00,2)); ?></td>
                                <td class="text-right">$<?php echo e(number_format(0.00,2)); ?></td>
                                <td class="text-right" colspan="1">$<?php echo e(number_format($sexento,2)); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp7433\htdocs\frutas\resources\views/sexcluded/report.blade.php ENDPATH**/ ?>