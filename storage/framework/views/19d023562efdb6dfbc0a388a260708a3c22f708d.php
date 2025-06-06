 <?php $__env->startSection('content'); ?>
<?php if(session()->has('not_permitted')): ?>
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><?php echo e(session()->get('not_permitted')); ?></div> 
<?php endif; ?>
<?php if(session()->has('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4><?php echo e(trans('file.Add Customer')); ?></h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small><?php echo e(trans('file.The field labels marked with * are required input fields')); ?>.</small></p>
                        <?php echo Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Customer Group')); ?> *</strong> </label>
                                    <select required class="form-control selectpicker" id="customer-group-id" name="customer_group_id" onchange='saveValue(this);'>
                                        <?php $__currentLoopData = $lims_customer_group_all; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer_group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($customer_group->id); ?>"><?php echo e($customer_group->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Type of Taxpayer')); ?> *</strong> </label>
                                    <select required class="form-control selectpicker" id="type_taxpayer_id" name="type_taxpayer_id" onchange='saveValue(this);'>
                                        <?php $__currentLoopData = $lims_type_taxpayer_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxpayer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($taxpayer->id); ?>"><?php echo e($taxpayer->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Code')); ?> *</strong> </label>
                                    <input type="text" id="code" name="code" required class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.name')); ?> *</strong> </label>
                                    <input type="text" id="name" name="customer_name" required class="form-control" onkeyup='saveValue(this);'>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Commercial Name')); ?> *</label>
                                    <input type="text" name="company_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Email')); ?></label>
                                    <input type="email" name="email" placeholder="example@example.com" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Phone Number')); ?> *</label>
                                    <input type="text" name="phone_number" required class="form-control">
                                    <?php if($errors->has('phone_number')): ?>
                                   <span>
                                       <strong><?php echo e($errors->first('phone_number')); ?></strong>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Tax Number')); ?></label>
                                    <input type="text" name="tax_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Nit')); ?></label>
                                    <input type="text" name="nit" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Address')); ?> *</label>
                                    <input type="text" name="address" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Country')); ?> *</label>
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
                                            <option value="<?php echo e($country->id); ?>"><?php echo e($country->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>                                        
                                    </select>
                                </div>                        
                            </div>  

                            <div id="unit" class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-5">
                                        <label><?php echo e(trans('file.Postal Code')); ?> *</strong> </label>                                        
                                        <div class="input-group">
                                            <select class="form-control selectpicker"  data-live-search="true" data-live-search-style="begins" name="gire_id"> 
                                                <?php $__currentLoopData = $lims_customer_gyre; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gyre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($gyre->id); ?>"><?php echo e($gyre->name); ?> | <?php echo e($gyre->code); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>         
                                     <div class="col-md-3">
                                        <label><?php echo e(trans('file.Department')); ?> *</strong> </label>
                                        <div class="input-group">
                                            <select class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select depto..." name="state_id">
                                                <?php $__currentLoopData = $lims_customer_state; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($state->id); ?>"><?php echo e($state->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label><?php echo e(trans('file.Municipality')); ?></strong> </label>                                        
                                        <div class="input-group">
                                            <select class="form-control selectpicker"  data-live-search="true" data-live-search-style="begins" name="municipality_id"> 
                                                <?php $__currentLoopData = $lims_customer_municipalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $muni): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($muni->id); ?>"><?php echo e($muni->name); ?> | <?php echo e($muni->state->name); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>                      
                                </div>                                
                            </div>           
                            
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label><?php echo e(trans('file.Add User')); ?></label>&nbsp;
                                    <input type="checkbox" name="user" value="1" />
                                </div>
                            </div>

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
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="pos" value="0">
                            <input type="submit" value="<?php echo e(trans('file.submit')); ?>" class="btn btn-primary">
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
    $("ul#people #customer-create-menu").addClass("active");

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

    //$("#name").val(getSavedValue("name"));
    //$("#customer-group-id").val(getSavedValue("customer-group-id"));

    function saveValue(e) {
        var id = e.id;  // get the sender's id to save it.
        var val = e.value; // get the value.
        localStorage.setItem(id, val);// Every time user writing something, the localStorage's value will override.
    }
    //get the saved value function - return the value of "v" from localStorage. 
    function getSavedValue  (v){
        if (!localStorage.getItem(v)) {
            return "";// You can change this to your defualt value. 
        }
        return localStorage.getItem(v);
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/tramites/public_html/auto/resources/views/customer/create.blade.php ENDPATH**/ ?>