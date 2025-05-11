

<?php $__env->startSection('content'); ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>
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
                            <option value="4">Descargas PDF</option>
                            <option value="5">Descargas JSON</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('sales.report')); ?>" method="GET" id="reporte-section">
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
                            Seleccione las fechas de FACturas
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

        <form action="<?php echo e(route('sales.excel')); ?>" method="GET" id="excel-section">
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
                                <option value="1">Contribuyentes</option>
                                <option value="2">Consumidor Final</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-excel-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Export')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row" id="pdf-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="pdf_report"><?php echo e(__('PDF report')); ?></label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="pdf_report" id="pdf_report" required>
                            <option value="0" enabled><?php echo e(__('Select')); ?></option>
                            <option value="1">Creditos Fiscales</option>
                            <option value="2">Consumidor Final</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('sales.pdfreport')); ?>" method="GET" id="ccf-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los CCF a descargar
                        </div>

                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-ccf-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Export')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="<?php echo e(route('sales.pdfreportFac')); ?>" method="GET" id="fac-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los FACTURAS a descargar
                        </div>

                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-fac-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Export')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

         <div class="row" id="json-section">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="json_report"><?php echo e(__('JSON report')); ?></label>
                    <div class="col-md-9">
                        <select class="col-md-6 form-control" name="json_report" id="json_report" required>
                            <option value="0" enabled><?php echo e(__('Select')); ?></option>
                            <option value="1">Creditos Fiscales</option>
                            <option value="2">Consumidor Final</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('sales.download-json-ccf')); ?>" method="GET" id="jsonccf-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los JSON de ccf a descargar
                        </div>

                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-jsonccf-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Download Zip')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form action="<?php echo e(route('sales.download-json-fac')); ?>" method="GET" id="jsonfac-section">
            <?php echo csrf_field(); ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fa fa-edit"></i>
                            Seleccione las fechas de los JSON de facturas a descargar
                        </div>

                        <div class="card-body">
                            <?php echo $__env->make('sale.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="form-actions" id="button-jsonfac-section">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save fa-1x"></i> <?php echo e(__('Download Zip')); ?></button>
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
                            <h2 class="">MR J&B INVERSIONES, S.A. DE C.V.</h2><br/>
                            <h3 class=""><?php echo e(trans('file.Sale Report')); ?></h3><br/>
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
                                <th>N¨²mero Venta</th>
                                <th>Cliente</th>
                                <th>Tipo de identificaci¨®n</th>
                                <th class="text-center" colspan="1">Sub-total</th>
                                <th class="text-center" colspan="1">Impuesto</th>
                                <th class="text-center" colspan="1">Exento</th>
                                <th class="text-center" colspan="1">Percepci¨®n</th>
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
                                <?php elseif($comp->document_id===2): ?>
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
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/frankdev/public_html/auto/resources/views/sale/report.blade.php ENDPATH**/ ?>