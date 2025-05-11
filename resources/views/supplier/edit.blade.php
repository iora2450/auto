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
                        <h4>{{trans('file.Update Supplier')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['supplier.update', $lims_supplier_data->id], 'method' => 'put', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="name" value="{{$lims_supplier_data->name}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Image')}}</label>
                                    <input type="file" name="image" class="form-control">
                                    @if($errors->has('image'))
                                   <span>
                                       <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">   
                                <div class="form-group">
                                    <label>{{trans('file.Commercial Name')}} *</label>
                                    <input type="text" name="company_name" value="{{$lims_supplier_data->company_name}}" required class="form-control">
                                    @if($errors->has('company_name'))
                                   <span>
                                       <strong>{{ $errors->first('company_name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}} *</label>
                                    <input type="email" name="email" value="{{$lims_supplier_data->email}}" required class="form-control">
                                    @if($errors->has('email'))
                                   <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.VAT Number')}} *</label>
                                    <input type="text" name="vat_number" value="{{$lims_supplier_data->vat_number}}" class="form-control">
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Nit')}} *</label>
                                    <input type="text" name="nit" value="{{$lims_supplier_data->nit}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number" value="{{$lims_supplier_data->phone_number}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Address')}} *</label>
                                    <input type="text" name="address" value="{{$lims_supplier_data->address}}" required class="form-control">
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
                                        @foreach($lims_supplier_country as $country)
                                            <?php $country[$country->id] = $country->name; ?>
                                            <?php
                                                if($country->id == $lims_supplier_data->country_id ){
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
                                            <label>{{trans('file.Postal Code')}} *</label>
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
                                                @foreach($lims_supplier_gyre as $gyre)
                                                    <?php $gyre[$gyre->id] = $gyre->name; ?>
                                                    <?php
                                                        if($gyre->id == $lims_supplier_data->gire_id ){
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
                                                @foreach($lims_supplier_state as $state)
                                                    <?php $state[$state->id] = $state->name; ?>
                                                    <?php
                                                        if($state->id == $lims_supplier_data->state_id ){
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
                                                <input type="hidden" name="municipality" value="{{ $lims_supplier_data->municipality_id}}">
                                                <select 
                                                    name="municipality_id" 
                                                    class="selectpicker form-control" 
                                                    data-live-search="true" 
                                                    data-live-search-style="begins" 
                                                    title="Select Municipio..."
                                                >
                                                    @foreach($lims_supplier_municipality as $municipality)
                                                        <option value="{{$municipality->id}}">{{$municipality->name}} | {{$municipality->state->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>             
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

    var municipality = $("input[name='municipality']").val();
    $('select[name=municipality_id]').val(municipality); 

</script>
@endsection
