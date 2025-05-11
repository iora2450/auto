<div id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modal_header" class="modal-title"></h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                </div>
                <div class="modal-body">
                    <form id='actualiza_quedan'>
                        <input type="hidden" name="id_orden" id="id_orden">
                        <div class="form-group">
                            <label>Editar quedan</label>
                                <select required class="form-control selectpicker" id="customer_id" name="customer_id" onchange='saveValue(this);'>
                                        <?php $__currentLoopData = $lims_client_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer_group->id); ?>"><?php echo e($customer_group->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                        </div>
                    <div class="col-md-12">
                                <div class="form-group">
                                    <label>Fecha de quedan*</strong> </label>
                                    <input type="date" id="date_quedan" name="date_quedan" required class="form-control" >
                                    <input type="hidden" id="quedan_id" name="quedan_id" required class="form-control" >

                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Numero de factura</label>
                                    <input type="text" name="number_invoice" id="number_invoice" readonly class="form-control">
                                </div>
                            </div>


                             <div class="col-md-12">
                                <div class="form-group">
                                  <label>Documentos para del quedan: </label>

                                    <select id='documentos' name='number_invoice_2[]' class="selectpicker" multiple style='width: 100%'>
                                         
                                        </select>

                                    ,<!--<label>Numero de factura</label>
                                    <input type="text" name="number_invoice" id="number_invoice" class="form-control">
                                -->
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Total</label>
                                    <input type="number" name="total" id="total" class="form-control" step='any'>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Fecha de vencimiento *</label>
                                    <input type="date" name="due_date" id="due_date" required class="form-control">
                                    
                                </div>
                            </div>

                        <input type="submit" value="Actualizar" class="btn btn-primary">
                          
                    </form>
                </div>
            </div>
        </div>
    </div>




 <?php $__env->startSection('content'); ?>
<?php if($errors->has('name')): ?>
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e($errors->first('name')); ?></div>
<?php endif; ?>
<?php if($errors->has('image')): ?>
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e($errors->first('image')); ?></div>
<?php endif; ?>
<?php if($errors->has('email')): ?>
<div class="alert alert-danger alert-dismissible text-center">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e($errors->first('email')); ?></div>
<?php endif; ?> 
<?php if(session()->has('message')): ?>
  <div class="alert alert-success alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo session()->get('message'); ?></div> 
<?php endif; ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>
<section>
    <?php if(in_array("employees-add", $all_permission)): ?>
    <div class="container-fluid">
        <a href="<?php echo e(route('quedan.create')); ?>" class="btn btn-info"><i class="dripicons-plus"></i>Crear quedan</a>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table id="employee-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th>Fecha quedan</th>
                    <th>Numero de factura</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Total</th>
                    <th>Fecha de vencimiento</th>
                    <th class="not-exported"><?php echo e(trans('file.action')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $lims_quedan_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quedan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <?php $cliente = \App\Customer::find($quedan->customer_id); ?>
                <tr data-id="<?php echo e($quedan->id); ?>">
                   
                <td><?php echo e($key); ?></td>
                    <td><?php echo e($quedan->date_quedan); ?></td>
                    <td><?php echo e($quedan->number_invoice); ?></td>
                    <td><?php echo e($cliente->name); ?></td>
                    <?php
                    $fecha1 =     date("Y-m-d");
                                  $fecha2 = $quedan->due_date;
                         
                                   $fecha1= new DateTime($fecha1);
                                   $fecha2= new DateTime($fecha2);
                                   $diff = $fecha1->diff($fecha2);

                                    if($fecha1 <= $fecha2){
                                    $interval = $diff->days;
                               
                                }else{
                                    $interval = $diff->days*-1;
                           
                                }
                
                   
                 if($interval <=7 && $interval >1 ){
                    $estado_color= '<div class="badge badge-warning">Revisar</div>';
              }
                
                elseif($interval ==1){
                    $estado_color = '<div class="badge badge-danger">por vencer</div>';
                 }
                 elseif($interval >7){
                    $estado_color = '<div class="badge badge-success">En tiempo</div>';
                  }
                  else{
                    $estado_color = '<div class="badge badge-danger">Vencida</div>';
                   }
                    ?>
                    <td><?php echo $estado_color; ?></td>
                    <td><?php echo e($quedan->total); ?></td>
                    <td><?php echo e($quedan->due_date); ?></td>
                    
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo e(trans('file.action')); ?>

                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                <?php if(in_array("quedan-edit", $all_permission)): ?>
                                <li>


                                    <button type="button" data-id="<?php echo e($quedan->id); ?>"  data-customer_id="<?php echo e($quedan->customer_id); ?>"
                                     data-date_quedan="<?php echo e($quedan->date_quedan); ?>"  data-number_invoice="<?php echo e($quedan->number_invoice); ?>"  data-total="<?php echo e($quedan->total); ?>"  data-due_date="<?php echo e($quedan->due_date); ?>"   class='btn btn-link edit-btn'   data-toggle="modal"  data-target="#editModal"><i class="dripicons-document-edit"></i> Editar</button> 
                                </li>
                                <?php endif; ?>
                                <li class="divider"></li>
                                <?php if(in_array("quedan-delete", $all_permission)): ?>
                                <?php echo e(Form::open(['route' => ['quedan.destroy', $quedan->id], 'method' => 'DELETE'] )); ?>

                                <li>
                                    <button type="submit" class="btn btn-link" onclick="return confirmDelete()"><i class="dripicons-trash"></i> <?php echo e(trans('file.delete')); ?></button>
                                </li>
                                <?php echo e(Form::close()); ?>

                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</section>


<script type="text/javascript">

    $("#documentos").change(function(event){

var options = $(this).find('option:selected').map(function() {
    return $(this).data('monto');
  }).get();

total=0
for (i = 0; i < options.length; ++i) {
    total+=options[i];
}
$("#total").val(total)

})




    $("ul#hrm").siblings('a').attr('aria-expanded','true');
    $("ul#hrm").addClass("show");
    $("ul#hrm #employee-menu").addClass("active");

    var employee_id = [];
    var user_verified = <?php echo json_encode(env('USER_VERIFIED')) ?>;
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function confirmDelete() {
        if (confirm("Are you sure want to delete?")) {
            return true;
        }
        return false;
    }

    $('.edit-btn').on('click', function() {
        //alert($(this).data('customer_id'))
        fecha = $(this).data('date_quedan')
        st= fecha.substring(0, 10)
        //alert(st)
        $("#quedan_id").val( $(this).data('id') );
        $("#customer_id").val( $(this).data('customer_id') );
        $("#date_quedan").val(st);
        $("#number_invoice").val( $(this).data('number_invoice') );
        $("#total").val( $(this).data('total') );
        fecha = $(this).data('due_date')
        st= fecha.substring(0, 10)
        

        $.ajax({
                type: "POST",
                url: "quedan/obtener_facturas",
                data:{customer_id:$(this).data('customer_id')},
               
                success: function(result) {
                  $("#documentos").html(result)
                  $('#documentos').selectpicker('refresh');

                }
                
            });



        $("#due_date").val( st);
       
        $('.selectpicker').selectpicker('refresh');
    });


    $("#actualiza_quedan").submit(function(event){
     event.preventDefault() 

    $.ajax({
                type: "POST",
                url: "quedan/update_quedan",
                data:$("#actualiza_quedan").serialize(),
               
                success: function(result) {
                  alert("Datos Actualizados")
                  location.reload(); 

                }
                
            });





    })

    $('#employee-table').DataTable( {
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
        'columnDefs': [
            {
                "orderable": false,
                'targets': [0, 1, 6]
            },
            {
                'render': function(data, type, row, meta){
                    if(type === 'display'){
                        data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
                    }

                   return data;
                },
                'checkboxes': {
                   'selectRow': true,
                   'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                },
                'targets': [0]
            }
        ],
        'select': { style: 'multi',  selector: 'td:first-child'},
        'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        dom: '<"row"lfB>rtip',
        buttons: [
            {
                extend: 'pdf',
                text: '<?php echo e(trans("file.PDF")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
                customize: function(doc) {
                    for (var i = 1; i < doc.content[1].table.body.length; i++) {
                        if (doc.content[1].table.body[i][0].text.indexOf('<img src=') !== -1) {
                            var imagehtml = doc.content[1].table.body[i][0].text;
                            var regex = /<img.*?src=['"](.*?)['"]/;
                            var src = regex.exec(imagehtml)[1];
                            var tempImage = new Image();
                            tempImage.src = src;
                            var canvas = document.createElement("canvas");
                            canvas.width = tempImage.width;
                            canvas.height = tempImage.height;
                            var ctx = canvas.getContext("2d");
                            ctx.drawImage(tempImage, 0, 0);
                            var imagedata = canvas.toDataURL("image/png");
                            delete doc.content[1].table.body[i][0].text;
                            doc.content[1].table.body[i][0].image = imagedata;
                            doc.content[1].table.body[i][0].fit = [30, 30];
                        }
                    }
                },
            },
            {
                extend: 'csv',
                text: '<?php echo e(trans("file.CSV")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    format: {
                        body: function ( data, row, column, node ) {
                            if (column === 0 && (data.indexOf('<img src=') != -1)) {
                                var regex = /<img.*?src=['"](.*?)['"]/;
                                data = regex.exec(data)[1];                 
                            }
                            return data;
                        }
                    }
                },
            },
            {
                extend: 'print',
                text: '<?php echo e(trans("file.Print")); ?>',
                exportOptions: {
                    columns: ':visible:Not(.not-exported)',
                    rows: ':visible',
                    stripHtml: false
                },
            },
            {
                text: '<?php echo e(trans("file.delete")); ?>',
                className: 'buttons-delete',
                action: function ( e, dt, node, config ) {
                    if(user_verified == '1') {
                        employee_id.length = 0;
                        $(':checkbox:checked').each(function(i){
                            if(i){
                                employee_id[i-1] = $(this).closest('tr').data('id');
                            }
                        });
                        if(employee_id.length && confirm("Are you sure want to delete?")) {
                            $.ajax({
                                type:'POST',
                                url:'employees/deletebyselection',
                                data:{
                                    employeeIdArray: employee_id
                                },
                                success:function(data){
                                    alert(data);
                                }
                            });
                            dt.rows({ page: 'current', selected: true }).remove().draw(false);
                        }
                        else if(!employee_id.length)
                            alert('No employee is selected!');
                    }
                    else
                        alert('This feature is disable for demo!');
                }
            },
            {
                extend: 'colvis',
                text: '<?php echo e(trans("file.Column visibility")); ?>',
                columns: ':gt(0)'
            },
        ],
    } );
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/frankdev/public_html/auto/resources/views/quedan/index.blade.php ENDPATH**/ ?>