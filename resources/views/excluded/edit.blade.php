@extends('layout.main') @section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>{{trans('file.Update Excluded')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                        {!! Form::open(['route' => ['excluded.update', $lims_excluded_data->id], 'method' => 'put', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.name')}} *</strong> </label>
                                    <input type="text" name="name" value="{{$lims_excluded_data->name}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Email')}} *</label>
                                    <input type="email" name="email" value="{{$lims_excluded_data->email}}" required class="form-control">
                                    @if($errors->has('email'))
                                   <span>
                                       <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.DUI')}}</label>
                                    <input type="text" name="dui" value="{{$lims_excluded_data->dui}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.NIT')}}</label>
                                    <input type="text" name="nit" value="{{$lims_excluded_data->nit}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{trans('file.Phone Number')}} *</label>
                                    <input type="text" name="phone" value="{{$lims_excluded_data->phone}}" required class="form-control">
                                </div>
                            </div>
                           
                            <div id="unit" class="col-md-12">
                                <div class="row ">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Address')}} *</label>
                                            <input type="text" name="address" value="{{$lims_excluded_data->address}}" required class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="unit" class="col-md-12">
                                <div class="row ">      
                                    <div class="col-md-6">
                                        <label>{{trans('file.City')}} *</strong> </label>
                                        <div class="input-group">
                                            <select required class="form-control selectpicker" data-live-search="true" data-live-search-style="begins" title="Select unit..." name="state_id">
                                                <?php $state = []; ?>
                                                @foreach($lims_excluded_state as $state)
                                                    <?php $state[$state->id] = $state->name; ?>
                                                    <?php
                                                        if($state->id == $lims_excluded_data->state_id ){
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{trans('file.Municipality')}}</strong> </label>
                                            <div class="input-group">
                                                <input type="hidden" name="municipality" value="{{ $lims_excluded_data->municipality_id}}">
                                                <select 
                                                    name="municipality_id" 
                                                    class="selectpicker form-control" 
                                                    data-live-search="true" 
                                                    data-live-search-style="begins" 
                                                    title="Select Municipio..."
                                                >
                                                    @foreach($lims_excluded_municipality as $municipality)
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
