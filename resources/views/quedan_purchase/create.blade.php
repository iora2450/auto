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
                        <h4>Creacion quedan Compra</h4>
                    </div>
                    <div class="card-body">                        
                        {!! Form::open(['route' => 'quedan_purchase.store', 'method' => 'post', 'files' => false]) !!}
                            <div class="row">
                                <div class="col-md-8">
                                    {{-- <p class="italic"><small>{{trans('file.Supplier')}}</small></p> --}}
                                    <div class="form-group">
                                        <label>{{trans('file.Supplier')}}</label>
                                        <select required class="form-control selectpicker" id="supplier_id" name="supplier_id" onchange='saveValue(this);'>
                                            <option value="">Selecciona un proveedor</option>
                                            @foreach($lims_client_list  as $supplier_group)
                                                @if($supplier_id == $supplier_group->id)
                                                    <option value="{{$supplier_group->id}}" selected>{{$supplier_group->name}}</option>
                                                @endif
                                                @if($supplier_id != $supplier_group->id)
                                                    <option value="{{$supplier_group->id}}" >{{$supplier_group->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Date quedan')}}*</strong> </label>
                                        <input type="date" id="date_quedan" name="date_quedan" required class="form-control" >
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{trans('file.Documents of quedan')}}</label>
                                        <select id='documentos' name='number_invoice[]' class="selectpicker form-control" data-live-search="true" multiple style='width: 100%'>
                                            @foreach($datos_facturas as $fact)
                                                <option data-monto="{{$fact->grand_total}}" value="{{$fact->id}}">{{$fact->invoice}} - {{$fact->name}} - ${{$fact->grand_total}}</option>
                                            @endforeach
                                        </select>
                                        ,
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha de vencimiento *</label>
                                        <input type="date" name="due_date" id="due_date" required class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total</label>
                                        <input type="number" name="totalvt" id="totalvt" class="form-control" step="any">
                                    </div>
                                </div> 

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>{{trans('file.Warehouse')}} *</label>
                                        <select required name="warehouse_id" id="warehouse_id" class="selectpicker form-control" data-live-search="true" data-live-search-style="begins" title="Select warehouse...">
                                            @foreach($lims_warehouse_list as $warehouse)
                                                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>    

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{trans('file.Documents of quedan')}}</label>
                                        <select id='notas' name='number_nc[]' class="selectpicker form-control" data-live-search="true" multiple style='width: 100%'>
                                            @foreach($datos_nc as $nc)
                                                <option data-monto="{{$nc->grand_total}}" value="{{$nc->id}}">{{$nc->invoice}} - {{$nc->name}} - ${{$nc->grand_total}}</option>
                                            @endforeach
                                        </select>
                                        ,
                                    </div>
                                </div> 

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total NC</label>
                                        <input type="number" name="totalnc" id="totalnc" class="form-control" step="any">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Total</label>
                                        <input type="number" name="total" id="total" class="form-control" step="any">
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

    $("#supplier_id").change(function(e){
        supplier_id = $("#supplier_id").val()
        url= "<?php echo url('');?> "
        url= url.trim();
        window.location.href = url+"/quedan_purchase/create/"+supplier_id;
    })

    $("#documentos").change(function(event){
        var options = $(this).find('option:selected').map(function() {
            return $(this).data('monto');
        }).get();

        total=0
        for (i = 0; i < options.length; ++i) {
            total+=options[i];
        }
        $("#totalvt").val(total)
        $("#total").val(total)

        //var total_vt = parseFloat($('#totalvt').text());
        //var total_nc = parseFloat($('#totalnc').text());

        //order_tax = (total_vt - total_nc);

        //$('#total').val(order_tax.toFixed(2));
    })

    $("#notas").change(function(event){
        var options = $(this).find('option:selected').map(function() {
            return $(this).data('monto');
        }).get();

        total= parseFloat($('#totalvt').val());
        for (i = 0; i < options.length; ++i) {
            total-=options[i];
        }
        //$("#totalnc").val(total).toFixed(2)
        $("#total").val(total)
    })

    $("ul#people").siblings('a').attr('aria-expanded','true');
    $("ul#people").addClass("show");
    $("ul#people #supplier-create-menu").addClass("active");

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