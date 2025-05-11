@extends('layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row"> 
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Update Customer')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['customer.update',$lims_customer_data->id], 'method' => 'put', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="hidden" name="customer_group" value="{{$lims_customer_data->customer_group_id}}">
                                    <label>{{trans('file.Customer Group')}} *</strong> </label>
                                    <select required class="form-control selectpicker" name="customer_group_id">
                                        @foreach($lims_customer_group_all as $customer_group)
                                            <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Type of Taxpayer')}} *</strong> </label>
                                    <select required class="form-control selectpicker" name="type_taxpayer_id">
                                        @foreach($lims_type_taxpayer_list as $taxpayer)
                                        <?php
                                           if($taxpayer->id == $lims_customer_data->type_taxpayer_id  ){
                                        ?>
                                           <option selected value="{{$taxpayer->id}}">{{$taxpayer->name}}</option>
                                        <?php }else{ ?>
                                           <option value="{{$taxpayer->id}}">{{$taxpayer->name}}</option>
                                        <?php } ?>                                            
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Code')}} *</strong> </label>
                                    <input type="text" id="code" name="code" required="true" class="form-control" value="{{$lims_customer_data->code}}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="customer_name" value="{{$lims_customer_data->name}}" required class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Commercial Name')}} </label>
                                    <input type="text" name="company_name" value="{{$lims_customer_data->company_name}}" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}}</label>
                                    <input type="email" name="email" value="{{$lims_customer_data->email}}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" required value="{{$lims_customer_data->phone_number}}" class="form-control">
                                    @if($errors->has('phone_number'))
                                   <span>
                                       <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Tax Number')}} *</label>
                                    <input type="text" name="tax_no" class="form-control" value="{{$lims_customer_data->tax_no}}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Nit')}} *</label>
                                    <input type="text" name="nit" class="form-control" value="{{$lims_customer_data->nit}}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Address')}} *</label>
                                    <input type="text" name="address" required value="{{$lims_customer_data->address}}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Country')}}</label>
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
                                        @foreach($lims_customer_country as $country)
                                            <?php $country[$country->id] = $country->name; ?>
                                            <?php
                                                if($country->id == $lims_customer_data->country_id ){
                                            ?>       
                                            <option selected value="{{$country->id}}">{{$country->name}}</option>
                                            <?php
                                                }else{
                                            ?>
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                            <?php
                                                }
                                            ?>
                                        @endforeach                                        
                                    </select>
                                </div>                        
                            </div>

                            <div id="unit" class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>{{trans('file.Postal Code')}}</label>
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
                                                @foreach($lims_customer_gyre as $gyre)
                                                    <?php $gyre[$gyre->id] = $gyre->name; ?>
                                                    <?php
                                                        if($gyre->id == $lims_customer_data->gire_id ){
                                                    ?>       
                                                    <option selected value="{{$gyre->id}}">{{$gyre->name}}</option>
                                                    <?php
                                                        }else{
                                                    ?>
                                                    <option value="{{$gyre->id}}">{{$gyre->name}}</option>
                                                    <?php
                                                        }
                                                    ?>
                                                @endforeach                                        
                                            </select>
                                        </div>                        
                                    </div>
                                    <div class="col-md-3">
                                        <label>{{trans('file.City')}} *</strong> </label>
                                        <div class="input-group">
                                            <select required class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select unit..." name="state_id">
                                                <?php $state = []; ?>
                                                @foreach($lims_customer_state as $state)
                                                    <?php $state[$state->id] = $state->name; ?>
                                                    <?php
                                                        if($state->id == $lims_customer_data->state_id ){
                                                    ?>       
                                                    <option selected value="{{$state->id}}">{{$state->name}}</option>
                                                    <?php
                                                        }else{
                                                    ?>
                                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                                    <?php
                                                        }
                                                    ?>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="unit" value="{{ $state->id}}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{trans('file.Municipality')}}</strong> </label>
                                            <div class="input-group">
                                                <input type="hidden" name="municipality" value="{{ $lims_customer_data->municipality_id}}">
                                                <select 
                                                    name="municipality_id" 
                                                    class="selectpicker form-control" 
                                                    data-live-search="true" 
                                                    data-live-search-style="begins" 
                                                    title="Select Municipio..."
                                                >
                                                    @foreach($lims_customer_municipality as $municipality)
                                                        <option value="{{$municipality->id}}">{{$municipality->name}} | {{$municipality->state->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>             
                                </div>                                
                            </div>
                                                        
                            @if(!$lims_customer_data->user_id)
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label>{{trans('file.Add User')}}</label>&nbsp;
                                    <input type="checkbox" name="user" value="1" />
                                </div>
                            </div>
                            @endif

                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label>{{trans('file.UserName')}} *</label>
                                    <input type="text" name="name" class="form-control">
                                    @if($errors->has('name'))
                                   <span>
                                       <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 user-input">
                                <div class="form-group">
                                    <label>{{trans('file.Password')}} *</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mt-3">
                                    <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
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
@endsection