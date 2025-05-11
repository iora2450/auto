@extends('layout.main')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card-body">
                @php
                if($general_setting->theme == 'default.css'){
                    $color = '#733686';
                        $color_rgba = 'rgba(115, 54, 134, 0.8)';
                }elseif($general_setting->theme == 'green.css'){
                    $color = '#2ecc71';
                    $color_rgba = 'rgba(46, 204, 113, 0.8)';
                }elseif($general_setting->theme == 'blue.css'){
                    $color = '#3498db';
                    $color_rgba = 'rgba(52, 152, 219, 0.8)';
                }elseif($general_setting->theme == 'dark.css'){
                    $color = '#34495e';
                    $color_rgba = 'rgba(52, 73, 94, 0.8)';
                }
                    $color_rgba_anterior = 'rgba(46, 204, 113, 0.8)';
                    $color_rgba_diferencia = 'rgba(52, 152, 219, 0.8)';
                @endphp

                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header mt-2">
                            <h3 class="text-center">Grafico de pedidos</h3>
                        </div>
                        {!! Form::open(['route' => 'report.OrdersGraph', 'method' => 'get']) !!}
                        <div class="row mb-3">
                <div class="col-md-5 offset-md-2 mt-3">
                    <div class="form-group row">

                        <div class="d-tc">
                            <div class="input-group">
                                   <select name='billers' id='billers'>
                                  <option value="">Todos los vendedores</option>
                                  @foreach($datos_billers as $key)
                                  @if($key->id == $billers)
                                  <option selected value='{{$key->id}}'>{{$key->name}}</option>
                                  @endif
                                    @if($key->id != $billers)
                                  <option value='{{$key->id}}'>{{$key->name}}</option>
                                  @endif
                                  @endforeach
                              </select>

                            </div>
                        </div>

                       <div class="d-tc">
                            <div class="input-group">
                              <select name='brands' id='brands'>
                                  <option value="">Todas las marcas</option>
                                  @foreach($datos_brands as $key)
                                   @if($key->id == $brands)
                                  <option selected value='{{$key->id}}'>{{$key->title}}</option>
                                  @endif
                                  @if($key->id != $brands)
                                  <option value='{{$key->id}}'>{{$key->title}}</option>
                                  @endif
                                  @endforeach
                              </select>

                            </div>
                        </div>


               <div class="d-tc">
                            <div class="input-group">
                              <select name='clientes' id='clientes'>
                                  <option value="">Todos los clientes</option>
                                  @foreach($datos_customer as $key)
                                   @if($key->id == $brands)
                                  <option selected value='{{$key->id}}'>{{$key->name}}</option>
                                  @endif
                                  @if($key->id != $brands)
                                  <option value='{{$key->id}}'>{{$key->name}}</option>
                                  @endif
                                  @endforeach
                              </select>

                            </div>
                        </div>




                    </div>
                </div>

                <div class="col-md-2 mt-3">
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">{{trans('file.submit')}}</button>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>




                <canvas id="salesGraph" data-color="{{$color}}"
                data-color_rgba_anterior="{{$color_rgba_anterior}}"
                data-color_rgba_diferencia="{{$color_rgba_diferencia}}"
                data-color_rgba="{{$color_rgba}}" data-product = "{{json_encode($datos2)}}" data-sold_venta="{{json_encode($datos3)}}"
                data-sold_venta_anterior="{{json_encode($datos3_anterior)}}"
                 data-sold_venta_diferencia="{{json_encode($datos3_diferencia)}}"
                 ></canvas>
            </div>
        </div>
  </div>
</div>

<script type="text/javascript">

  $("ul#report").siblings('a').attr('aria-expanded','true');
    $("ul#report").addClass("show");
    $("ul#report #best-seller-report-menu").addClass("active");

  $('#warehouse_id').val($('input[name="warehouse_id_hidden"]').val());
  $('.selectpicker').selectpicker('refresh');

  $('#warehouse_id').on("change", function(){
    $('#report-form').submit();
  });
</script>
@endsection