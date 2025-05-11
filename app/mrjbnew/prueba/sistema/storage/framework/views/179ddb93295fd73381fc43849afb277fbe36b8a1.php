 <?php $__env->startSection('content'); ?> 
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4><?php echo e(trans('file.Add Type Document')); ?></h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <?php echo Form::open(['route' => 'typeDocument.store', 'method' => 'post']); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Document')); ?> *</strong> </label>
                                    <input type="text" name="documento" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Resolution')); ?> *</strong> </label>
                                    <input type="text" name="resolucion" required class="form-control">
                                </div>
                            </div>

                             <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Serie')); ?> *</strong> </label>
                                    <input type="text" name="serie" required class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Correlativ')); ?> *</label>
                                    <input type="number" name="correlativo" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Module')); ?> *</label>
                                    <input type="text" name="modulo" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mt-4">
                                    <input type="submit" value="<?php echo e(trans('file.submit')); ?>" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        <?php echo Form::close(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #biller-create-menu").addClass("active");
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp74\htdocs\instruquimica\resources\views/type_document/create.blade.php ENDPATH**/ ?>