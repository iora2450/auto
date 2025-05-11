@extends('layout.main') 

@section('content')

@if(session()->has('not_permitted'))
    <div class="alert alert-danger alert-dismissible text-center">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>{{ session()->get('not_permitted') }}
    </div> 
@endif

<section class="forms">
    <div class="container-fluid">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>{{trans('file.Add Box Cut')}}</h4>
                </div>
        
                <div class="card-body">
                    <p class="italic"><small>{{trans('file.The field labels marked with * are required input fields')}}.</small></p>
                    
                    <form action="{{ route('boxcuts.store') }}" method="POST" class="payment-form">
                        @csrf
                        
                        <div class="form-group row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{trans('file.Warehouse')}} *</label>
                                    <select required id="warehouse_id" name="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse..">
                                        @foreach($lims_warehouse_list as $warehouse)
                                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{trans('file.Biller')}} *</label>
                                    <select required name="biller_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select Biller...">
                                        @foreach($lims_biller_list as $biller)
                                            <option value="{{$biller->id}}">{{$biller->name . ' (' . $biller->company_name . ')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>                   
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="form-control-label" for="nombre">Producto</label>
                                <select class="form-control selectpicker" name="id_producto" id="id_producto" data-live-search="true">
                                    <option value="0" selected>Seleccione</option>
                                    @foreach($lims_ticket_list as $prod)
                                        <option value="{{$prod->id}}_{{$prod->base_unit}}">{{$prod->producto}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-control-label" for="cantidad">Cantidad</label>
                                <input type="number" id="cantidad" name="cantidad" class="form-control" placeholder="Ingrese cantidad" pattern="[0-9]{0,15}">
                            </div>

                            <div class="col-md-3">
                                <input type="hidden" disabled id="costo" name="costo" class="form-control" placeholder="Precio de costo" >
                            </div>

                           <div class="col-md-3">
                                <label class="form-control-label" for="total_dinero">Precio Compra</label>
                                <input type="hidden" disabled id="total_dinero" name="total_dinero" class="form-control" placeholder="Ingrese precio de compra">
                            </div>
                        </div>

                        <div class="form-group row">                                                       
                            <div class="col-md-3">
                                <button type="button" id="agregar" class="btn btn-primary"> {{__('file.add_product')}}</button>
                            </div>
                        </div>        
                        
                        <br/><br/>

                        <div class="form-group row border">
                            <h3>Lista de Efectivo en Mano</h3>
                            <div class="table-responsive col-md-12">
                                <table id="detalles" class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr class="bg-success">
                                            <th>Eliminar</th>
                                            <th>Moneda</th>
                                            <th>Multiplicador(USD$)</th>
                                            <th>Cantidad</th>
                                            <th>SubTotal (USD$)</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4"><p align="right">TOTAL:</p></th>
                                            <th><p align="right"><span id="total">USD$ 0.00</span> </p></th>
                                        </tr>
                                        {{-- <tr>
                                            <th colspan="4"><p align="right">TOTAL IMPUESTO (13%):</p></th>
                                            <th><p align="right"><span id="total_impuesto">USD$ 0.00</span></p></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4"><p align="right">EXENTO:</p></th>
                                            <th><p align="right"><span id="exentot">USD$ 0.00</span> <input type="hidden" name="total_exento" id="total_exento"> </p></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4"><p align="right">PERCEPCIÓN:</p></th>
                                            <th><p align="right"><span id="total_percepcion">USD$ 0.00</span>  </p></th>
                                        </tr>
                                        <tr>
                                            <th colspan="4"><p align="right">TOTAL PAGAR:</p></th>
                                            <th><p align="right"><span align="right" id="total_pagar_html">USD$ 0.00</span> <input type="hidden" name="total_pagar" id="total_pagar"></p></th>
                                        </tr> --}}
                                    </tfoot>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer form-group row" id="guardar">
                            <div class="col-md">
                                <input type="hidden" name="_token" value="{{csrf_token()}}">
                                <button type="submit" class="btn btn-success"> {{__('file.add_product')}}</button>
                            </div>
                        </div>
                    </form>
                </div><!--fin del div card body-->
            </div>
        </div>
    </div>
</section>            


@section('scripts')
    <script>
        $("#numCompra-section").hide();

        $('#class_document').change(function(){
            var valorCambiado =$(this).val();
            console.log(valorCambiado);
            if(valorCambiado == '2'){
                $("#numCompra-section").show();
            }else{
                $("#numCompra-section").hide();
            }
        });

        $("#id_producto").change(mostrarValores);

        function mostrarValores(){
            datosProducto = document.getElementById('id_producto').value.split('_');
            $("#stock").val(datosProducto[3]);
            $("#precio_venta").val(datosProducto[2]);
            $("#costo").val(datosProducto[1]);
            $("#total_dinero").val(datosProducto[1]);
        }

        $(document).ready(function(){
            $("#agregar").click(function(){
                agregar();
            });
        });

        var cont=0;
        total=0;
        exentot=0;
        percepciont=0;
        subtex=[];
        subtpe=[];
        subtotal=[];
        
        $("#guardar").hide();

        function agregar(){
            datosProducto = document.getElementById('id_producto').value.split('_');

            id_producto= datosProducto[0];
            qty_producto = $("#cantidad").val();
            qty=datosProducto[1];
            total_dinero=datosProducto[1];
            producto = $("#id_producto option:selected").text();
            cantidad = $("#cantidad").val();
            precio_compra= $("#costo").val();
            exento= $("#exento").val();
            percepcion= $("#percepcion").val();
            impuesto=13;

            if(id_producto !="" && cantidad!="" && cantidad>0 && precio_compra!=""){

                if(percepcion !=""){
                    subtpe[cont]=0.00+percepcion;
                    percepciont=percepciont+subtpe[cont];
                }

                subtotal[cont]=cantidad*precio_compra;
                subtex[cont]=cantidad*exento;
                exentot=exentot+subtex[cont];
                total= total+subtotal[cont];

                var fila= '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="ibtnDel btn btn-md btn-danger" onclick="eliminar('+cont+');">{{trans("file.delete")}}</button></td> <td><input type="hidden" name="id_producto[]" value="'+id_producto+'">'+producto+'</td> <td><input type="number" disabled id="precio_compra[]" name="precio_compra[]"  value="'+precio_compra+'"> </td>  <td><input type="hidden" name="qty_producto[]" value="'+qty_producto+'">'+cantidad+'</td> <td><input type="hidden" name="total_dinero[]" value="'+subtotal[cont]+'"> $'+subtotal[cont]+' </td></tr>';
                cont++;
                limpiar();
                totales();
                evaluar();
                $('#detalles').append(fila);
            }else{
                // alert("Rellene todos los campos del detalle de la compra, revise los datos del producto");
                Swal.fire({
                    type: 'error',
                    //title: 'Oops...',
                    text: 'Rellene todos los campos del efectivo de corte de caja',
                })
            }
        }

        function limpiar(){
            $("#cantidad").val("");
            $("#precio_compra").val("");
            $("#exento").val("");
        }

        function totales(){
            var taxexento= $("#type_tax").val();

            $("#exentot").html("USD$ " + exentot.toFixed(2));
            sub_total=total;
            if( taxexento == 0){
               total_impuesto=total*impuesto/100;
            } else{
                total_impuesto=total*0/100;
            }
            total_percepcion=percepciont*100/100;
            total_pagar=total+total_impuesto+exentot+total_percepcion;
            total_exento=exentot;
            $("#percepciont").html("USD$ " + sub_total.toFixed(2));
            $("#total").html("USD$ " + total.toFixed(2));
            $("#total_impuesto").html("USD$ " + total_impuesto.toFixed(2));
            $("#total_pagar_html").html("USD$ " + total_pagar.toFixed(2));
            $("#total_pagar").val(total_pagar.toFixed(2));
            $("#total_exento").val(total_exento.toFixed(2));
            $("#total_percepcion").html("USD$ " + total_percepcion.toFixed(2));
        }

        function evaluar(){
            if(total>0){
                $("#guardar").show();
            } else{
                $("#guardar").hide();
            }
        }

        function eliminar(index){

            total=total-subtotal[index];
            total_impuesto= total*20/100;
            exentot=0.00
            total_exento=0.00
            percepciont=0.00
            total_pagar_html = total + total_impuesto;

            $("#total").html("USD$" + total);
            $("#exentot").html("USD$" + exentot);
            $("#percepciont").html("USD$" + percepciont);
            $("#total_exento").html("USD$" + total_exento);
            $("#total_impuesto").html("USD$" + total_impuesto);
            $("#total_pagar_html").html("USD$" + total_pagar_html);
            $("#total_pagar").val(total_pagar_html.toFixed(2));

            $("#fila" + index).remove();
            evaluar();
        }
    </script>
@endsection
@endsection('content')