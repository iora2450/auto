@extends('layout.main') @section('content')
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
                        <h4>{{trans('file.Update Type Document')}}</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic">
                            <small>{{trans('file.The field labels marked with * are required input fields')}}.</small>
                        </p>
                        {!! Form::open(['route' => ['typeDocument.update',$lims_typedocument_data->id], 'method' => 'put', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Document')}} *</strong></label>
                                    <input type="text" id="documento" name="documento" required="true" class="form-control" value="{{$lims_typedocument_data->documento}}">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Resolution')}} *</strong> </label>
                                    <input type="text" name="resolucion" value="{{$lims_typedocument_data->resolucion}}" required class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Serie')}} </label>
                                    <input type="text" name="serie" value="{{$lims_typedocument_data->serie}}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Correlativ')}}</label>
                                    <input type="number" name="correlativo" value="{{$lims_typedocument_data->correlativo}}" class="form-control">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{trans('file.Modulo')}} *</label>
                                    <input type="text" name="modulo" required value="{{$lims_typedocument_data->modulo}}" class="form-control">
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

    $("ul#setting").siblings('a').attr('aria-expanded','true');
    $("ul#setting").addClass("show");
    $("ul#setting #typedocument-menu").addClass("active");
        
</script>
@endsection