<?php $__env->startSection('content'); ?>

<style type="text/css">

    tbody th
    {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        border-bottom-width: 0px !important;
    }
 
   tbody tr td 
   {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        border-bottom-width: 0px !important;
    }

    tfoot tr td 
   {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        border-bottom-width: 0px !important;
    }
</style>

<section>
    <h3 class="text-center"><?php echo e(trans('file.Box Cut Report')); ?></h3>
    <?php echo Form::open(['route' => 'report.boxCutReport', 'method' => 'post']); ?>

    <div class="col-md-6 offset-md-3 mt-4">
        <div class="form-group row">
            <label class="d-tc mt-2"><strong><?php echo e(trans('file.Choose Your Date')); ?></strong> &nbsp;</label>
            <div class="d-tc">
                <div class="input-group">
                    <input type="text" class="daterangepicker-field form-control" value="<?php echo e($start_date); ?> To <?php echo e($end_date); ?>" required />
                    <input type="hidden" name="start_date" value="<?php echo e($start_date); ?>" />
                    <input type="hidden" name="end_date" value="<?php echo e($end_date); ?>" />
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><?php echo e(trans('file.submit')); ?></button>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <?php echo e(Form::close()); ?>

    <div class="container-fluid">
        <div class="row mt-4">
            <div class="container-fluid">
                <input name="b_print" type="button" class="ipt" onClick="printdiv('div_print');" value=" Print ">    
            </div>  
        </div>
    </div>

    <div class="table-responsive mb-4" id="div_print">
        <center>            
            <b>MR. J&B INVERSIONES DEL CIELO, S.A. DE C.V.</b>
            <br>
            <b>INFORME DE CORTE DE CAJA DEL DIA</b>
            <br>
            <br>Del <?php echo e(\Carbon\Carbon::parse($start_date)->format('d/m/Y')); ?> al <?php echo e(\Carbon\Carbon::parse($end_date)->format('d/m/Y')); ?> 
            <br>            
        </center>

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-7">
                    <div class="card">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Venta</th>
                                                <th>Devolución</th>
                                                <th>Total Facturado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Ventas por CCF </td>
                                                <td  style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"class="text-right">$<?php echo e($saleCcf); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($saleCcf); ?></td>
                                            </tr>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Ventas por Factura </td>
                                                <td  style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"class="text-right">$<?php echo e($saleFac); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($saleFac); ?></td>
                                            </tr>       
                                        </tbody>              
                                    </table>
                                </div>
                            </div>
                        </div> 
                    </div>

                    <div class="card">
                        <div class="tab-content">
                            <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                                <h4 class="text-center">CORRELATIVOS COMPROBANTES</h4>
                            </div>
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">
                                    <table class="table">                                        
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Tipo Comprobante</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Desde No.</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Hasta No.</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Ventas por CCF</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($vsaleNumberCcfI); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($vsaleNumberCcfF); ?></td>
                                            </tr>

                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Ventas por Factura</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($vsaleNumberFacI); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($vsaleNumberFacF); ?></td>
                                            </tr> 
                                        </tbody>              
                                    </table>
                                </div>
                            </div>
                        </div>                               
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                            <h4 class="text-center">ARQUEO DE CAJA</h4>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Denominación</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Cantidad</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stotal=0 ?>                                            
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>
                                            <?php $__currentLoopData = $boxesCuts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $boxesCut): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $stotal += $boxesCut->total_dinero ?>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($boxesCut->name); ?></td>
                                                <td class="text-right" style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">$<?php echo e($boxesCut->qty_producto); ?></td>
                                                <td class="text-right" style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">$<?php echo e($boxesCut->total_dinero); ?></td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>              
                                        </tbody>
                                        <tfoot>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right" colspan="1"><strong>$<?php echo e(number_format($stotal,2)); ?></strong></td>
                                        </tfoot>              
                                    </table>
                                </div>
                            </div>
                        </div>        
                    </div>                
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                            <h4 class="text-center">CHEQUES RECIBIDOS</h4>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Número</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Banco</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Cliente</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Valor</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>No. Reserva</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Banco RM</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sgrantotal=0 ?>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>                  
                                            <?php $__currentLoopData = $pagos_cheque_sale; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $sgrantotal += $key->amount ?> 
                                                <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->cheque_no); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->banco); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->name); ?></td>
                                                    <td class="text-right" style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">$<?php echo e($key->amount); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->reserva); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->banco_reserva); ?></td>
                                                </tr>              
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right" colspan="1"><strong>$<?php echo e(number_format($sgrantotal,2)); ?></strong></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-8">
                    <div class="card">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <?php $vtotalIngresos=0 ?>
                                        <?php $vtotalEgresos=0 ?>
                                        <?php $pagosCCF=0 ?>
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $vtotalIngresos +=  $cash_registers + $cash_payment_sale + $cobro_payment_sale + $cheque_payment_sale + $deposit_payment_sale ?> 
                                            <?php $vtotalEgresos += $pagosCCF + $expenses_sum + $pagosCCF + $pagosCCF ?>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>                  
                                            <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">APERTURA DE CAJA</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($cash_registers,2)); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">PAGOS CCF</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($pagosCCF); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">CONTADO</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($cash_payment_sale,2)); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">PAGOS FACTURAS</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($pagosCCF); ?></td>
                                            </tr> 
                                             <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">COBROS DEL DIA</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($cobro_payment_sale,2)); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">VALES CAJA</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($expenses_sum,2)); ?></td>
                                            </tr>    
                                             <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">CHEQUES</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($cheque_payment_sale,2)); ?></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">REINTEGROS CLIENTES</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($pagosCCF); ?></td>
                                            </tr> 
                                           <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">DEPOSITOS RECIBIDOS</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($deposit_payment_sale,2)); ?></td>
                                            </tr> 
                                             
                                        </tbody>
                                        <tfoot>
                                            <tr style="padding-top: 10px !important;">
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><strong>INGRESOS</strong></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right"><strong>$<?php echo e(number_format($vtotalIngresos,2)); ?></strong></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">EGRESOS</td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right"><strong>$<?php echo e(number_format($vtotalEgresos,2)); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">VALOR A REMESAR</td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right"><strong>$<?php echo e(number_format($vtotalIngresos-$vtotalEgresos,2)); ?></strong></td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">(-)FALTANTE</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">(+)sOBRANTE</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                            <h4 class="text-center">GASTOS VARIOS</h4>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Fecha</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Concepto</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $stotal=0 ?>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>          
                                            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $stotal += $expense->amount ?>
                                                <tr>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e(\Carbon\Carbon::parse($expense->created_at)->format('d/m/Y')); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($expense->note); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e($expense->amount); ?></td>
                                                </tr>              
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right"><strong>Total:</strong></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right" colspan="1"><strong>$<?php echo e(number_format($stotal,2)); ?></strong></td>

                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
        
        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                            <h4 class="text-center">DETALLE DE DOCUMENTOS ANULADOS</h4>
                        </div>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Tipo <br>Documento</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">Fecha</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Serie</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Número</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sgrantotal=0 ?>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>          
                                            <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php $sgrantotal += $key->grand_total ?> 
                                                <tr>
                                                    <?php if($key->canceled==1): ?>
                                                        <?php if($key->document_id==1): ?>
                                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">CCF</td>
                                                        <?php else: ?>
                                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">FACTURA</td>
                                                        <?php endif; ?>
                                                        <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e(\Carbon\Carbon::parse($key->created_at)->format('d/m/Y')); ?></td>
                                                        <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->serie); ?></td>
                                                        <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($key->reference_no); ?></td>
                                                    <?php endif; ?>
                                                </tr>              
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-12">
                    <div class="card">
                        <div class="tab-content">
                            <div class="card-header d-flex justify-content-between align-items-center" style="padding-top: 0px; padding-bottom: 0px;">
                                <h4 class="text-center">DETALLE DE COMPRAS</h4>
                            </div>
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <thead>
                                            <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>No</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Fecha</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" >Numero de <br>Documento</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Proveedor</th>
                                                <th style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" border=1>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sgrantotal=0 ?>
                                             <tr style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;">
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                                <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            </tr>                     
                                            <?php $__currentLoopData = $purchases_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $sgrantotal += $purchase->grand_total ?> 
                                                <tr>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($loop->iteration); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e(\Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y')); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($purchase->invoice); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"><?php echo e($purchase->name); ?></td>
                                                    <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right">$<?php echo e(number_format($purchase->grand_total,2)); ?></td>
                                                </tr>              
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                        <tfoot>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;"></td>    
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right"><strong>Total:</strong></td>
                                            <td style="padding-top: 0px; padding-bottom: 0px; border-bottom-width: 0px;" class="text-right" colspan="1"><strong>$<?php echo e(number_format($sgrantotal,2)); ?></strong></td>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>                        

        <br>

        <div class="container-fluid">
            <div class="row">        
                <div class="col-md-12">
                    <div class="card">
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                                <div class="table-responsive">                
                                    <table id="report-table" class="table" style='line-height: 1.1;'>
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Elaborado Por</td>
                                                <td>Revisado Por</td>
                                                <td>Autorizado Por</td>
                                            </tr> 
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</section>

<script type="text/javascript">

    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #CorteCajaReport-report-menu").addClass("active");

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

    function printdiv(printpage) {
        var oldstr = document.body.innerHTML;
        var headstr = "<html><head><title></title></head>" + ' <style>;}@page  { size: letter; }  #izq{float:left; #der{float:right; text-align:right;   }  }</style></head>';
        var footstr = "</body>";
        var newstr = document.all.item(printpage).innerHTML;
        
        document.body.innerHTML = headstr + newstr + footstr;
        window.print();
        //document.body.innerHTML = oldstr;
        return false; 
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp7433\htdocs\frutas\resources\views/report/box_cut.blade.php ENDPATH**/ ?>