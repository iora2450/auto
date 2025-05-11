 

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
        <a href="<?php echo e(route('sexcluded.create')); ?>" class="btn btn-info"><i class="dripicons-plus"></i><?php echo e(trans('file.Add Excluded')); ?></a>
    </div>
    <?php endif; ?>
    <div class="table-responsive">
        <table id="employee-table" class="table">
            <thead>
                <tr>
                    <th class="not-exported"></th>
                    <th><?php echo e(trans('file.Date')); ?></th>
                    <th><?php echo e(trans('file.reference')); ?></th>
                    <th>EstadoDTE</th>
                    <th><?php echo e(trans('file.Excluded')); ?></th>
                    <th><?php echo e(trans('file.Warehouse')); ?></th>
                    <th><?php echo e(trans('file.grand total')); ?></th>
                    <th class="not-exported"><?php echo e(trans('file.action')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $lims_excluded_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$quedan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr data-id="<?php echo e($quedan->id); ?>">                   
                        <td><?php echo e($key); ?></td>
                        <td><?php echo e($quedan->created_at); ?></td>
                        <td><?php echo e($quedan->numerocontrol); ?></td>
                        <td>
                        <?php if($quedan->estadodte === 'done'): ?>
        Procesado
    <?php elseif($quedan->estadodte == null || $quedan->estadodte === ''): ?>    
        S /T 
    <?php else: ?>
        Rechazado
    <?php endif; ?>
        </td>
                        <td><?php echo e($quedan->excluded->name); ?></td>
                        <td><?php echo e($quedan->warehouse->name); ?></td>
                        <td><?php echo e($quedan->grand_total); ?></td>                    
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php echo e(trans('file.action')); ?>

                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    <li>
                                        <a href="<?php echo e(url('sexcluded/gen_invoice_ccf/'.$quedan->id)); ?>" target="_blank" style="text-decoration: none;">
                                            <button type="button" class="btn btn-info btn-sm">
                                                <i class="fa fa-file fa-1x"></i> PDF
                                            </button> &nbsp;

                                        </a>
                                        <li>
                                    <button type="button" class="btn btn-link" id="examinarDTEButton" onclick="verdte('<?php echo e($quedan->id); ?>')"><i class="fa fa-eye"></i> DTE</button>
                                </li>   
                                <li>
                                    <button type="button" class="btn btn-link" id="examinarDTEButtonjson" onclick="dwdte('<?php echo e($quedan->id); ?>')"><i class="fa fa-eye"></i> JSON</button>
                                </li>                                    
                                    </li>                                    
                                    <li class="divider"></li>
                                    <?php if(in_array("quedan-edit", $all_permission)): ?>
                                        <li>
                                            <button 
                                                type="button" 
                                                data-id="<?php echo e($quedan->id); ?>" 
                                                data-supplier_id="<?php echo e($quedan->supplier_id); ?>"
                                                data-date_quedan="<?php echo e($quedan->date_quedan); ?>"  
                                                data-number_invoice="<?php echo e($quedan->number_invoice); ?>"  
                                                data-total="<?php echo e($quedan->total); ?>"  
                                                data-due_date="<?php echo e($quedan->due_date); ?>"   
                                                class='btn btn-link edit-btn'   
                                                data-toggle="modal"  
                                                data-target="#editModal"
                                            >
                                                <i class="dripicons-document-edit"></i> 
                                                Editar
                                            </button> 
                                        </li>
                                    <?php endif; ?>
                                    <li class="divider"></li>
                                    <?php if(in_array("quedan-delete", $all_permission)): ?>
                                        <?php echo e(Form::open(['route' => ['quedan.destroy', $quedan->id], 'method' => 'DELETE'] )); ?>

                                        <li>
                                            <button type="submit" class="btn btn-link" onclick="return confirmDelete()">
                                                <i class="dripicons-trash"></i> 
                                                <?php echo e(trans('file.delete')); ?>

                                            </button>
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
    
    <div id="return-detailsdte" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
        <div role="document" class="modal-dialog">
          <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
            <div class="row">
                <div class="col-md-3">
             
              </div>
                 </div>
                <div id="return-contentdte" class="modal-body">

                    
                </div>
               
                <div id="return-footerdte" class="modal-body"></div>
          </div>
        </div>
    </div>

</section>

<div id="return-details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="container mt-3 pb-2 border-bottom">
                <div class="row">
                    <div class="col-md-3">
                        <button id="print-btn" type="button" class="btn btn-default btn-sm d-print-none">
                            <i class="dripicons-print"></i> 
                            <?php echo e(trans('file.Print')); ?>

                        </button>
                        
                        <input type="hidden" name="return_id">                       
                    </div>
                    <div class="col-md-6">
                        <h3 id="exampleModalLabel" class="modal-title text-center container-fluid"><?php echo e($general_setting->site_title); ?></h3>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="close-btn" data-dismiss="modal" aria-label="Close" class="close d-print-none">
                            <span aria-hidden="true">
                                <i class="dripicons-cross"></i>
                            </span>
                        </button>
                    </div>
                    <div class="col-md-12 text-center">
                        <i style="font-size: 15px;"><?php echo e(trans('file.Return Details')); ?></i>
                    </div>
                </div>
            </div>
            
            <div id="return-content" class="modal-body">
            </div>
            <br>
            <table class="table table-bordered product-return-list">
                <thead>
                    <th>#</th>
                    <th><?php echo e(trans('file.product')); ?></th>
                    <th><?php echo e(trans('file.Qty')); ?></th>
                    <th><?php echo e(trans('file.Unit Price')); ?></th>
                    <th><?php echo e(trans('file.Tax')); ?></th>
                    <th><?php echo e(trans('file.Discount')); ?></th>
                    <th><?php echo e(trans('file.Subtotal')); ?></th>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="return-footer" class="modal-body"></div>
        </div>
    </div>
</div>


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



    
    function verdte(id) {
        
        $.ajax({
            url: 'sexcluded/verdte',
            type: 'POST',
            data: { id: id }, // Datos a enviar (el ID)
            success: function (response) {
                try {
                   
                    // Intenta analizar el JSON recibido
                   if(response =="done")
                   {
                    alert("done");
                   } 
                   else if(response =="1")
                   {
                    alert("Sin transmision");
                   }
                    else if (response == null || response.trim() === "") {
                     // Manejar el caso de cadena vacía o nula aquí
                     alert("Sin transmision");
                    }
                   else{
                   
                    var jsonData = JSON.parse(response);
                    var prettyJson = JSON.stringify(jsonData, null, 2);
             
                    returnDetailsDTE(prettyJson, id);
                   
                   }
                
                } catch (error) {
                    // Si hay un error al analizar el JSON, muestra un mensaje de error
                    alert("Error al analizar el JSO00N: " + error.message);
                }
            },
            error: function (xhr, status, error) {
                // Maneja los errores de la solicitud AJAX aquí
                alert("Error en la solicitud A00JAX: " + error);
            }
        });
    }
    
    
    
    function returnDetailsDTE(returns, id){
    
    
    $('#return-contentdte').html(returns);
    
    
    // crear tres botones para ver, descargar y enviar por correo    
    var buttonfooter = "<button type='button' class='btn btn-primary' data-dismiss='modal'>Cerrar</button>"
    var buttonfooter2 = "<button type='button' class='btn btn-primary' data-dismiss='modal'>Enviar</button>"
    //colocar esos dos botones en el footer de la modal
    $('#return-footerdte').html(buttonfooter);
    $('#return-footerdte').append(buttonfooter2);
    
    var btnReenviar = $("<input>", {
        type: "button",
        value: "Reenviar",
        click: function() {
            reenviarDTE(id); // Llama a la función 'reenviarDTE' pasando el valor 'id'
        }
    }).addClass("btn btn-primary"); // Agrega clases de Bootstrap
    
    
    $('#return-footerdte').html(btnReenviar);
    
    $('#return-detailsdte').modal('show');
    }
    
    
    function reenviarDTE(id) {
 
        $.ajax({
            url: 'sexcluded/reenviardte',
            type: 'POST',
            data: { id: id }, // Datos a enviar (el ID)
            success: function (response) {
                try {
              alert(response);
              console.log(response);
             
                } catch (error) {
                    // Si hay un error al analizar el JSON, muestra un mensaje de error
                    alert("Error al analizar el JSO00N: " + error.message);
                  
                } 
            },
            error: function (xhr, status, error) {
                // Maneja los errores de la solicitud AJAX aquí
                alert("Error en la solicitud A00JAX: " + error);
              
            }
        });
    }


       
function dwdte(id) {
    $.ajax({
        url: 'sexcluded/dwdte',
        type: 'POST',
        data: { id: id }, // Datos a enviar (el ID)
        success: function (response) {
            try {
                // Verifica si la respuesta es válida (por ejemplo, es un texto)
                if (typeof response === 'string') {
                    // Crea un enlace para descargar el archivo
                    const blob = new Blob([response], { type: 'text/plain' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'sexcluded_json_' + new Date().toISOString().replace(/[:.]/g, '') + '.txt'; ; // Nombre del archivo
                    document.body.appendChild(a);
                    a.click(); // Inicia la descarga
                    window.URL.revokeObjectURL(url);
                } else {
                    alert('La respuesta no es un texto válido.');
                }
            } catch (error) {
                // Si hay un error al procesar la respuesta, muestra un mensaje de error
                alert("Error al procesar la respuesta: " + error.message);
            }
        },
        error: function (xhr, status, error) {
            // Maneja los errores de la solicitud AJAX aquí
            alert("Error en la solicitud AJAX: " + error);
        }
    });
}
</script>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home2/mrjbinve/public_html/factue/resources/views/sexcluded/index.blade.php ENDPATH**/ ?>