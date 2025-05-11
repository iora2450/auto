 <?php $__env->startSection('content'); ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>
<section class="forms">
    <div class="container-fluid">
        <div class="row"> 
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4><?php echo e(trans('file.Update Supplier')); ?></h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <?php echo Form::open(['route' => ['supplier.update', $lims_supplier_data->id], 'method' => 'put', 'files' => true]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.name')); ?> *</strong> </label>
                                    <input type="text" name="name" value="<?php echo e($lims_supplier_data->name); ?>" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Image')); ?></label>
                                    <input type="file" name="image" class="form-control">
                                    <?php if($errors->has('image')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('image')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">   
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Commercial Name')); ?> *</label>
                                    <input type="text" name="company_name" value="<?php echo e($lims_supplier_data->company_name); ?>" required class="form-control">
                                    <?php if($errors->has('company_name')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('company_name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Email')); ?> *</label>
                                    <input type="email" name="email" value="<?php echo e($lims_supplier_data->email); ?>" required class="form-control">
                                    <?php if($errors->has('email')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('email')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.VAT Number')); ?> *</label>
                                    <input type="text" name="vat_number" value="<?php echo e($lims_supplier_data->vat_number); ?>" class="form-control">
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Nit')); ?> *</label>
                                    <input type="text" name="nit" value="<?php echo e($lims_supplier_data->nit); ?>" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Phone Number')); ?> *</label>
                                    <input type="text" name="phone_number" value="<?php echo e($lims_supplier_data->phone_number); ?>" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Address')); ?> *</label>
                                    <input type="text" name="address" value="<?php echo e($lims_supplier_data->address); ?>" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Country')); ?></label>
                                    <select 
                                        required 
                                        name="country_id" 
                                        id="country_id" 
                                        class="selectpicker form-control" 
                                        data-live-search="true" 
                                        data-live-search-style="begins" 
                                        title="Select country..."
                                    >
                                        <?php $country = []; ?>
                                        <?php $__currentLoopData = $lims_supplier_country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $country[$country->id] = $country->name; ?>
                                            <?php
                                                if($country->id == $lims_supplier_data->country_id ){
                                            ?>       
                                            <option selected value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                            <?php
                                                }else{
                                            ?>
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                            <?php
                                                }
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                        
                                    </select>
                                </div>                        
                            </div>
                            <div id="unit" class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-5"> 
                                        <div class="form-group">
                                            <label><?php echo e(trans('file.Postal Code')); ?> *</label>
                                            <select 
                                                required 
                                                name="gire_id" 
                                                id="gire_id" 
                                                class="selectpicker form-control" 
                                                data-live-search="true" 
                                                data-live-search-style="begins" 
                                                title="Select client..."
                                            >
                                                <?php $gyre = []; ?> 
                                                <?php $__currentLoopData = $lims_supplier_gyre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gyre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $gyre[$gyre->id] = $gyre->name; ?>
                                                    <?php
                                                        if($gyre->id == $lims_supplier_data->gire_id ){
                                                    ?>       
                                                    <option selected value="<?php echo e($gyre->id); ?>"><?php echo e($gyre->name); ?></option>
                                                    <?php
                                                        }else{
                                                    ?>
                                                    <option value="<?php echo e($gyre->id); ?>"><?php echo e($gyre->name); ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                        
                                            </select>
                                        </div>
                                    </div>                      
                                    <div class="col-md-3">
                                        <label><?php echo e(trans('file.City')); ?> *</strong> </label>
                                        <div class="input-group">
                                            <select required class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select unit..." name="state_id">
                                                <?php $state = []; ?>
                                                <?php $__currentLoopData = $lims_supplier_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $state[$state->id] = $state->name; ?>
                                                    <?php
                                                        if($state->id == $lims_supplier_data->state_id ){
                                                    ?>       
                                                    <option selected value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                                                    <?php
                                                        }else{
                                                    ?>
                                                    <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <input type="hidden" name="unit" value="<?php echo e($state->id); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><?php echo e(trans('file.Municipality')); ?></strong> </label>
                                            <div class="input-group">
                                                <input type="hidden" name="municipality" value="<?php echo e($lims_supplier_data->municipality_id); ?>">
                                                <select 
                                                    name="municipality_id" 
                                                    class="selectpicker form-control" 
                                                    data-live-search="true" 
                                                    data-live-search-style="begins" 
                                                    title="Select Municipio..."
                                                >
                                                    <?php $__currentLoopData = $lims_supplier_municipality; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($municipality->id); ?>"><?php echo e($municipality->name); ?> | <?php echo e($municipality->state->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>             
                                </div>                                
                            </div>
                           
                            <div class="col-md-12">
                                <div class="form-group mt-3">
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

    var municipality = $("input[name='municipality']").val();
    $('select[name=municipality_id]').val(municipality); 

</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp7433\htdocs\frutas\resources\views/supplier/edit.blade.php ENDPATH**/ ?>