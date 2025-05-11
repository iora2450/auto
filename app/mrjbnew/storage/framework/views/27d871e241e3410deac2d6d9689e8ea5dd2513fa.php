 <?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
    
            <div class="card-body">
              <?php
                if($general_setting->theme == 'default.css'){
                  $color = '#733686';
                        $color_rgba = 'rgba(115, 54, 134, 0.8)';
                }
                elseif($general_setting->theme == 'green.css'){
                        $color = '#2ecc71';
                        $color_rgba = 'rgba(46, 204, 113, 0.8)';
                    }
                    elseif($general_setting->theme == 'blue.css'){
                        $color = '#3498db';
                        $color_rgba = 'rgba(52, 152, 219, 0.8)';
                    }
                    elseif($general_setting->theme == 'dark.css'){
                        $color = '#34495e';
                        $color_rgba = 'rgba(52, 73, 94, 0.8)';
                    }
                    $color_rgba_anterior = 'rgba(46, 204, 113, 0.8)';
                    $color_rgba_diferencia = 'rgba(52, 152, 219, 0.8)';
                 ?>


  <div class="container-fluid">
        <div class="card">
            <div class="card-header mt-2">
                <h3 class="text-center">Grafico de ventas</h3>
            </div>
            <?php echo Form::open(['route' => 'report.SalesGraph', 'method' => 'get']); ?>

            <div class="row mb-3">
                <div class="col-md-5 offset-md-2 mt-3">
                    <div class="form-group row">
                     
                        <div class="d-tc">
                            <div class="input-group">
                                   <select name='billers' id='billers'>
                                  <option value="">Todos los vendedores</option>
                                  <?php $__currentLoopData = $datos_billers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <?php if($key->id == $billers): ?>
                                  <option selected value='<?php echo e($key->id); ?>'><?php echo e($key->name); ?></option>
                                  <?php endif; ?>
                                    <?php if($key->id != $billers): ?>
                                  <option value='<?php echo e($key->id); ?>'><?php echo e($key->name); ?></option>
                                  <?php endif; ?>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                               
                            </div>
                        </div>

                       <div class="d-tc">
                            <div class="input-group">
                              <select name='brands' id='brands'>
                                  <option value="">Todas las marcas</option>
                                  <?php $__currentLoopData = $datos_brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                   <?php if($key->id == $brands): ?>
                                  <option selected value='<?php echo e($key->id); ?>'><?php echo e($key->title); ?></option>
                                  <?php endif; ?>
                                  <?php if($key->id != $brands): ?>
                                  <option value='<?php echo e($key->id); ?>'><?php echo e($key->title); ?></option>
                                  <?php endif; ?>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                               
                            </div>
                        </div>


               <div class="d-tc">
                            <div class="input-group">
                              <select name='clientes' id='clientes'>
                                  <option value="">Todos los clientes</option>
                                  <?php $__currentLoopData = $datos_customer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                   <?php if($key->id == $brands): ?>
                                  <option selected value='<?php echo e($key->id); ?>'><?php echo e($key->name); ?></option>
                                  <?php endif; ?>
                                  <?php if($key->id != $brands): ?>
                                  <option value='<?php echo e($key->id); ?>'><?php echo e($key->name); ?></option>
                                  <?php endif; ?>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                              </select>
                               
                            </div>
                        </div>




                    </div>
                </div>
               
                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit"><?php echo e(trans('file.submit')); ?></button>
                    </div>
                </div>
            </div>
            <?php echo Form::close(); ?>

        </div>
    </div>



         
                <canvas id="salesGraph" data-color="<?php echo e($color); ?>" 
                data-color_rgba_anterior="<?php echo e($color_rgba_anterior); ?>" 
                data-color_rgba_diferencia="<?php echo e($color_rgba_diferencia); ?>"
                data-color_rgba="<?php echo e($color_rgba); ?>" data-product = "<?php echo e(json_encode($datos2)); ?>" data-sold_venta="<?php echo e(json_encode($datos3)); ?>"
                data-sold_venta_anterior="<?php echo e(json_encode($datos3_anterior)); ?>"
                 data-sold_venta_diferencia="<?php echo e(json_encode($datos3_diferencia)); ?>"
                 ></canvas>
            </div>
        </div>
  </div>
</div>

<script type="text/javascript">

  $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #best-seller-report-menu").addClass("active");

  $('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
  $('.selectpicker').selectpicker('refresh');

  $('#warehouse_id').on("change", function(){
    $('#report-form').submit();
  });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home2/mrjbinve/public_html/sistema/resources/views/report/sales_graph.blade.php ENDPATH**/ ?>