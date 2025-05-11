 <?php $__env->startSection('content'); ?>
<?php if(empty($datos)): ?>
<div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e('No Data exist between this date range!'); ?></div>
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>

<section class="forms">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Reporte de Corte de Caja</h3>
            </div>
            <?php echo Form::open(['route' => 'report.SalesdayReport', 'method' => 'get']); ?>

            <div class="row mb-3">
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>Fecha inicio:</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input name='fecha_inicio' type="date" value='<?php echo e($start_date); ?>' required />
                                
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group row">
                        <label class="d-tc mt-2"><strong>Fecha final</strong> &nbsp;</label>
                        <div class="d-tc">
                            <div class="input-group">
                                <input name='fecha_final' type="date" value='<?php echo e($end_date); ?>' required />
                            </div>
                        </div>
                    </div>
                </div>


                
               
                <div class="col-md-4 offset-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit"><?php echo e(trans('file.submit')); ?></button>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
        
        <div class="container-fluid">
            <input name="b_print" type="button" class="ipt" onClick="printdiv('div_print');" value=" Print ">    
        </div>   
    </div>

    


    <div class="table-responsive mb-4" id="div_print">
        <?php if(count($datos)>0): ?> 
        <center>
            
            <b>MR. J&B INVERSIONES DEL CIELO, S.A. DE C.V.</b><br><b>Auxiliar de Corte de Caja</b><br>Del <?php echo e(\Carbon\Carbon::parse($start_date)->format('d/m/Y')); ?> al <?php echo e(\Carbon\Carbon::parse($end_date)->format('d/m/Y')); ?> 
            <br>

            <div id='der' style='float:right;'>
                <b>NIT:</b>0614-011022-104-3<br>
                <b>NRC:</b>320113-6
            </div>
        </center>
        <?php endif; ?>

        <br>

        <table id="report-table" class="table" style='line-height: 1.1;'>
            <thead>
                <tr>
                    <th border=1>No</th>
                    <th border=1>Fecha</th>
                    <th >Numero de <br>Documento</th>
                    <th border=1>Nombre Cliente</th>
                    <th border=1>Valor Sin Iva</th>
                    <th border=1>Iva</th>
                    <th border=1>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $stotal=0 ?>
                <?php $siva=0 ?>
                <?php $sgrantotal=0 ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>                  
                <?php $__currentLoopData = $datos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $stotal += $key->subtotal ?>
                <?php $siva += $key->iva ?> 
                <?php $sgrantotal += $key->grand_total ?> 
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e(\Carbon\Carbon::parse($key->created_at)->format('d/m/Y')); ?></td>
                        <td><?php echo e($key->reference_no); ?></td>
                        <?php if($key->canceled==1): ?>
                            <td>A N U L A D O</td>
                        <?php else: ?>
                            <td><?php echo e($key->name); ?></td>
                        <?php endif; ?>
                        <td class="text-right">$<?php echo e($key->subtotal); ?></td>
                        <td class="text-right">$<?php echo e($key->iva); ?></td>
                        <td class="text-right">$<?php echo e($key->grand_total); ?></td>
                    </tr>              
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
            <tfoot>
                <td></td>
                <td></td>
                <td></td>    
                <td></td>
                <td class="text-right" colspan="1">$<?php echo e(number_format($stotal,2)); ?></td>
                <td class="text-right" colspan="1">$<?php echo e(number_format($siva,2)); ?></td>
                <td class="text-right" colspan="1">$<?php echo e(number_format($sgrantotal,2)); ?></td>

            </tfoot>
        </table>
    </div>
</section>

<script type="text/javascript">

    $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #product-report-menu").addClass("active");

    $('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
    $('.selectpicker').selectpicker('refresh');


    function printdiv(printpage) {
        var headstr = "<html><head><title></title></head><body>" + ' <style>body{background-color:white !important;}@page  { size: letter; }  #izq{float:left; #der{float:right; text-align:right;   }  }</style></head>';
        var footstr = "</body>";
        var newstr = document.all.item(printpage).innerHTML;
        var oldstr = document.body.innerHTML;
        document.body.innerHTML = headstr + newstr + footstr;
        window.print();
        document.body.innerHTML = oldstr;
        return false;
    }

    $('#report-table1').DataTable( {
        "order": [],
        'language': {
            'lengthMenu': '_MENU_ <?php echo e(trans("file.records per page")); ?>',
             "info":      '<small><?php echo e(trans("file.Showing")); ?> _START_ - _END_ (_TOTAL_)</small>',
            "search":  '<?php echo e(trans("file.Search")); ?>',
            'paginate': {
                    'previous': '<i class="dripicons-chevron-left"></i>',
                    'next': '<i class="dripicons-chevron-right"></i>'
            }
        },
       
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<?php echo e(trans("file.PDF")); ?>',
                orientation: 'landscape',
                pageSize: 'LEGAL',
                 text:      '<i class="fa fa-file-pdf-o">PDF</i> ',
                title: "",
                titleAttr: 'Exportar a PDF',
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'csv',
                text: '<?php echo e(trans("file.CSV")); ?>',
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'print',
                text: '<?php echo e(trans("file.Print")); ?>',
               /* exportOptions: {
                    columns: ':visible:not(.not-exported)',
                    rows: ':visible'
                },
                */
                action: function(e, dt, button, config) {
                    datatable_sum(dt, true);
                    $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                    datatable_sum(dt, false);
                },
                footer:true
            },
            {
                extend: 'colvis',
                text: '<?php echo e(trans("file.Column visibility")); ?>',
                columns: ':gt(0)'
            }
        ],
        drawCallback: function () {
            var api = this.api();
            datatable_sum(api, false);
        }
    } );

    function datatable_sum(dt_selector, is_calling_first) {
        if (dt_selector.rows( '.selected' ).any() && is_calling_first) {
            var rows = dt_selector.rows( '.selected' ).indexes();

          
           $( dt_selector.column( 4 ).footer() ).html(dt_selector.cells( rows, 4, { page: 'current' } ).data().sum().toFixed(2));
           
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
             $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
              $( dt_selector.column( 10 ).footer() ).html(dt_selector.cells( rows, 10, { page: 'current' } ).data().sum().toFixed(2));
               $( dt_selector.column( 11 ).footer() ).html(dt_selector.cells( rows, 11, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 12 ).footer() ).html(dt_selector.cells( rows, 12, { page: 'current' } ).data().sum().toFixed(2));
        }
        else {
                    $( dt_selector.column( 4 ).footer() ).html(dt_selector.cells( rows, 4, { page: 'current' } ).data().sum().toFixed(2));
           
            $( dt_selector.column( 7 ).footer() ).html(dt_selector.cells( rows, 7, { page: 'current' } ).data().sum().toFixed(2));
            $( dt_selector.column( 8 ).footer() ).html(dt_selector.cells( rows, 8, { page: 'current' } ).data().sum().toFixed(2));
             $( dt_selector.column( 9 ).footer() ).html(dt_selector.cells( rows, 9, { page: 'current' } ).data().sum().toFixed(2));
              $( dt_selector.column( 10 ).footer() ).html(dt_selector.cells( rows, 10, { page: 'current' } ).data().sum().toFixed(2));
               $( dt_selector.column( 11 ).footer() ).html(dt_selector.cells( rows, 11, { page: 'current' } ).data().sum().toFixed(2));
                $( dt_selector.column( 12 ).footer() ).html(dt_selector.cells( rows, 12, { page: 'current' } ).data().sum().toFixed(2));
        }
    }
 

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

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp7433\htdocs\frutas\resources\views/report/Sales_daily_report.blade.php ENDPATH**/ ?>