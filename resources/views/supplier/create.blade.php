@extends('layout.main') 
@section('content')
@if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        {{ session()->get('not_permitted') }}
    </div> 
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Add Supplier')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => 'supplier.store', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="name" required class="form-control">
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
                                    <input type="text" name="company_name" required class="form-control">
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
                                    <input type="email" name="email" placeholder="example@example.com" required class="form-control">
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
                                    <input type="text" name="vat_number" placeholder="0000000" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Nit')}} *</label>
                                    <input type="text" name="nit" placeholder="06140000001110" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone_number"placeholder="22452292" required class="form-control">
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
                                        @foreach($lims_supplier_country as $country)
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
                                                @foreach($lims_supplier_gyre as $gyre)
                                                    <option value="{{$gyre->id}}">{{$gyre->name}} | {{$gyre->code}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>         
                                     <div class="col-md-3">
                                        <label>{{trans('file.Department')}} *</strong> </label>
                                        <div class="input-group">
                                            <select class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select depto..." name="state_id">
                                                @foreach($lims_supplier_state as $state)
                                                    <option value="{{$state->id}}">{{$state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>{{trans('file.Municipality')}}</strong> </label>                                        
                                        <div class="input-group">
                                            <select class="form-control selectpicker"  data-live-search="true" data-live-search-style="begins" name="municipality_id"> 
                                                @foreach($lims_supplier_municipalities as $muni)
                                                    <option value="{{$muni->id}}">{{$muni->name}} | {{$muni->state->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>                      
                                </div>                                
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group mt-4">
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
    $("ul#people #supplier-create-menu").addClass("active"); 
</script>
@endsection