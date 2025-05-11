@extends('layout.main') @section('content')
@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div> 
@endif
@if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Customer')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'customer.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Customer Group')}} *</strong> </label>
                                    <select required class="form-control selectpicker" id="customer-group-id" name="customer_group_id" onchange='saveValue(this);'>
                                        @foreach($lims_customer_group_all as $customer_group)
                                            <option value="{{$customer_group->id}}">{{$customer_group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                           
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Type of Taxpayer')}} *</strong> </label>
                                    <select required class="form-control selectpicker" id="type_taxpayer_id" name="type_taxpayer_id" onchange='saveValue(this);'>
                                        @foreach($lims_type_taxpayer_list as $taxpayer)
                                            <option value="{{$taxpayer->id}}">{{$taxpayer->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Code')}} *</strong> </label>
                                    <input type="text" id="code" name="code" required class="form-control" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" id="name" name="customer_name" required class="form-control" onkeyup='saveValue(this);'>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Commercial Name')}} *</label>
                                    <input type="text" name="company_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}}</label>
                                    <input type="email" name="email" placeholder="example@example.com" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" required class="form-control">
                                    @if($errors->has('phone_number'))
                                   <span>
                                       <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Tax Number')}}</label>
                                    <input type="text" name="tax_no" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Nit')}}</label>
                                    <input type="text" name="nit" value="" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Address')}} *</label>
                                    <input type="text" name="address" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Country')}} *</label>
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
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach                                        
                                    </select>
                                </div>                        
                            </div>  

                            <div id="unit" class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-5">
                                        <label>{{trans('file.Postal Code')}} *</strong> </label>                                        
                                        <div class="input-group">
                                            <select class="form-control selectpicker"  data-live-search="true" data-live-search-style="begins" name="gire_id"> 
                                                @foreach($lims_customer_gyre as $gyre)
                                                    <option value="{{$gyre->id}}">{{$gyre->name}} | {{$gyre->code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>         
                                     <div class="col-md-3">
                                        <label>{{trans('file.Department')}} *</strong> </label>
                                        <div class="input-group">
                                            <select class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select depto..." name="state_id">
                                                @foreach($lims_customer_state as $state)
                                                    <option value="{{$state->id}}">{{$state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{trans('file.Municipality')}}</strong> </label>                                        
                                        <div class="input-group">
                                            <select class="form-control selectpicker"  data-live-search="true" data-live-search-style="begins" name="municipality_id"> 
                                                @foreach($lims_customer_municipalities as $muni)
                                                    <option value="{{$muni->id}}">{{$muni->name}} | {{$muni->state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>                      
                                </div>                                
                            </div>           
                            
                            <div class="col-md-6 mt-3">
                                <div class="form-group">
                                    <label>{{trans('file.Add User')}}</label>&nbsp;
                                    <input type="checkbox" name="user" value="1" />
                                </div>
                            </div>

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
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="pos" value="0">
                            <input type="submit" value="{{trans('file.submit')}}" class="btn btn-primary">
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
@endsection