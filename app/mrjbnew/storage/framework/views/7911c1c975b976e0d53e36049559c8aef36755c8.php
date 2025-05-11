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
                        <h4><?php echo e(trans('file.Update Customer')); ?></h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <?php echo Form::open(['route' => ['customer.update',$lims_customer_data->id], 'method' => 'put', 'files' => true]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="customer_group" value="<?php echo e($lims_customer_data->customer_group_id); ?>">
                                    <label><?php echo e(trans('file.Customer Group')); ?> *</strong> </label>
                                    <select required class="form-control selectpicker" name="customer_group_id">
                                        <?php $__currentLoopData = $lims_customer_group_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer_group->id); ?>"><?php echo e($customer_group->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Type of Taxpayer')); ?> *</strong> </label>
                                    <select required class="form-control selectpicker" name="type_taxpayer_id">
                                        <?php $__currentLoopData = $lims_type_taxpayer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxpayer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                           if($taxpayer->id == $lims_customer_data->type_taxpayer_id  ){
                                        ?>
                                           <option selected value="<?php echo e($taxpayer->id); ?>"><?php echo e($taxpayer->name); ?></option>
                                        <?php }else{ ?>
                                           <option value="<?php echo e($taxpayer->id); ?>"><?php echo e($taxpayer->name); ?></option>
                                        <?php } ?>                                            
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Code')); ?> *</strong> </label>
                                    <input type="text" id="code" name="code" required="true" class="form-control" value="<?php echo e($lims_customer_data->code); ?>">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.name')); ?> *</strong> </label>
                                    <input type="text" name="customer_name" value="<?php echo e($lims_customer_data->name); ?>" required class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Commercial Name')); ?> </label>
                                    <input type="text" name="company_name" value="<?php echo e($lims_customer_data->company_name); ?>" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Email')); ?></label>
                                    <input type="email" name="email" value="<?php echo e($lims_customer_data->email); ?>" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Phone Number')); ?> *</label>
                                    <input type="text" name="phone_number" required value="<?php echo e($lims_customer_data->phone_number); ?>" class="form-control">
                                    <?php if($errors->has('phone_number')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('phone_number')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Tax Number')); ?> *</label>
                                    <input type="text" name="tax_no" class="form-control" value="<?php echo e($lims_customer_data->tax_no); ?>">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Nit')); ?> *</label>
                                    <input type="text" name="nit" class="form-control" value="<?php echo e($lims_customer_data->nit); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Address')); ?> *</label>
                                    <input type="text" name="address" required value="<?php echo e($lims_customer_data->address); ?>" class="form-control">
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
                                        <?php $__currentLoopData = $lims_customer_country; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $country[$country->id] = $country->name; ?>
                                            <?php
                                                if($country->id == $lims_customer_data->country_id ){
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
                                            <label><?php echo e(trans('file.Postal Code')); ?></label>
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
                                                <?php $__currentLoopData = $lims_customer_gyre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gyre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $gyre[$gyre->id] = $gyre->name; ?>
                                                    <?php
                                                        if($gyre->id == $lims_customer_data->gire_id ){
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
                                                <?php $__currentLoopData = $lims_customer_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <?php $state[$state->id] = $state->name; ?>
                                                    <?php
                                                        if($state->id == $lims_customer_data->state_id ){
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
                                                <input type="hidden" name="municipality" value="<?php echo e($lims_customer_data->municipality_id); ?>">
                                                <select 
                                                    name="municipality_id" 
                                                    class="selectpicker form-control" 
                                                    data-live-search="true" 
                                                    data-live-search-style="begins" 
                                                    title="Select Municipio..."
                                                >
                                                    <?php $__currentLoopData = $lims_customer_municipality; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($municipality->id); ?>"><?php echo e($municipality->name); ?> | <?php echo e($municipality->state->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>             
                                </div>                                
                            </div>
                                                        
                            <?php if(!$lims_customer_data->user_id): ?>
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Add User')); ?></label>&nbsp;
                                    <input type="checkbox" name="user" value="1" />
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.UserName')); ?> *</label>
                                    <input type="text" name="name" class="form-control">
                                    <?php if($errors->has('name')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('name')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Password')); ?> *</label>
                                    <input type="password" name="password" class="form-control">
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

    $(".user-input").hide();

    $('input[name="user"]').on('change', function() {
        if ($(this).is(':checked')) {
            $('.user-input').show(300);
            $('input[name="name"]').prop('required',true);
            $('input[name="password"]').prop('required',true);
        }
        else{
            $('.user-input').hide(300);
            $('input[name="name"]').prop('required',false);
            $('input[name="password"]').prop('required',false);
        }
    });
        
    var customer_group = $("input[name='customer_group']").val();
    $('select[name=customer_group_id]').val(customer_group);
    
    var municipality = $("input[name='municipality']").val();
    $('select[name=municipality_id]').val(municipality);     
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ltygkxsm/public_html/mrjb/resources/views/customer/edit.blade.php ENDPATH**/ ?>